<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FolderAssignedMail extends Mailable
{
    use Queueable, SerializesModels;
        public $clientUser;
    public function __construct($clientUser)
    {
        $this->clientUser = $clientUser;
    }

    public function build()
    {
        return $this->subject('New Folder Assigned Access Available')
                    ->view('emails.folder_assigned')
                    ->with([
                        'clientName' => $this->clientUser->name,
                    ]);
    }
}
