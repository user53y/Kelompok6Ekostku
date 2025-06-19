<?php

namespace App\Exports;

use App\Models\Datakamar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KamarExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithEvents,
    ShouldAutoSize,
    WithCustomStartCell
{
    protected $rowNumber = 0;

    public function collection()
    {
        return Datakamar::all();
    }

    public function headings(): array
    {
        return [
            'No.',
            'No. Kamar',
            'Tipe',
            'Luas',
            'Lantai',
            'Kapasitas',
            'Harga Bulanan',
            'Status',
        ];
    }

    public function map($kamar): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $kamar->no_kamar,
            $kamar->tipe,
            $kamar->luas,
            $kamar->lantai,
            $kamar->kapasitas . ' orang',
            'Rp ' . number_format($kamar->harga_bulanan, 0, ',', '.'),
            $kamar->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Header bagian atas (judul, alamat, dll)
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');
                $sheet->mergeCells('A3:H3');
                $sheet->mergeCells('A4:H4');
                $sheet->mergeCells('A5:H5');

                $sheet->setCellValue('A1', 'KOST PUTRI BU TIK');
                $sheet->setCellValue('A2', 'Jl. Margo Tani, Sukorame, Kec. Mojoroto, Kota Kediri, Jawa Timur 64114');
                $sheet->setCellValue('A3', 'Telp: 085815320313 | Website: indiekost.mif-project.com');
                $sheet->setCellValue('A4', 'LAPORAN DATA KAMAR');
                $sheet->setCellValue('A5', 'Tanggal Cetak: ' . now()->format('d M Y'));

                // Styling header
                $sheet->getStyle('A1:H5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Style header kolom (baris 7)
                $sheet->getStyle('A7:H7')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'E9ECEF']
                    ],
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Dapatkan baris terakhir dari data
                $lastRow = $sheet->getHighestRow();

                // Border seluruh tabel
                $sheet->getStyle('A7:H' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Penyesuaian per kolom
                $sheet->getStyle('A8:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
                $sheet->getStyle('B8:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No Kamar
                $sheet->getStyle('E8:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Lantai
                $sheet->getStyle('F8:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Kapasitas
                $sheet->getStyle('H8:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status
                $sheet->getStyle('G8:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);  // Harga

                // Tinggi baris default
                $sheet->getDefaultRowDimension()->setRowHeight(25);
            },
        ];
    }
}
