<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCode extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $isMobile;

    /**
     * Create a new message instance.
     */
    public function __construct($code, $isMobile = false)
    {
        $this->code = $code;
        $this->isMobile = $isMobile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isMobile ? 'Mobil Uygulama Giriş Doğrulama Kodunuz' : 'Giriş Doğrulama Kodunuz'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.two_factor_code',
        );
    }
}
