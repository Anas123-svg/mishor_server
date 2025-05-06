<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $plainPassword;

    public function __construct($client, $plainPassword)
    {
        $this->client = $client;
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        return $this->subject('Welcome to Portal Drive')
                    ->view('emails.client_welcome');
    }
}
