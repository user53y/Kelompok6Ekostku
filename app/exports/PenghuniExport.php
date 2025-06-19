<?php

namespace App\Exports;

use App\Models\Datapenghuni;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenghuniExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Ambil data penghuni beserta user dan kamar
        return Datapenghuni::with(['user', 'datakamar'])->get()->map(function ($item, $key) {
            return [
                'No' => $key + 1,
                'Nama Lengkap' => $item->nama_lengkap,
                'Username' => $item->user->username ?? '',
                'No. Telepon' => $item->no_telepon,
                'Pekerjaan' => $item->pekerjaan,
                'Tanggal Masuk' => $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') : '-',
                'No. Kamar' => $item->datakamar->no_kamar ?? '',
                'Status Hunian' => $item->status_hunian,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'Username',
            'No. Telepon',
            'Pekerjaan',
            'Tanggal Masuk',
            'No. Kamar',
            'Status Hunian',
        ];
    }
}
