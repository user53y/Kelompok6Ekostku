<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Data Penghuni</title>
    <style type="text/css">
        .style1 {
            font-size: large;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn {
            display: block;
            margin-bottom: 20px;
        }
        @media print {
            .print-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="style1"><strong>KOST PUTRI BU TIK</strong></div>
        <div>Jl. Margo Tani, Sukorame, Kec. Mojoroto, Kota Kediri</div>
        <div>Jawa Timur 64114</div>
        <div>Telp: 085815320313 | Website: indiekost.mif-project.com</div>
        <hr>
        <h2 class="style1">LAPORAN DATA PENGHUNI</h2>
        <div>Tanggal Cetak: {{ now()->format('d M Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>No. Telepon</th>
                <th>Pekerjaan</th>
                <th>No. Kamar</th>
                <th>Tanggal Masuk</th>
                <th>Status Hunian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penghuni as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_lengkap }}</td>
                    <td>{{ $item->nik }}</td>
                    <td>{{ $item->no_telepon }}</td>
                    <td>{{ $item->pekerjaan }}</td>
                    <td>{{ optional($item->datakamar)->no_kamar }}</td>
                    <td>{{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $item->status_hunian }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div align="center" class="print-btn" style="display: block; margin-bottom: 20px;">
        <button id="cetak" onclick="printPage()" style="margin-right:10px;">Cetak</button>
        <button id="kembali" onclick="kembaliKeIndex()">Kembali</button>
    </div>

    <script>
        function printPage() {
            document.getElementById('cetak').style.display = 'none';
            document.getElementById('kembali').style.display = 'none';
            window.print();
            document.getElementById('cetak').style.display = 'inline';
            document.getElementById('kembali').style.display = 'inline';
        }

        function kembaliKeIndex() {
            window.location.href = '{{ route("datapenghuni.index") }}';
        }
    </script>
</body>
</html>
