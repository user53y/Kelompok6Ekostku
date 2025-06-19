<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - {{ $bulan ? date('F', mktime(0, 0, 0, $bulan, 1)) : 'Tahun' }} {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 20px; font-weight: bold; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        .total-section { margin-top: 20px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 30px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">KOST PUTRI BU TIK</div>
        <div>Jl. Margo Tani, Sukorame, Kec. Mojoroto, Kota Kediri</div>
        <div>Jawa Timur 64114</div>
        <hr>
        <div class="title">LAPORAN KEUANGAN</div>
        <div>Periode: {{ $bulan ? date('F', mktime(0, 0, 0, $bulan, 1)) : 'Tahun' }} {{ $tahun }}</div>
    </div>

    <div>
        <h3>Pemasukan</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Penghuni</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pemasukan as $item)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($item->tanggal_pembayaran)) }}</td>
                    <td>{{ optional($item->tagihan)->penghuni->nama_lengkap ?? 'Data Penghuni Terhapus' }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah_pembayaran, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total Pemasukan</th>
                    <th class="text-right">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <h3>Pengeluaran</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengeluaran as $item)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($item->tanggal_pengeluaran)) }}</td>
                    <td>{{ optional($item->jenisPengeluaran)->nama_pengeluaran ?? 'Data Jenis Terhapus' }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah_pengeluaran, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total Pengeluaran</th>
                    <th class="text-right">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="summary">
            <table>
                <tr>
                    <th>Total Pemasukan</th>
                    <td class="text-right">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total Pengeluaran</th>
                    <td class="text-right">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Laba Bersih</th>
                    <td class="text-right">Rp {{ number_format($total_laba, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="no-print" style="margin-top: 20px;">
        <div class="d-flex justify-content-start">
            <button class="btn btn-dark me-2" onclick="window.print()">Cetak</button>
            <button class="btn btn-secondary" onclick="window.close()">Tutup</button>
        </div>
    </div>
</body>
</html>
