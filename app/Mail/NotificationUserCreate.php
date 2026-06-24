<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationUserCreate extends Mailable
{
      use Queueable, SerializesModels;

    public $distressCall;

    public function __construct()
    {
     
    }

    public function build()
    {
        return $this->view('mails.UserNotify');
    }
}
