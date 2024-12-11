<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramFile;

class TelegramProductNotification extends Notification
{
    use Queueable;

    private $product;
    private $noty;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($product, $noty)
    {
        $this->product = $product;
        $this->noty = $noty;
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

        $this->noty->update(['in_telegram' => 1]);

        $post = $this->product->image? TelegramFile::create()->file($this->product->telegram_img, 'photo') : TelegramFile::create()->file(url('files/net-fot_230x230.jpg'), 'photo');
        $post = $post->to($chat_id);

        if($this->noty->type == 'new') {
            $content = "#Новий\_проект";
            $content .= "\r\n📣 Новый об'єкт [" . $this->product->name . '](' . $this->product->link . ')';
            $content = $this->noty->price? $content . "\r\n▫️" . 'Ціна: від ' . $this->noty->price  . ' грн/' . $this->product->area_unit: $content;

            if($this->product->address['region'] == 29)
                $content .= "\r\n▫️Київська область";
            elseif($this->product->region && $this->product->area && $this->product->area == $this->product->city)
                $content .= "\r\n▫️" . $this->product->region . ' область, ';
            elseif($this->product->region && $this->product->area && $this->product->area != $this->product->city)
                $content .= "\r\n▫️" . $this->product->region . ' область, ' . $this->product->area . ' район';

            $content = $this->product->extras_translatable['address_string'] && $this->product->city? $content . "\r\n▫️" . $this->product->city . ', ' . $this->product->extras_translatable['address_string']  : ($this->product->city? $content . "\r\n▫️" . $this->product->city : $content);
            $content = isset($this->product->extras_translatable['infrastructure']) && $this->product->extras_translatable['infrastructure']? $content . "\r\n▫️Інфраструктура: " . $this->product->extras_translatable['infrastructure'] : $content;
            $content = $this->product->extras['site']? $content . "\r\n👉🏻" .  '[Сайт](' . $this->product->extras['site'] . ')' : $content;

            return $post
            ->content($content);
        } else {
            $content = "#Зміни";
            $content = $this->product->category_id == 1 || $this->product->category_id == 6? $content . "\r\nЗміни в містечку " : $content . "\r\nЗміни в новобудові ";
            $content .= "[" . $this->product->name . "](" . $this->product->link . ")";
            if($this->noty->old_status) {
                $content .= "\r\n🏗" . $this->noty->old_status_string;
                $content = $this->noty->old_status != $this->noty->status? $content . ' ➨ ' . $this->noty->status_string : $content;
            } elseif($this->noty->status) {
                $content .= "\r\n🏗" . $this->noty->status_string;
            }

            if($this->noty->old_price) {
                $content = $content . "\r\n💰" . $this->noty->old_price . ' грн/' . $this->product->area_unit;
                $content = $this->noty->price && $this->noty->old_price != $this->noty->price? $content . ' ➨ ' . $this->noty->price . ' грн/' . $this->product->area_unit : $content;
            } elseif($this->noty->price) {
                $content = $content . "\r\n💰" . $this->noty->price . ' грн/' . $this->product->area_unit;
            }

            if($this->product->address['region'] == 29)
                $content .= "\r\n▫️Київська область";
            elseif($this->product->region && $this->product->area && $this->product->area == $this->product->city)
                $content .= "\r\n▫️" . $this->product->region . ' область, ';
            elseif($this->product->region && $this->product->area && $this->product->area != $this->product->city)
                $content .= "\r\n▫️" . $this->product->region . ' область, ' . $this->product->area . ' район';

            $content = $this->product->extras_translatable['address_string'] && $this->product->city? $content . "\r\n▫️" . $this->product->city . ', ' . $this->product->extras_translatable['address_string']  : ($this->product->city? $content . "\r\n▫️" . $this->product->city : $content);
            $content = isset($this->product->extras['site']) && $this->product->extras['site']? $content . "\r\n👉🏻" . '[Сайт](' . $this->product->extras['site'] . ')' : $content;

            return $post
            ->content($content);
        }
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
