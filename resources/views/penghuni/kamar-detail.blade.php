@extends('dashboard.penghuni')
@section('title', 'Detail Kamar')

@section('content')
<div class="container mt-4">
    <h2>Detail Kamar - No. {{ $penghuni->datakamar->no_kamar }}</h2>
    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Tipe:</strong> {{ $penghuni->datakamar->tipe }}</p>
            <p><strong>Luas:</strong> {{ $penghuni->datakamar->luas }}</p>
            <p><strong>Lantai:</strong> {{ $penghuni->datakamar->lantai }}</p>
            <p><strong>Kapasitas:</strong> {{ $penghuni->datakamar->kapasitas }} orang</p>
            <p><strong>Harga Bulanan:</strong> Rp {{ number_format($penghuni->datakamar->harga_bulanan, 0, ',', '.') }}</p>
        </div>
    </div>
    <h4>Riwayat Menghuni</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Periode Mulai</th>
                <th>Tanggal Keluar</th>
                <th>Status Hunian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penghuni->riwayat as $riwayat)
            <tr>
                <td>{{ \Carbon\Carbon::parse($riwayat->periode_mulai)->format('d M Y') }}</td>
                <td>
                    @if($riwayat->tanggal_keluar)
                        {{ \Carbon\Carbon::parse($riwayat->tanggal_keluar)->format('d M Y') }}
                    @else
                        <!-- kosong jika belum keluar -->
                    @endif
                </td>
                <td>{{ $riwayat->status_hunian }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('dashboard.penghuni') }}" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
</div>
@endsection
