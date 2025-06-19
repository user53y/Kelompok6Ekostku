<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\WhatsAppNotification;
use App\Notifications\Channels\WhatsAppChannel;

class PembayaranPendingNotification extends Notification
{
    use Queueable, WhatsAppNotification;

    protected $penghuni;
    protected $type;

    public function __construct($penghuni, $type = 'pending')
    {
        $this->penghuni = $penghuni;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        // Hanya kirim ke database jika status pending (untuk pemilik)
        $channels = [WhatsAppChannel::class];
        if ($this->type === 'pending') {
            $channels[] = 'database';
        }
        return $channels;
    }

    public function toWhatsapp($notifiable)
    {
        $messages = [
            'pending' => "💳 *Notifikasi Pembayaran Baru E-KOST Bu Tik*\n" .
                         "----------------------------------------\n\n" .
                         "Ada pembayaran baru dari penghuni atas nama *{$this->penghuni->nama_lengkap}* (Kamar: *{$this->penghuni->datakamar->no_kamar}*).\n\n" .
                         "Silakan cek dashboard untuk melakukan verifikasi pembayaran.\n\n" .
                         "Terima kasih.",

            'late' => "⚠️ *Pengingat Pembayaran E-KOST Bu Tik*\n" .
                      "----------------------------------------\n\n" .
                      "👋 Halo {$this->penghuni->user->username},\n\n" .
                      "Kami ingin mengingatkan bahwa pembayaran sewa kamar nomor *{$this->penghuni->datakamar->no_kamar}* untuk periode *{$this->penghuni->periode_mulai}* telah melewati batas waktu. ⏰❗\n\n" .
                      "Mohon segera melakukan pembayaran untuk menghindari denda keterlambatan. 💳⚠️\n\n" .
                      "Apabila Anda sudah melakukan pembayaran, silakan abaikan pesan ini atau konfirmasi ke pengelola kost. 📲\n\n" .
                      "Terima kasih atas perhatian dan kerjasamanya. 🙏",

            'approved' => "✅ *Konfirmasi Pembayaran E-KOST Bu Tik*\n" .
                          "----------------------------------------\n\n" .
                          "👋 Halo {$this->penghuni->user->username},\n\n" .
                          "Pembayaran sewa kamar nomor *{$this->penghuni->datakamar->no_kamar}* telah berhasil diverifikasi dan disetujui. 🎉✅\n\n" .
                          "Terima kasih atas ketepatan pembayaran Anda. Semoga Anda nyaman dan betah tinggal di kost Bu Tik. 🏡✨\n\n" .
                          "Jika ada kebutuhan lain atau pertanyaan, silakan hubungi pengelola kost. 🤝📞\n\n" .
                          "Salam hangat dan sukses selalu. 🙏"
        ];

        // Penentuan nomor tujuan WhatsApp
        if ($this->type === 'pending') {
            // Kirim ke pemilik (User), ambil dari $notifiable->no_telepon
            $phone = $notifiable->no_telepon;
        } else {
            // Kirim ke penghuni (User), ambil dari $this->penghuni->user->no_telepon
            $phone = $this->penghuni->user->no_telepon ?? null;
        }

        if (empty($phone)) {
            \Log::error('Nomor HP WhatsApp tidak ditemukan', [
                'penghuni_id' => $this->penghuni->id,
                'user_id' => $this->penghuni->user->id ?? null,
                'type' => $this->type
            ]);
            return false;
        }

        $result = $this->sendWhatsAppMessage($phone, $messages[$this->type]);
        if ($result) {
            \Log::info('WhatsApp notification sent', [
                'type' => $this->type,
                'to' => $phone,
                'penghuni_id' => $this->penghuni->id,
                'user_id' => $this->penghuni->user->id ?? null
            ]);
        } else {
            \Log::error('WhatsApp notification failed to send', [
                'type' => $this->type,
                'to' => $phone,
                'penghuni_id' => $this->penghuni->id,
                'user_id' => $this->penghuni->user->id ?? null
            ]);
        }
        return $result;
    }

    public function toArray($notifiable)
    {
        // Hanya untuk status pending, agar tidak double notifikasi
        if ($this->type !== 'pending') {
            return [];
        }
        $latestTagihan = $this->penghuni->tagihan->last();
        return [
            'message' => "Ada pembayaran baru dari {$this->penghuni->nama_lengkap} yang membutuhkan konfirmasi",
            'payment_id' => $latestTagihan->id ?? null,
            'type' => 'payment_pending',
            'bukti_pembayaran' => $latestTagihan->bukti_pembayaran ?? null,
        ];
    }
}
