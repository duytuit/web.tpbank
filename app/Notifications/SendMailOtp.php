<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendMailOtp extends Notification
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;

     protected $otp;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     */
    public function __construct($user, $otp)
    {
        $this->user = $user;
         $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Send password resetted mail after successful password reset
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject(trans('notification.send_otp') . ' | ' . config('config.company_name'))
            ->greeting(trans('notification.hello') . $this->user->profile->first_name)
            ->line(trans('notification.send_otp_success') . $this->otp)
            ->line(trans('notification.thankyou'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
