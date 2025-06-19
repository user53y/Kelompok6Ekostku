<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Traits\WhatsAppNotification;
use App\Notifications\Channels\WhatsAppChannel;

class PengajuanBerhentiNotification extends Notification
{
    use Queueable, WhatsAppNotification;

    protected $penghuni;

    public function __construct($penghuni)
    {
        $this->penghuni = $penghuni;
    }

    public function via($notifiable)
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toWhatsapp($notifiable)
    {
        $message = "🚨 *Pengajuan Pemberhentian Sewa E-KOST Bu Tik*\n"
            . "----------------------------------------\n\n"
            . "Penghuni atas nama *{$this->penghuni->user->name}* (Kamar: *{$this->penghuni->datakamar->no_kamar}*)\n"
            . "telah mengajukan pemberhentian sewa.\n\n"
            . "Silakan cek dashboard untuk konfirmasi pengajuan ini.\n\n"
            . "Terima kasih.";

        $phone = $notifiable->no_hp ?? null;
        if (empty($phone)) {
            \Log::error('Nomor HP pemilik tidak ditemukan untuk notifikasi pemberhentian sewa');
            return false;
        }
        return $this->sendWhatsAppMessage($phone, $message);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Penghuni {$this->penghuni->user->name} (Kamar: {$this->penghuni->datakamar->no_kamar}) mengajukan pemberhentian sewa.",
            'penghuni_id' => $this->penghuni->id,
            'type' => 'pengajuan_berhenti'
        ];
    }
}
