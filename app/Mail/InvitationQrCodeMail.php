<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Invitation;
use Illuminate\Mail\Mailables\Address;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvitationQrCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $qrCode;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        
        // Generate QR Code content (same logic as CheckinController)
        $qrContent = route('checkin.store', ['token' => $invitation->token]);
        
        // Generate QR Code as PNG string
        // Explicitly casting to string because QrCode::generate() might return Illuminate\Support\HtmlString
        $this->qrCode = (string) QrCode::format('png')
                        ->size(300)
                        ->margin(1)
                        ->generate($qrContent);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu QR Code para ' . $this->invitation->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation_qrcode',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Ensure $this->qrCode is a string, SimpleSoftwareIO\QrCode might return an HtmlString object depending on usage, 
        // but ->format('png')->generate() should return string.
        // However, if it returns HtmlString, we cast it.
        $qrCodeData = (string) $this->qrCode;

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(fn () => $qrCodeData, 'qrcode.png')
                ->withMime('image/png'),
        ];
    }
}
