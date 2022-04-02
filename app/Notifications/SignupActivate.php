<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SignupActivate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = "http://examplewinery.local/api/auth/signup/activate/".$notifiable->activation_token;
        return (new MailMessage)
            ->from($address = 'support@examplewinery.it', $name = 'Example Winery')
            ->subject('Conferma il tuo account')
            ->greeting("Ciao ".ucwords($notifiable->first_name).",")
            ->line('Grazie per esserti iscritto su '.env('APP_NAME'))
            ->line("Per completare la registrazione devi confermare l'indirizzo di posta tramite il pulsante sottostante")
            ->action('Completa la Registrazione', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
