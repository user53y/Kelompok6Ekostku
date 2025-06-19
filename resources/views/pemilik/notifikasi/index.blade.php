@extends('dashboard.pemilik')
@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid p-0">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bell me-2"></i>
                        Notifikasi
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

                    @if($notifications->isEmpty())
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            Tidak ada notifikasi baru.
                        </div>
                    @else
                        @foreach($notifications as $notif)
                            <div class="alert alert-info d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $notif->data['message'] ?? '' }}
                                </div>
                                <button class="btn btn-sm btn-outline-secondary mark-as-read-btn" data-id="{{ $notif->id }}">
                                    Tandai Sudah Dibaca
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('.mark-as-read-btn').click(function() {
    var id = $(this).data('id');
    $.post('/notifications/' + id + '/mark-as-read', {_token: '{{ csrf_token() }}'}, function() {
        location.reload();
    });
});
</script>
@endpush
@endsection
