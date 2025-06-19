<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    public function send($notifiable, Notification $notification)
    {
        return $notification->toWhatsapp($notifiable);
    }
}
