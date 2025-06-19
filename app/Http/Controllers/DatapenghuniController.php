<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Datapenghuni;
use App\Models\Datakamar;
use App\Models\Tagihan;
use App\Services\TagihanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\PenghuniExport;
use Maatwebsite\Excel\Facades\Excel;

class DatapenghuniController extends Controller
{
    protected $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function index()
    {
        $penghuni = Datapenghuni::with(['user:id,username', 'datakamar:id,no_kamar'])->get();
        $users = User::select('id', 'username')
                    ->where('role', 'penghuni')
                    ->whereNotIn('id', function($query) {
                        $query->select('id_user')
                              ->from('datapenghuni')
                              ->where('status_hunian', 'Menghuni');
                    })
                    ->get();
        $kamar = Datakamar::select('id', 'no_kamar')
                         ->where('status', 'Tersedia')
                         ->get();

        return view('pemilik.datapenghuni.index', compact('penghuni', 'users', 'kamar'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'id_user' => 'required|exists:users,id',
                'id_datakamar' => 'required|exists:datakamar,id',
                'nama_lengkap' => 'required|string|max:255',
                'nik' => 'required|string|max:16',
                'alamat' => 'required|string',
                'no_telepon' => 'required|string|max:15',
                'pekerjaan' => 'required|string|max:100',
                'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status_hunian' => 'required|in:Menghuni,Tidak Menghuni',
            ]);

            // Set tanggal_masuk to current date
            $validated['tanggal_masuk'] = now();

            if ($request->hasFile('foto_ktp')) {
                $file = $request->file('foto_ktp');
                $filename = 'ktp_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Create directory if it doesn't exist (Windows-friendly)
                $path = public_path('images/ktp');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);

                    // Set Windows permissions to allow full access
                    $this->setWindowsPermissions($path);
                }

                $file->move($path, $filename);
                $validated['foto_ktp'] = $filename;
            }

            // Create penghuni
            $penghuni = Datapenghuni::create($validated);

            // Update kamar status
            Datakamar::where('id', $validated['id_datakamar'])
                     ->update(['status' => 'Disewa']);

            DB::commit();

            if($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data penghuni berhasil ditambahkan'
                ]);
            }

            return redirect()->route('datapenghuni.index')
                            ->with('success', 'Data penghuni berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan data penghuni'
                ], 500);
            }
            return back()->with('error', 'Gagal menambahkan data penghuni');
        }
    }

    public function show($id)
    {
        $penghuni = Datapenghuni::with(['user', 'datakamar'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $penghuni->id,
                    'id_user' => $penghuni->id_user,
                    'id_datakamar' => $penghuni->id_datakamar,
                    'nama_lengkap' => $penghuni->nama_lengkap,
                    'nik' => $penghuni->nik,
                    'alamat' => $penghuni->alamat,
                    'no_telepon' => $penghuni->no_telepon,
                    'pekerjaan' => $penghuni->pekerjaan,
                    'tanggal_masuk' => $penghuni->tanggal_masuk,
                    'status_hunian' => $penghuni->status_hunian,
                    'foto_ktp' => $penghuni->foto_ktp,
                    'foto_ktp_url' => $penghuni->foto_ktp ? asset('images/ktp/' . basename($penghuni->foto_ktp)) : null,
                    'datakamar' => $penghuni->datakamar ? [
                        'id' => $penghuni->datakamar->id,
                        'no_kamar' => $penghuni->datakamar->no_kamar
                    ] : null,
                    'user' => [
                        'id' => $penghuni->user->id,
                        'username' => $penghuni->user->username,
                        'avatar' => $penghuni->user->avatar ?? null
                    ]
                ]
            ]);
        }

        return view('pemilik.datapenghuni.edit', compact('penghuni'));
    }

    public function edit($id)
    {
        $datapenghuni = Datapenghuni::with(['user', 'datakamar'])->findOrFail($id);
        $users = User::where('role', 'penghuni')->get(); // Get all users for edit
        $kamar = Datakamar::all(); // Get all rooms for edit

        return view('pemilik.datapenghuni.edit', [
            'datapenghuni' => $datapenghuni,
            'users' => $users,
            'kamar' => $kamar
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'username')
                    ->where('role', 'penghuni')
                    ->whereNotIn('id', function($query) {
                        $query->select('id_user')
                              ->from('datapenghuni')
                              ->where('status_hunian', 'Menghuni');
                    })
                    ->get();
        $kamar = Datakamar::select('id', 'no_kamar')
                         ->where('status', 'Tersedia')
                         ->get();

        return view('pemilik.datapenghuni.tambah', compact('users', 'kamar'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $penghuni = Datapenghuni::findOrFail($id);

            $validated = $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'nik' => 'required|string|max:16',
                'alamat' => 'required|string',
                'no_telepon' => 'required|string|max:15',
                'pekerjaan' => 'required|string|max:100',
                'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status_hunian' => 'required|in:Menghuni,Tidak Menghuni',
                // Jangan validasi id_user, id_datakamar, tanggal_masuk
            ]);

            // Jangan update id_user, id_datakamar, tanggal_masuk
            // Update previous kamar status to Tersedia jika status_hunian berubah ke Tidak Menghuni
            // atau jika status_hunian tetap Menghuni, tidak perlu update kamar

            // Jika status_hunian berubah dari Menghuni ke Tidak Menghuni, update kamar ke Tersedia
            if (
                $penghuni->status_hunian === 'Menghuni'
                && $validated['status_hunian'] === 'Tidak Menghuni'
            ) {
                Datakamar::where('id', $penghuni->id_datakamar)
                    ->update(['status' => 'Tersedia']);
            }

            if ($request->hasFile('foto_ktp')) {
                // Delete old photo if exists
                if ($penghuni->foto_ktp) {
                    $oldPath = public_path('images/ktp/' . $penghuni->foto_ktp);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $file = $request->file('foto_ktp');
                $filename = 'ktp_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/ktp'), $filename);
                $validated['foto_ktp'] = $filename; // Save only filename
            }

            $penghuni->update($validated);

            // Jika status_hunian berubah dari Tidak Menghuni ke Menghuni, update kamar ke Disewa
            if (
                $penghuni->status_hunian === 'Tidak Menghuni'
                && $validated['status_hunian'] === 'Menghuni'
            ) {
                Datakamar::where('id', $penghuni->id_datakamar)
                    ->update(['status' => 'Disewa']);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data penghuni berhasil diperbarui'
                ]);
            }

            return redirect()->route('datapenghuni.index')
                            ->with('success', 'Data penghuni berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui data penghuni'
                ], 500);
            }
            return back()->with('error', 'Gagal memperbarui data penghuni');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $penghuni = Datapenghuni::findOrFail($id);

            // Update kamar status back to Tersedia
            Datakamar::where('id', $penghuni->id_datakamar)
                     ->update(['status' => 'Tersedia']);

            // Delete foto_ktp if exists
            if ($penghuni->foto_ktp) {
                $path = public_path('images/' . $penghuni->foto_ktp);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $penghuni->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data penghuni berhasil dihapus'
                ]);
            }

            return redirect()->route('datapenghuni.index')
                            ->with('success', 'Data penghuni berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data penghuni'
                ], 500);
            }
            return back()->with('error', 'Gagal menghapus data penghuni');
        }
    }

    public function bulkDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->ids;
            if (!is_array($ids) || empty($ids)) {
                throw new \Exception('Invalid input');
            }

            $penghuni = Datapenghuni::whereIn('id', $ids)->with('datakamar')->get();

            foreach ($penghuni as $item) {
                // Update kamar status to Tersedia
                if ($item->datakamar) {
                    $item->datakamar->update(['status' => 'Tersedia']);
                }

                // Delete foto_ktp if exists
                if ($item->foto_ktp) {
                    $path = public_path('images/' . $item->foto_ktp);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }

                $item->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data penghuni berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data penghuni'
            ], 500);
        }
    }

    public function bookRoom(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'id_user' => 'required|exists:users,id',
                'id_datakamar' => 'required|exists:datakamar,id',
                'nama_lengkap' => 'required|string|max:255',
                'nik' => 'required|string|max:16',
                'alamat' => 'required|string',
                'no_telepon' => 'required|string|max:15',
                'pekerjaan' => 'required|string|max:100',
                'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'status_hunian' => 'required|in:Menghuni,Tidak Menghuni',
            ]);

            // Check if room is still available
            $kamar = Datakamar::find($validated['id_datakamar']);
            if ($kamar->status !== 'Tersedia') {
                throw new \Exception('Maaf, kamar ini sudah tidak tersedia');
            }

            // Set additional data
            $validated['tanggal_masuk'] = now();
            $validated['status_pembayaran'] = 'Belum Bayar';

            // Handle KTP upload
            if ($request->hasFile('foto_ktp')) {
                $file = $request->file('foto_ktp');
                $filename = 'ktp_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/ktp'), $filename);
                $validated['foto_ktp'] = $filename;
            }

            // Create penghuni record
            $penghuni = Datapenghuni::create($validated);

            // Update room status to Pending
            $kamar->update(['status' => 'Pending']);

            // Create initial tagihan
            $currentMonth = Carbon::now();
            $periodeLengkap = $currentMonth->format('F Y');

            // Calculate prorated amount
            $jumlahTagihan = $this->tagihanService->hitungTagihanBulanan(
                Carbon::parse($penghuni->tanggal_masuk),
                null,
                $kamar->harga_bulanan,
                $periodeLengkap
            );

            // Create tagihan record
            Tagihan::create([
                'id_penghuni' => $penghuni->id,
                'tanggal_masuk' => $penghuni->tanggal_masuk,
                'tanggal_keluar' => null,
                'periode' => $periodeLengkap,
                'tanggal_tagihan' => now(),
                'jatuh_tempo' => now()->addDays(7), // Due in 7 days
                'jumlah_tagihan' => $jumlahTagihan,
                'status_tagihan' => 'Belum Lunas'
            ]);

            DB::commit();

            // Langsung return redirect tanpa response JSON
            return redirect()->route('cek-pembayaran');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memesan kamar: ' . $e->getMessage());
        }
    }

    public function excel()
    {
        return Excel::download(new PenghuniExport, 'data-penghuni-' . date('Y-m-d') . '.xlsx');
    }


    public function pdf(Request $request)
    {
        $data = Datapenghuni::with(['user', 'datakamar']);
        if ($request->has('status') && $request->status !== 'all') {
            $data->where('status_hunian', $request->status);
        }
        $penghuni = $data->get(); // Execute the query to get the results
        return view('pemilik.datapenghuni.pdf', ['penghuni' => $penghuni]);
    }

    // Add this helper method to your controller
    private function setWindowsPermissions($path)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            // Get current user
            $user = get_current_user();

            // Use icacls to set permissions (Windows command)
            exec('icacls "' . $path . '" /grant "' . $user . '":(OI)(CI)F /T');
            exec('icacls "' . $path . '" /grant "IUSR":(OI)(CI)F /T');
            exec('icacls "' . $path . '" /grant "IIS_IUSRS":(OI)(CI)F /T');
            exec('icacls "' . $path . '" /grant "Everyone":(OI)(CI)F /T');
        }
    }
}
