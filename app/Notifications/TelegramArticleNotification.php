<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramFile;

class TelegramArticleNotification extends Notification
{
    use Queueable;

    private $article;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($article)
    {
        $this->article = $article;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */

    public function toTelegram($notifiable)
    {
        // \App::setLocale('ru');
        \App::setLocale('uk'); // @ts set Ukrainian locale

        // $chat_id = -1001375219595;
        // $chat_id = -1001479721487; // test
        // $chat_id = 833654351; // me
        // $chat_id = -1001375219595; // zagorodna
        $chat_id = -1002155697214; // dev Zagorodna
        // todo: @ts change $chat_id before deploy to prod

        $this->article->update(['in_telegram' => 1]);

        return TelegramFile::create()
        ->to($chat_id)
        ->content("#Новини,статті,аналітика[\r\n" . $this->article->title . '](' . $this->article->link . ")\r\n" . $this->article->short_desc)
        ->file($this->article->telegram_img, 'photo'); // local photo

        // OR using a helper method with or without a remote file.
        // ->photo('https://file-examples.com/wp-content/uploads/2017/10/file_example_JPG_1MB.jpg');
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
