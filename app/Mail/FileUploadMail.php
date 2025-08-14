<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FileUploadMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public function __construct($client)
    {
        $this->client = $client;
    }

    public function build()
    {
        return $this->subject('File Upload Notification')
                    ->view('emails.file_upload')
                    ->with([
                        'clientName' => $this->client->name,
                    ]);
    }
}
