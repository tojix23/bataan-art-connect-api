<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Verified extends Mailable
{
    use SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function __construct($user)
    {

        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to Our Application')
            ->view('emails.verifie');
    }
}
