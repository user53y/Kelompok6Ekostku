<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Datapenghuni;
use App\Services\TagihanService;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentProofUploaded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Console\Commands\GenerateMonthlyTagihan;
use Illuminate\Support\Facades\Artisan;

class TagihanController extends Controller
{
    protected $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function index()
    {
        // Jalankan command setiap kali halaman index tagihan diakses
        Artisan::call('tagihan:generate-monthly');

        $tagihan = Tagihan::with(['penghuni.datakamar'])->get();

        // Get only penghuni with Menghuni status
        $penghuni = Datapenghuni::with('datakamar')
            ->where('status_hunian', 'Menghuni')
            ->whereHas('datakamar')
            ->whereDoesntHave('tagihan', function($query) {
                $query->whereIn('status_tagihan', ['Belum Lunas', 'Menunggu Konfirmasi']);
            })
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($item) {
                return (object)[
                    'id' => $item->id,
                    'nama_lengkap' => $item->nama_lengkap,
                    'datakamar' => $item->datakamar
                ];
            });

        return view('pemilik.tagihan.index', compact('tagihan', 'penghuni'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'id_penghuni' => 'required|exists:datapenghuni,id',
                'periode' => 'required|string|date_format:Y-m',
            ]);

            $penghuni = Datapenghuni::with('datakamar')->findOrFail($validated['id_penghuni']);

            // Convert periode from Y-m to Month Year format
            $periodeDate = Carbon::createFromFormat('Y-m', $validated['periode']);
            $periodeLengkap = $periodeDate->format('F Y');

            // Calculate prorated amount
            $jumlahTagihan = $this->tagihanService->hitungTagihanBulanan(
                Carbon::parse($penghuni->tanggal_masuk),
                $penghuni->tanggal_keluar ? Carbon::parse($penghuni->tanggal_keluar) : null,
                $penghuni->datakamar->harga_bulanan,
                $periodeLengkap
            );

            $tagihan = Tagihan::create([
                'id_penghuni' => $validated['id_penghuni'],
                'tanggal_masuk' => $penghuni->tanggal_masuk,
                'tanggal_keluar' => $penghuni->tanggal_keluar,
                'periode' => $periodeLengkap,
                'tanggal_tagihan' => now(),
                'jatuh_tempo' => now()->addDays(37), // Set to +37 days
                'jumlah_tagihan' => $jumlahTagihan,
                'status_tagihan' => 'Belum Lunas'
            ]);

            // Update penghuni status
            $penghuni->update([
                'status_hunian' => 'Menghuni'
            ]);

            DB::commit();

            // Tidak ada notifikasi ke penghuni di sini

            if($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tagihan berhasil dibuat',
                    'data' => $tagihan
                ]);
            }

            return redirect()->route('tagihan.index')
                            ->with('success', 'Tagihan berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat tagihan: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal membuat tagihan');
        }
    }

    public function show($id)
    {
        $tagihan = Tagihan::with(['penghuni.datakamar'])->findOrFail($id);

        if (request()->ajax()) {
            if (!$tagihan->penghuni) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penghuni not found for this tagihan'
                ], 404);
            }

            $denda = $tagihan->calculateDenda();
            $totalPembayaran = $tagihan->jumlah_tagihan + $denda;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $tagihan->id,
                    'id_penghuni' => $tagihan->id_penghuni,
                    'periode' => $tagihan->periode,
                    'tanggal_tagihan' => $tagihan->tanggal_tagihan,
                    'jatuh_tempo' => $tagihan->tanggal_masuk->addDays(37),
                    'jumlah_tagihan' => $tagihan->jumlah_tagihan,
                    'status_tagihan' => $tagihan->status_tagihan,
                    'denda' => $denda,
                    'total_pembayaran' => $totalPembayaran,
                    'penghuni' => [
                        'nama_lengkap' => $tagihan->penghuni->nama_lengkap,
                        'kamar' => [
                            'no_kamar' => $tagihan->penghuni->datakamar->no_kamar ?? null
                        ]
                    ]
                ]
            ]);
        }

        return view('pemilik.tagihan.show', compact('tagihan'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $tagihan = Tagihan::findOrFail($id);

            $validated = $request->validate([
                'id_penghuni' => 'required|exists:datapenghuni,id',
                'tanggal_tagihan' => 'required|date',
                'jatuh_tempo' => 'required|date|after:tanggal_tagihan',
                'jumlah_tagihan' => 'required|numeric|min:0',
                'periode' => 'required|string',
                'status_tagihan' => 'required|in:Lunas,Belum Lunas',
            ]);

            $tagihan->update($validated);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tagihan berhasil diperbarui'
                ]);
            }

            return redirect()->route('tagihan.index')
                            ->with('success', 'Tagihan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui tagihan'
                ], 500);
            }
            return back()->with('error', 'Gagal memperbarui tagihan');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $tagihan = Tagihan::findOrFail($id);
            $tagihan->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tagihan berhasil dihapus'
                ]);
            }

            return redirect()->route('tagihan.index')
                            ->with('success', 'Tagihan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus tagihan'
                ], 500);
            }
            return back()->with('error', 'Gagal menghapus tagihan');
        }
    }

    public function bulkDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->ids;
            Tagihan::whereIn('id', $ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tagihan'
            ], 500);
        }
    }

    public function calculate($id, $periode)
    {
        try {
            $penghuni = Datapenghuni::with('datakamar')->findOrFail($id);
            $periodeDate = Carbon::createFromFormat('Y-m', $periode);
            $periodeLengkap = $periodeDate->format('F Y');

            $jumlahTagihan = $this->tagihanService->hitungTagihanBulanan(
                Carbon::parse($penghuni->tanggal_masuk),
                $penghuni->tanggal_keluar ? Carbon::parse($penghuni->tanggal_keluar) : null,
                $penghuni->datakamar->harga_bulanan,
                $periodeLengkap
            );

            return response()->json([
                'success' => true,
                'jumlah' => $jumlahTagihan,
                'jatuh_tempo' => now()->addDays(37)->format('d F Y') // Consistent +37 days
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $tagihan = Tagihan::findOrFail($id);
            $penghuni = $tagihan->penghuni;

            // Calculate late fee
            $denda = $tagihan->calculateDenda();
            $totalPembayaran = $tagihan->jumlah_tagihan + $denda;

            // Handle file upload
            $filename = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/payments'), $filename);
            }

            // Create data pemasukan langsung status Lunas
            $tagihan->datapemasukan()->create([
                'tanggal_pembayaran' => now(),
                'jumlah_pembayaran' => $totalPembayaran,
                'jenis_pembayaran' => 'Cash',
                'bukti_pembayaran' => $filename,
                'denda' => $denda,
                'status' => 'Lunas'
            ]);

            // Update tagihan status langsung Lunas
            $tagihan->update(['status_tagihan' => 'Lunas']);

            // Update penghuni status tetap Menghuni
            $penghuni->update(['status_hunian' => 'Menghuni']);

            // Tidak perlu notifikasi konfirmasi

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah dan status tagihan langsung Lunas',
                'data' => [
                    'denda' => $denda,
                    'total_pembayaran' => $totalPembayaran
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $periode = request('periode') ?? date('Y-m');

        // Get penghuni with Menghuni status only
        $penghuni = \App\Models\Datapenghuni::with('datakamar')
            ->where('status_hunian', 'Menghuni')
            ->whereHas('datakamar')
            ->whereDoesntHave('tagihan', function($q) use ($periode) {
                $q->whereRaw("DATE_FORMAT(periode, '%Y-%m') = ?", [$periode]);
            })
            ->get();

        return view('pemilik.tagihan.form', compact('penghuni'));
    }

    public function availablePenghuni(Request $request)
    {
        $periode = $request->input('periode') ?? date('Y-m');

        // Get penghuni with Menghuni status only
        $penghuni = \App\Models\Datapenghuni::with('datakamar')
            ->where('status_hunian', 'Menghuni')
            ->whereHas('datakamar')
            ->whereDoesntHave('tagihan', function($q) use ($periode) {
                $q->whereRaw("DATE_FORMAT(periode, '%Y-%m') = ?", [$periode]);
            })
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama_lengkap' => $item->nama_lengkap,
                    'no_kamar' => $item->datakamar->no_kamar ?? '-'
                ];
            })
            ->values();

        return response()->json(['penghuni' => $penghuni]);
    }

    public function pemberhentianSewa($id)
    {
        DB::beginTransaction();
        try {
            $tagihan = Tagihan::with('penghuni')->findOrFail($id);
            $penghuni = $tagihan->penghuni;

            // Cek status tagihan bulan ini
            if ($tagihan->status_tagihan !== 'Lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan bulan ini belum lunas. Mohon selesaikan pembayaran terlebih dahulu.'
                ], 400);
            }

            if ($penghuni) {
                // Hapus semua tagihan penghuni ini
                Tagihan::where('id_penghuni', $penghuni->id)->delete();
                // Hapus data penghuni
                $penghuni->delete();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
