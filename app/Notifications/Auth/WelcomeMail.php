<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeMail extends Notification
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
     * Welcome Mail for new users.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */


    public function toMail($user)
    {
        //TODO set proper url
        $url = config('app.client_url');

        $userName = $user->name;

        return (new MailMessage)
            ->subject('Le damos la bienvenida a AilynMS')
            ->line('Bienvenido, '.$userName.'!')
            ->line('Le damos las gracias por registrarse en nuestro sitio AilymMS.')
            ->line('Lo invitamos a crear sus primeras verificaciones.')
            ->action('Presione aquí', $url);
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
