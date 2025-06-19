@extends('layouts.dashboard')
@section('content')
@section('title', 'Verifikasi Pembayaran')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Pembayaran Menunggu Verifikasi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Penghuni</th>
                            <th>Kamar</th>
                            <th>Tanggal Upload</th>
                            <th>Bukti Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingPayments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $payment->user->name }}</td>
                                <td>{{ $payment->datakamar->no_kamar }}</td>
                                <td>{{ $payment->updated_at->format('d M Y H:i') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info"
                                            onclick="viewProof('{{ asset('images/payments/'.$payment->bukti_pembayaran) }}')">
                                        <i class="bi bi-eye"></i> Lihat Bukti
                                    </button>
                                </td>
                                <td>
                                    <form action="{{ route('approve-payment', $payment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Konfirmasi pembayaran ini?')">
                                            <i class="bi bi-check-circle"></i> Setujui
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-info-circle text-info"></i>
                                    Tidak ada pembayaran yang perlu diverifikasi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="proofModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="" class="img-fluid" id="proofImage">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewProof(imageUrl) {
    $('#proofImage').attr('src', imageUrl);
    $('#proofModal').modal('show');
}
</script>
@endpush
@endsection
