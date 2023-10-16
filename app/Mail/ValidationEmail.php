<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ValidationEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $token;
    private string $name;

    /**
     * Create a new message instance.
     */
    public function __construct($token , $name)
    {
        $this->name = $name;
        $this->token = $token;
    }

    
    /**
     * Build the message.
     */
    public function build(){
        return $this->view('mails.validation', [
            'token' => $this->token , 
            'name' => $this->name
        ]);
    }
}
