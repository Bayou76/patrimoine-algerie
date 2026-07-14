<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Mailtrap\Transport\MailtrapApiTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Laravel ne connaît pas nativement le transport "mailtrap" (le package
        // symfony/mailtrap-mailer n'est qu'un pont Symfony, sans intégration
        // Laravel automatique) : on l'enregistre nous-mêmes ici pour que
        // config/mail.php puisse utiliser 'transport' => 'mailtrap'.
        Mail::extend('mailtrap', function (array $config) {
            return new MailtrapApiTransport($config['key']);
        });
    }
}
