<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentProofUploaded extends Notification
{
    use Queueable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->data['message'],
            'payment_id' => $this->data['payment_id'],
            'penghuni_name' => $this->data['penghuni_name'],
            'amount' => $this->data['amount'],
            'type' => 'payment_proof'
        ];
    }
}
