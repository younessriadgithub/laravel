<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
class MailVerifyEmailNotification extends Notification
{
    use Queueable;
    protected $token;
    public function __construct($token) {
     $this->token = $token;
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

      //  $link = url( "/password/reset/?token=" . $this->token );
        $link =  "http://localhost:4200/active/" . $this->token ;

        return ( new MailMessage )
           ->view('mail.verify', compact('link'))
           ->from('younessriadme@gmail.com')
           ->subject( 'Active Your Account' )
           ->line( 'Thank you!' );




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
