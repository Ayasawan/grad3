<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// use SadiqSalau\LaravelOtp\Contracts\OtpInterface as Otp;
use App\Models\User;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Ichtrojan\Otp\Models\Otp;
use Ichtrojan\Otp\Otp;


class EmailVerificationNotification extends Notification
{
    use Queueable ;
    public $message;
    public $subject;
    public $fromEmail;
    public $mailer;
    public $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->message = 'Use the below code for verification process';
        $this->subject = 'Verification Needed' ;
        $this->fromEmail = 'abeerasidah@gmail.com' ;
        $this->mailer = 'smtp' ;
        $this->otp = new Otp ;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $otp = $this->otp->generate($notifiable->email,'numeric',6,60);
        return (new MailMessage)
                    ->mailer('smtp')
                    ->subject($this->subject)
                    ->greeting('Hello '.$notifiable->first_name)
                    ->line($this->message)
                    ->line('code: '.$otp->token);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
