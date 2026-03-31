<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeTenantMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $plainPassword,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Welcome to the Platform, {$this->user->name}!",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenants.welcome', 
        );
    }

    public function attachments(): array
    {
        $path = storage_path('app/public/onboarding_manual.pdf');

        
        if (file_exists($path)) {
            return [
                Attachment::fromPath($path)
                    ->as('Onboarding_Guide.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}