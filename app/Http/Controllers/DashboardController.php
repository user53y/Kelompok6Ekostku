<?php

namespace App\Http\Controllers;

use App\Models\Datakamar;
use App\Models\Datapenghuni;
use App\Models\Tagihan;
use App\Models\Datapemasukan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $kamarTersedia = Datakamar::where('status', 'Tersedia')->count();
        $belumLunas = Tagihan::where('status_tagihan', 'Belum Lunas')->count();
        $totalPemasukan = Datapemasukan::sum('jumlah_pembayaran');
        $penghuni = Datapenghuni::count(); // Changed: Count all penghuni instead of just active ones

        // Get recent penghuni with proper relationships
        $recentPenghuni = Datapenghuni::with(['user', 'datakamar'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($penghuni) {
                return [
                    'name' => $penghuni->nama_lengkap,
                    'kamar' => optional($penghuni->datakamar)->no_kamar ?? 'N/A',
                    'date' => $penghuni->created_at
                ];
            });

        // Get upcoming due dates with proper relationships
        $upcomingDueDates = Tagihan::with(['penghuni.datakamar'])
            ->where('jatuh_tempo', '>', Carbon::now())
            ->where('jatuh_tempo', '<=', Carbon::now()->addDays(7))
            ->orderBy('jatuh_tempo')
            ->take(5)
            ->get();

        return view('dashboard.pemilik', compact('penghuni',
            'kamarTersedia',
            'belumLunas',
            'totalPemasukan',
            'penghuni',
            'recentPenghuni',
            'upcomingDueDates'
        ));
    }

    public function penghuniDashboard()
    {
        // Get the logged in user's penghuni data with kamar relation
        $penghuni = auth()->user()->datapenghuni ?? null;
        $kamar = null;

        if(request()->routeIs('kamar-tersedia')) {
            $kamar = Datakamar::where('status', 'Tersedia')->get();
        }

        return view('dashboard.penghuni', compact('penghuni', 'kamar'));
    }
}
