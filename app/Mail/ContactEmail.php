<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->from('mussulo@siguangola.com', 'Administração do Mussulo')
            ->replyTo('mussulo@siguangola.com', 'Administração do Mussulo')
            ->subject($this->data['subject'])
            ->view('emails.contact')
            ->with(['data' => $this->data])
            ->priority(1);
    }
}