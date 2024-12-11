<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Research;

class ResearchCreated extends Notification
{
    use Queueable;

    private $research;
    private $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Research $research, $type)
    {
        $this->research = $research;
        $this->type = $type;
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
        $subject = $this->type === 'admin'? 'Запрос на проведение исследования' : 'Отправлен запрос на проведение исследования';

        return (new MailMessage)->markdown('mail.research_created', ['research' => $this->research, 'type' => $this->type, 'subject' => $subject])->subject($subject);
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
