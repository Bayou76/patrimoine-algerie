<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ResetPasswordEmail — envoyé par PasswordResetController::forgotPassword().
 *
 * $resetUrl pointe vers une page du FRONTEND (pas du backend), avec le token
 * et l'email en query params — c'est React qui affichera le formulaire de
 * nouveau mot de passe et appellera /api/reset-password.
 */
class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $resetUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réinitialise ton mot de passe Athar',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
        );
    }
}
