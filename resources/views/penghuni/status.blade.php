@extends('dashboard.penghuni')
@section('content')
@section('title', 'Cek Status Pemesanan')

<div class="container">
    <div class="content-wrapper">
        <!-- Gambar -->
        <div class="image-container">
            <img src="{{ asset('template/img/penghuni1.png') }}" alt="gambar">
        </div>
        <!-- Tabel -->
        <table class="table">
            <tr>
                <td>Nama</td>
                <td>{{ $penghuni->user->name ?? 'Data tidak tersedia' }}</td>
            </tr>
            <tr>
                <td>Kamar yang dipesan</td>
                <td>{{ $penghuni->datakamar->no_kamar ?? 'Belum melakukan pemesanan' }}</td>
            </tr>
            <tr>
                <td>Tanggal pemesanan</td>
                <td>{{ $penghuni->tanggal_masuk ?? 'Belum melakukan pemesanan' }}</td>
            </tr>
            <tr>
                <td>Status Pemesanan</td>
                <td>{{ $penghuni->status ?? 'Belum melakukan pemesanan' }}</td>
            </tr>
            <tr>
                <td>Status Pembayaran</td>
                <td>
                    @if($penghuni->status_pembayaran == 'Belum Lunas')
                        <span class="badge bg-warning">Belum Lunas</span>
                    @elseif($penghuni->status_pembayaran == 'Menunggu Konfirmasi')
                        <span class="badge bg-info">Menunggu Konfirmasi Pemilik</span>
                    @elseif($penghuni->status_pembayaran == 'Lunas')
                        <span class="badge bg-success">Lunas</span>
                    @endif
                </td>
            </tr>
            @if($penghuni->status_pembayaran == 'Belum Lunas')
            <tr>
                <td>Bukti Pembayaran</td>
                <td>
                    <form action="{{ route('upload-pembayaran', $penghuni->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Informasi Pembayaran:</h6>
                            <p>Transfer ke rekening berikut:</p>
                            <ul class="list-unstyled">
                                <li><strong>Bank BRI</strong></li>
                                <li>No. Rek: 1234-5678-9012-3456</li>
                                <li>A.N: Kos Bu Tik</li>
                            </ul>
                        </div>
                        <input type="file" class="form-control" name="bukti_pembayaran" required>
                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="bi bi-upload me-2"></i>Unggah Bukti Pembayaran
                        </button>
                    </form>
                </td>
            </tr>
            @elseif($penghuni->bukti_pembayaran && $penghuni->status_pembayaran == 'Menunggu Konfirmasi')
            <tr>
                <td>Bukti Pembayaran</td>
                <td>
                    <div class="uploaded-proof">
                        <img src="{{ asset('images/payments/' . $penghuni->bukti_pembayaran) }}"
                             alt="Bukti Pembayaran" class="img-fluid rounded mb-2" style="max-width: 200px;">
                        <div class="alert alert-warning">
                            <i class="bi bi-clock-history me-2"></i>
                            Bukti pembayaran sedang menunggu konfirmasi dari pemilik
                        </div>
                    </div>
                </td>
            </tr>
            @endif
        </table>
    </div>
</div>

<style>
.table td {
    padding: 1rem;
    vertical-align: middle;
}
.badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}
.uploaded-proof {
    border: 1px solid #dee2e6;
    padding: 1rem;
    border-radius: 0.5rem;
}
</style>
@endsection
