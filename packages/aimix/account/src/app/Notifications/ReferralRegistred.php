<?php

namespace Aimix\Account\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralRegistred extends Notification
{
    use Queueable;

    private $usermeta;
    private $level;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($value, $lvl)
    {
        $this->usermeta = $value;
        $this->level = $lvl;
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
        return (new MailMessage)->markdown('mail.referral_registred', ['usermeta' => $this->usermeta])->subject('New level ' . $this->level . ' referral');
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
