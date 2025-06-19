<?php

namespace App\Http\Controllers;

use App\Models\Datapenghuni;
use App\Models\DataKamar;
use App\Models\Datapemasukan;
use App\Models\Tagihan; // Add this import
use App\Notifications\PembayaranPendingNotification; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\DatabaseNotification;

class AdminController extends Controller
{
    public function showPendingPayments()
    {
        $pendingPayments = Tagihan::with(['user', 'datakamar'])
            ->where('status_pembayaran', 'Menunggu Konfirmasi')
            ->get();

        return view('pemilik.pembayaran.pending', compact('pendingPayments'));
    }

    public function konfirmasiPembayaran($id)
    {
        $penghuni = Datapenghuni::with(['user', 'datakamar'])->findOrFail($id);
        return view('pemilik.pembayaran.konfirmasi', compact('penghuni'));
    }

    public function approvePayment(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            \Log::info('Attempting to approve payment', ['id' => $id]);

            $tagihan = Tagihan::with('penghuni')->findOrFail($id);
            \Log::info('Tagihan found', ['tagihan' => $tagihan]);

            $buktiPembayaran = $tagihan->bukti_pembayaran;

            if (!$buktiPembayaran) {
                throw new \Exception('Bukti pembayaran tidak ditemukan. Silakan upload ulang.');
            }

            // Simpan data ke pemasukan
            Datapemasukan::create([
                'id_tagihan' => $tagihan->id,
                'tanggal_pembayaran' => now(),
                'jumlah_pembayaran' => $tagihan->jumlah_tagihan,
                'jenis_pembayaran' => 'transfer',
                'bukti_pembayaran' => $buktiPembayaran,
            ]);

            // Update status tagihan
            $tagihan->status_tagihan = 'Lunas';
            $tagihan->save();

            // Tandai notifikasi sebagai dibaca
            $user = auth()->user();
            $user->unreadNotifications()
                ->where('data->payment_id', $id)
                ->where('data->type', 'payment_pending')
                ->update(['read_at' => now()]);

            DB::commit();

            \Log::info('Payment approved successfully');
            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil dikonfirmasi']);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Payment approval failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectPayment(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $tagihan = Tagihan::findOrFail($id);

            // Set status back to unpaid
            $tagihan->status_tagihan = 'Belum Lunas';
            $tagihan->save();

            // Notify penghuni
            $tagihan->penghuni->user->notify(new PaymentRejectedNotification($tagihan));

            // Setelah reject, tandai notifikasi terkait sebagai read
            $user = auth()->user();
            $user->unreadNotifications()
                ->where('data->payment_id', $id)
                ->where('data->type', 'payment_pending')
                ->update(['read_at' => now()]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveBerhenti(Request $request, $penghuni_id)
    {
        // ...proses approve pemberhentian...
        // Setelah approve, tandai notifikasi terkait sebagai read
        $user = auth()->user();
        $user->unreadNotifications()
            ->where('data->penghuni_id', $penghuni_id)
            ->where('data->type', 'pengajuan_berhenti')
            ->update(['read_at' => now()]);
        // ...existing code...
    }

    public function rejectBerhenti(Request $request, $penghuni_id)
    {
        // ...proses reject pemberhentian...
        // Setelah reject, tandai notifikasi terkait sebagai read
        $user = auth()->user();
        $user->unreadNotifications()
            ->where('data->penghuni_id', $penghuni_id)
            ->where('data->type', 'pengajuan_berhenti')
            ->update(['read_at' => now()]);
        // ...existing code...
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function showNotifications()
    {
        $notifications = auth()->user()->unreadNotifications;
        return view('notifications.index', compact('notifications'));
    }

    public function whatsappWebhook(Request $request)
    {
        try {
            // Log incoming webhook
            \Log::info('WhatsApp Webhook received:', $request->all());

            // Validate webhook signature if provided
            if ($request->header('X-Fonnte-Signature') !== config('services.fonnte.webhook_secret')) {
                throw new \Exception('Invalid webhook signature');
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('WhatsApp Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
