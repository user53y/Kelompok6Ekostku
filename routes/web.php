<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatakamarController;
use App\Http\Controllers\DatapenghuniController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\JenisPengeluaranController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/auth', function () {
    return view('auth.auth');
})->name('auth');

Route::get('/view-kamar/{id}', [DatakamarController::class, 'view'])->name('datakamar.view');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Halaman forgot password
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// Halaman reset password
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::get('/auth/{provider}', [\App\Http\Controllers\SocialiteController::class, 'redirect'])->name('social.login');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\SocialiteController::class, 'callback'])->name('social.callback');

// Protected Routes (Requires Authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard-pemilik', [DashboardController::class, 'index'])->name('dashboard.pemilik');
    Route::get('/dashboard-penghuni', [DashboardController::class, 'penghuniDashboard'])->name('dashboard.penghuni');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Pemilik/Admin Routes

    // Pemilik/Admin Routes
    Route::middleware(['pemilik'])->group(function () {
        // Data Kamar Routes
        Route::resource('datakamar', DatakamarController::class);
        Route::post('/datakamar/store', [DatakamarController::class, 'store'])->name('datakamar.store');
        Route::get('/tampil-kamar', [DatakamarController::class, 'index'])->name('tampil-kamar');
        Route::get('/datakamar/export/excel', [DatakamarController::class, 'excel'])->name('kamar.excel');
        Route::get('/datakamar/export/pdf', [DatakamarController::class, 'pdf'])->name('kamar.pdf');
        Route::post('/datakamar/bulk-delete', [DatakamarController::class, 'bulkDelete'])->name('datakamar.bulk-delete');

        // Data Penghuni Routes
        Route::resource('datapenghuni', DatapenghuniController::class);
        Route::post('/datapenghuni/store', [DatapenghuniController::class, 'store'])->name('datapenghuni.store');
        Route::get('/tampil-penghuni', [DatapenghuniController::class, 'index'])->name('tampil-penghuni');
        // Perbaiki nama route export Excel dan PDF
        Route::get('/datapenghuni/export/excel', [DatapenghuniController::class, 'excel'])->name('datapenghuni.export.excel');
        Route::get('/datapenghuni/export/pdf', [DatapenghuniController::class, 'pdf'])->name('datapenghuni.export.pdf');
        Route::post('/datapenghuni/bulk-delete', [DatapenghuniController::class, 'bulkDelete'])->name('datapenghuni.bulk-delete');
        Route::get('/datapenghuni/{id}', [DatapenghuniController::class, 'show'])->name('datapenghuni.show');

        // Data Pengeluaran Routes
        Route::resource('datapengeluaran', PengeluaranController::class);
        Route::post('/datapengeluaran/store', [PengeluaranController::class, 'store'])->name('datapengeluaran.store');
        Route::get('/tampil-pengeluaran', [PengeluaranController::class, 'index'])->name('tampil-pengeluaran');
        Route::post('/datapengeluaran/bulk-delete', [PengeluaranController::class, 'bulkDelete'])->name('datapengeluaran.bulk-delete');
        Route::post('jenis-pengeluaran', [PengeluaranController::class, 'storeJenis'])->name('jenis-pengeluaran.store');
        Route::delete('jenis-pengeluaran/{id}', [PengeluaranController::class, 'destroyJenis'])->name('jenis-pengeluaran.destroy');
        Route::get('/jenis-pengeluaran', [JenisPengeluaranController::class, 'index'])->name('jenis-pengeluaran.index');
        Route::post('/jenis-pengeluaran', [JenisPengeluaranController::class, 'store'])->name('jenis-pengeluaran.store');
        Route::delete('/jenis-pengeluaran/{id}', [JenisPengeluaranController::class, 'destroy'])->name('jenis-pengeluaran.destroy');

        // Data Pemasukan Routes
        Route::get('/tampil-pemasukan', [PemasukanController::class, 'index'])->name('tampil-pemasukan');
        Route::resource('datapemasukan', PemasukanController::class);
        Route::get('/datapemasukan/export/pdf', [PemasukanController::class, 'pdf'])->name('pemasukan.pdf');

        // Laporan Routes
        Route::get('/tampil-laporan', [LaporanController::class, 'index'])->name('tampil-laporan');
        Route::get('/laporan/data', [LaporanController::class, 'getData'])->name('laporan.data');
        Route::post('/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

        // Payment Approval Routes
        Route::post('/approve-payment/{id}', [AdminController::class, 'approvePayment'])->name('approve-payment');
        Route::get('/konfirmasi-pembayaran/{id}', [AdminController::class, 'konfirmasiPembayaran'])->name('konfirmasi-pembayaran');

        // Add route for pending payments
        Route::get('/pending-payments', [AdminController::class, 'showPendingPayments'])->name('pending-payments');

        // Tagihan Routes
        Route::resource('tagihan', TagihanController::class);
        Route::get('/tampil-tagihan', [TagihanController::class, 'index'])->name('tampil-tagihan');
        Route::post('/tagihan/bulk-delete', [TagihanController::class, 'bulkDelete'])->name('tagihan.bulk-delete');
        Route::get('/tagihan/calculate/{penghuni}/{periode}', [TagihanController::class, 'calculate'])->name('tagihan.calculate');
        Route::post('/tagihan/{id}/update-status', [TagihanController::class, 'updateStatus'])->name('tagihan.update-status');
        Route::get('/tagihan/available-penghuni', [\App\Http\Controllers\TagihanController::class, 'availablePenghuni'])->name('tagihan.available-penghuni');

        // Update pemberhentian sewa route to use KontrakController
        Route::post('/pemberhentian-sewa/{id}', [KontrakController::class, 'berhentikan'])->name('pemberhentian-sewa');
    });

    // Penghuni Routes
    Route::middleware(['penghuni'])->group(function () {
        // Batasi akses kamar-tersedia hanya untuk user yang belum jadi penghuni
        Route::match(['get', 'post'], '/kamar-tersedia', function() {
            $sudahPenghuni = \App\Models\Datapenghuni::where('id_user', auth()->id())
                ->whereIn('status_hunian', ['Menghuni', 'Pending'])
                ->exists();
            if ($sudahPenghuni) {
                return redirect()->route('cek-pembayaran')
                    ->with('info', 'Anda sudah memesan/menghuni kamar. Silakan cek detail pemesanan.');
            }
            // ...panggil controller jika belum jadi penghuni
            return app(\App\Http\Controllers\DatakamarController::class)->kamarTersedia(request());
        })->name('kamar-tersedia');

        Route::get('/datakamar/{id}', [DatakamarController::class, 'show'])->name('datakamar.show');
        Route::get('/view-kamar/{id}', [DatakamarController::class, 'view'])->name('datakamar.view');
        //kamar detail
        Route::get('/kamar-detail/{id}', [DatakamarController::class, 'kamarDetail'])->name('kamar-detail');

        // Pemesanan kamar oleh penghuni (form dan submit)
        Route::get('/penghuni/book/{id}', [PenghuniController::class, 'create'])->name('penghuni.book');
        Route::post('/penghuni', [PenghuniController::class, 'store'])->name('penghuni.store');

        // Route book-room untuk proses booking kamar (form submit)
        Route::post('/book-room', [PenghuniController::class, 'store'])->name('book-room');

        // Pembayaran Routes
        Route::get('/pembayaran', [PenghuniController::class, 'cekPembayaran'])->name('cek-pembayaran');
        Route::post('/upload-pembayaran/{id}', [PenghuniController::class, 'uploadPembayaran'])->name('upload-pembayaran');

        // Kontrak Routes
        Route::post('/perpanjang-sewa', [KontrakController::class, 'perpanjangSewa'])->name('perpanjang-sewa');
        Route::post('/pemberhentian-sewa', [KontrakController::class, 'ajukanBerhentiViaRequest'])->name('pemberhentian-sewa');
        Route::post('/konfirmasi-berhenti/{id}', [KontrakController::class, 'konfirmasiBerhenti'])->name('konfirmasi-berhenti');
        Route::post('/penghuni/{penghuni_id}/ajukan-berhenti', [KontrakController::class, 'ajukanBerhenti'])->name('penghuni.ajukan-berhenti');

        // Informasi
        Route::get('/informasi', function () {
            return view('penghuni.informasi');
        })->name('informasi-kost');
    });

    // Payment & Billing Routes
    Route::prefix('payments')->group(function() {
        Route::get('/check', [PaymentController::class, 'check'])->name('payment.check');
        Route::post('/process/{id}', [PaymentController::class, 'process'])->name('payment.process');
        Route::get('/history', [PaymentController::class, 'history'])->name('payment.history');
    });

    // Notification Routes - Group them together
    Route::prefix('notifications')->group(function () {
        Route::get('/', [AdminController::class, 'showNotifications'])->name('notifikasi');
        Route::post('/admin/approve-payment/{id}', [AdminController::class, 'approvePayment'])->name('admin.approve-payment');
        Route::post('/admin/reject-payment/{id}', [AdminController::class, 'rejectPayment'])->name('admin.reject-payment');
        Route::post('/{id}/mark-as-read', [AdminController::class, 'markAsRead'])->name('mark-as-read');
        // Add WhatsApp webhook route - not requiring auth
        Route::post('/whatsapp/webhook', [AdminController::class, 'whatsappWebhook'])
            ->name('whatsapp.webhook')
            ->withoutMiddleware(['auth']);
    });

    // Ubah definisi route approval pembayaran
    Route::post('/approve-payment/{id}', [AdminController::class, 'approvePayment'])->name('approve.payment');
    Route::post('/reject-payment/{id}', [AdminController::class, 'rejectPayment'])->name('reject.payment');
});
