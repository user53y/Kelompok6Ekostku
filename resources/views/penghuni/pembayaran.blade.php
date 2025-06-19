@extends('dashboard.penghuni')
@section('title', 'Pembayaran')

@section('content')
<div class="container-fluid p-0">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-credit-card me-2"></i>
                        Pembayaran Sewa Kamar
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Tambahkan alert jika status tagihan menunggu konfirmasi --}}
                    @if(isset($latestTagihan) && $latestTagihan->status_tagihan == 'Menunggu Konfirmasi')
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-clock-history me-2"></i>
                            Bukti pembayaran sudah dikirim dan sedang menunggu konfirmasi dari pemilik.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($penghuni)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">Detail Tagihan</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td>Nomor Kamar</td>
                                                <td>: {{ $penghuni->datakamar->no_kamar }}</td>
                                            </tr>
                                            <tr>
                                                <td>Periode</td>
                                                <td>: {{ now()->format('F Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Jatuh Tempo</td>
                                                <td>: {{ now()->addDays(37)->format('d F Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total Tagihan</td>
                                                <td>:
                                                    @php
                                                        $latestTagihan = $penghuni->tagihan ? $penghuni->tagihan->first() : null;
                                                    @endphp
                                                    @if($latestTagihan)
                                                        Rp {{ number_format($latestTagihan->jumlah_tagihan, 0, ',', '.') }}
                                                    @else
                                                        Rp 0
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status</td>
                                                <td>:
                                                    @if($penghuni->tagihan && $penghuni->tagihan->last())
                                                        @php $latestTagihan = $penghuni->tagihan->last(); @endphp
                                                        @if($latestTagihan->status_tagihan == 'Belum Lunas')
                                                            <span class="badge bg-warning">Belum Bayar</span>
                                                        @elseif($latestTagihan->status_tagihan == 'Menunggu Konfirmasi')
                                                            <span class="badge bg-info">Menunggu Konfirmasi</span>
                                                        @else
                                                            <span class="badge bg-success">Lunas</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-warning">Belum Ada Tagihan</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                @php
                                    $latestTagihan = $penghuni->tagihan->first();
                                @endphp
                                @if($latestTagihan && $latestTagihan->status_tagihan == 'Belum Lunas')
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3">Informasi Pembayaran</h6>
                                            <div class="alert alert-info">
                                                <h6><i class="bi bi-info-circle-fill me-2"></i>Metode Pembayaran:</h6>
                                                <p class="mb-2">Silakan lakukan transfer ke salah satu rekening berikut:</p>
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <div class="border rounded p-2 bg-light">
                                                            <strong class="d-block mb-1"><i class="bi bi-bank2 me-1"></i>Bank BRI</strong>
                                                            <span class="d-block">No. Rekening: <strong>1234-5678-9012-3456</strong></span>
                                                            <span class="d-block">Atas Nama: <strong>Kos Bu Tik</strong></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="border rounded p-2 bg-light">
                                                            <strong class="d-block mb-1"><i class="bi bi-bank2 me-1"></i>Bank BCA</strong>
                                                            <span class="d-block">No. Rekening: <strong>9876-5432-1098-7654</strong></span>
                                                            <span class="d-block">Atas Nama: <strong>Bu Tik</strong></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <small class="text-muted">
                                                    Setelah melakukan pembayaran, silakan upload bukti transfer pada form di bawah.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-3">Upload Bukti Pembayaran</h6>
                                            <form action="{{ route('upload-pembayaran', $penghuni->id) }}"
                                                  method="POST"
                                                  enctype="multipart/form-data"
                                                  id="paymentForm">
                                                @csrf
                                                <div class="mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Bukti Transfer</label>
                                                        <input type="file"
                                                               class="form-control @error('bukti_pembayaran') is-invalid @enderror"
                                                               name="bukti_pembayaran"
                                                               accept="image/*"
                                                               required>
                                                        @error('bukti_pembayaran')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="preview mt-2"></div>
                                                    </div>
                                                </div>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-upload me-2"></i>Upload Bukti Pembayaran
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @elseif($latestTagihan && $latestTagihan->status_tagihan == 'Menunggu Konfirmasi')
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <i class="bi bi-clock-history me-2"></i>
                                                Bukti pembayaran sedang menunggu konfirmasi dari pemilik
                                            </div>
                                            @if($latestTagihan->datapemasukan && $latestTagihan->datapemasukan->bukti_pembayaran)
                                                <img src="{{ asset('images/payments/'.$latestTagihan->datapemasukan->bukti_pembayaran) }}"
                                                     alt="Bukti Pembayaran"
                                                     class="img-fluid rounded mt-3">
                                            @endif
                                        </div>
                                    </div>
                                @elseif($latestTagihan && $latestTagihan->status_tagihan == 'Lunas')
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <div class="alert alert-success">
                                                <i class="bi bi-check-circle me-2"></i>
                                                Pembayaran untuk periode ini sudah lunas
                                            </div>
                                            @if($latestTagihan->datapemasukan && $latestTagihan->datapemasukan->bukti_pembayaran)
                                                <img src="{{ asset('images/payments/'.$latestTagihan->datapemasukan->bukti_pembayaran) }}"
                                                     alt="Bukti Pembayaran"
                                                     class="img-fluid rounded mt-3">
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-circle me-2"></i>
                                        Tidak ada tagihan aktif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">Tidak Ada Data Pemesanan</h4>
                            <p class="text-muted">Anda belum melakukan pemesanan kamar</p>
                            <a href="{{ route('kamar-tersedia') }}" class="btn btn-primary">
                                <i class="bi bi-house me-2"></i>Lihat Kamar Tersedia
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Preview uploaded image
    $('input[name="bukti_pembayaran"]').change(function(e) {
        const preview = $(this).siblings('.preview');
        preview.empty();

        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.html(`<img src="${e.target.result}" class="img-fluid rounded mt-2" style="max-height: 200px">`);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Loading state on form submit
    $('#paymentForm').on('submit', function() {
        $(this).find('button[type="submit"]').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...');
    });
});
</script>
@endpush
@endsection
