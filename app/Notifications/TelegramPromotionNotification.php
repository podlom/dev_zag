<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramFile;

class TelegramPromotionNotification extends Notification
{
    use Queueable;

    private $promotion;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($promotion)
    {
        $this->promotion = $promotion;
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

        $this->promotion->update(['in_telegram' => 1]);

        $post = $this->promotion->image? TelegramFile::create()->file($this->promotion->telegram_img, 'photo') : TelegramFile::create()->file(url('files/net-fot_230x230.jpg'), 'photo');
        $post = $post->to($chat_id);

        $content = "#Акції";
        $content .= "\r\n🔥" . $this->promotion->title;
        $content .= "\r\n▫️[" . $this->promotion->product->name . '](' . $this->promotion->product->link . '/promotions' . ')';

        if($this->promotion->start !== $this->promotion->end) {
            $content .= "\r\n▫️" . \Carbon\Carbon::createFromTimeStamp(strtotime($this->promotion->start))->format('d.m.Y') . ' - ' . \Carbon\Carbon::createFromTimeStamp(strtotime($this->promotion->end))->format('d.m.Y');
        }

        $content = $this->promotion->product->extras_translatable['address_string'] && $this->promotion->product->city? $content . "\r\n▫️" . $this->promotion->product->city . ', ' . $this->promotion->product->extras_translatable['address_string']  : ($this->promotion->product->city? $content . "\r\n▫️" . $this->promotion->product->city : $content);

        if($this->promotion->desc) {
            $content .= "\r\n" . str_replace(['&sup2;', '&nbsp;', '&laquo;', '&raquo;'], ['.кв.', ' ', '"', '"'], strip_tags($this->promotion->desc));
        }

        return $post
        ->content($content);
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
