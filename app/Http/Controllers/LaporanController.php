<?php

namespace App\Http\Controllers;

use App\Models\Datapemasukan;
use App\Models\Datapengeluaran;
use App\Models\Datapenghuni;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        // Get unique years from both tables
        $pemasukanYears = Datapemasukan::selectRaw('YEAR(tanggal_pembayaran) as year')
            ->distinct()
            ->pluck('year');

        $pengeluaranYears = Datapengeluaran::selectRaw('YEAR(tanggal_pengeluaran) as year')
            ->distinct()
            ->pluck('year');

        $years = $pemasukanYears->concat($pengeluaranYears)
            ->unique()
            ->sort()
            ->reverse()
            ->values();

        // Get months that have transactions
        $pemasukanMonths = Datapemasukan::selectRaw('MONTH(tanggal_pembayaran) as month')
            ->distinct()
            ->pluck('month');

        $pengeluaranMonths = Datapengeluaran::selectRaw('MONTH(tanggal_pengeluaran) as month')
            ->distinct()
            ->pluck('month');

        $availableMonths = $pemasukanMonths->concat($pengeluaranMonths)
            ->unique()
            ->sort()
            ->values();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Filter months array to only include available months
        $months = collect($months)
            ->only($availableMonths)
            ->all();

        return view('pemilik.laporan.index', [
            'availableYears' => $years,
            'availableMonths' => $months
        ]);
    }

    public function getData(Request $request)
    {
        $year = $request->tahun ?? now()->year;
        $month = $request->bulan;

        // Get monthly data for pemasukan
        $pemasukan = Datapemasukan::query()
            ->whereYear('tanggal_pembayaran', $year)
            ->when($month, function($q) use ($month) {
                $q->whereMonth('tanggal_pembayaran', $month);
            })
            ->selectRaw('DATE(tanggal_pembayaran) as date, SUM(jumlah_pembayaran) as total')
            ->groupBy('date')
            ->get();

        // Get monthly data for pengeluaran
        $pengeluaran = Datapengeluaran::query()
            ->whereYear('tanggal_pengeluaran', $year)
            ->when($month, function($q) use ($month) {
                $q->whereMonth('tanggal_pengeluaran', $month);
            })
            ->selectRaw('DATE(tanggal_pengeluaran) as date, SUM(jumlah_pengeluaran) as total')
            ->groupBy('date')
            ->get();

        // Calculate totals
        $totalPemasukan = $pemasukan->sum('total') ?? 0;
        $totalPengeluaran = $pengeluaran->sum('total') ?? 0;

        // Prepare chart data
        if ($month) {
            // For monthly view, show daily data
            $daysInMonth = Carbon::create($year, $month)->daysInMonth;
            $labels = collect(range(1, $daysInMonth))->map(function($day) use ($year, $month) {
                return Carbon::create($year, $month, $day)->format('d M Y');
            });

            $chartData = [
                'labels' => $labels,
                'pemasukan' => array_fill(0, $daysInMonth, 0),
                'pengeluaran' => array_fill(0, $daysInMonth, 0)
            ];

            // Fill daily data
            foreach ($pemasukan as $data) {
                $day = Carbon::parse($data->date)->day - 1;
                $chartData['pemasukan'][$day] = floatval($data->total);
            }

            foreach ($pengeluaran as $data) {
                $day = Carbon::parse($data->date)->day - 1;
                $chartData['pengeluaran'][$day] = floatval($data->total);
            }
        } else {
            // For yearly view, show monthly data (keep existing code)
            $labels = collect(range(1, 12))->map(fn($m) => Carbon::create($year, $m)->format('F Y'));

            $chartData = [
                'labels' => $labels,
                'pemasukan' => array_fill(0, 12, 0),
                'pengeluaran' => array_fill(0, 12, 0)
            ];

            foreach ($pemasukan->groupBy(function($item) {
                return Carbon::parse($item->date)->month;
            }) as $month => $group) {
                $chartData['pemasukan'][$month - 1] = floatval($group->sum('total'));
            }

            foreach ($pengeluaran->groupBy(function($item) {
                return Carbon::parse($item->date)->month;
            }) as $month => $group) {
                $chartData['pengeluaran'][$month - 1] = floatval($group->sum('total'));
            }
        }

        return response()->json([
            'summary' => [
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'total_laba' => $totalPemasukan - $totalPengeluaran
            ],
            'chart' => $chartData
        ]);
    }

    public function cetak(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;

        // Get pemasukan data with nested relationships
        $pemasukan = Datapemasukan::query()
            ->with(['tagihan' => function($query) {
                $query->select('id', 'id_penghuni');
            }, 'tagihan.penghuni' => function($query) {
                $query->select('id', 'nama_lengkap');
            }])
            ->whereYear('tanggal_pembayaran', $tahun)
            ->when($bulan, function($q) use ($bulan) {
                $q->whereMonth('tanggal_pembayaran', $bulan);
            })
            ->select('id', 'id_tagihan', 'jumlah_pembayaran', 'tanggal_pembayaran')
            ->get();

        // Get pengeluaran data
        $pengeluaran = Datapengeluaran::query()
            ->with(['jenisPengeluaran'])
            ->whereYear('tanggal_pengeluaran', $tahun)
            ->when($bulan, function($q) use ($bulan) {
                $q->whereMonth('tanggal_pengeluaran', $bulan);
            })
            ->get();

        // Calculate totals using correct column names
        $total_pemasukan = $pemasukan->sum('jumlah_pembayaran');
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');
        $total_laba = $total_pemasukan - $total_pengeluaran;

        return view('pemilik.laporan.cetak', compact(
            'pemasukan',
            'pengeluaran',
            'total_pemasukan',
            'total_pengeluaran',
            'total_laba',
            'tahun',
            'bulan'
        ));
    }
}
