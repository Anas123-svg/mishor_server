<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientUserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $clientUser;
    public $plainPassword;

    public function __construct($clientUser, $plainPassword)
    {
        $this->clientUser = $clientUser;
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        return $this->subject('Welcome to Portal Drive')
                    ->view('emails.client_user_welcome');
    }
}
