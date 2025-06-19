@extends('layouts.dashboard')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0">
                        <i class="bi bi-credit-card-fill me-2"></i>
                        Konfirmasi Pembayaran
                    </h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Detail Penghuni</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-muted">Nama</td>
                                    <td>{{ $penghuni->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. Kamar</td>
                                    <td>{{ $penghuni->datakamar->no_kamar }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Periode</td>
                                    <td>{{ $penghuni->periode_mulai }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jumlah</td>
                                    <td>Rp {{ number_format($penghuni->datakamar->harga_bulanan, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Bukti Pembayaran</h6>
                            @if($penghuni->bukti_pembayaran)
                                <img src="{{ asset('images/payments/' . $penghuni->bukti_pembayaran) }}"
                                     alt="Bukti Pembayaran"
                                     class="img-fluid rounded">
                            @else
                                <p class="text-muted">Bukti pembayaran belum diunggah</p>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-light">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <form action="{{ route('approve-payment', $penghuni->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Konfirmasi Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
