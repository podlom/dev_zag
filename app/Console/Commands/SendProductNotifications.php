<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\ProductNotification;
use App\Models\Subscription;

use App\Notification;
use Aimix\Shop\app\Models\Product;

class SendProductNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send products updates to subscribers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $subscriptions = Subscription::where('adding', 1)->orWhere('status', 1)->orWhere('price', 1)->get();
        foreach($subscriptions as $subscription) {
            $notifications = Notification::where('created_at', '<', now())->where('created_at', '>', now()->subDays(7));

            if($subscription->region) {
                $region = $subscription->region;
                $notifications = $notifications->whereHas('product', function($query) use ($region) {
                    $query->where('address->administrative', $region);
                });
            }

            
            $notifications = $notifications->where(function($q) use ($subscription) {
                $first = true;
                if($subscription->adding) {
                    $first = false;
                    $q->where('type', 'new');
                }

                
                if($subscription->status) {
                    if($first)
                        $q->where('type', 'old')->whereRaw('notifications.old_status != notifications.status');
                    else {
                        $q->orWhere(function($q2) {
                            $q2->where('type', 'old')->whereRaw('notifications.old_status != notifications.status');
                        });
                    }
                    $first = false;
                }

                if($subscription->price) {
                    if($first)
                        $q->where('type', 'old')->whereRaw('notifications.old_price != notifications.price');
                    else {
                        $q->orWhere(function($q3) {
                            $q3->where('type', 'old')->whereRaw('notifications.old_price != notifications.price');
                        });
                    }
                }
            });

            $notifications = $notifications->whereIn('product_id', Product::get()->whereIn('type', $subscription->types)->pluck('id', 'id'))->get();

            if($subscription->latlng) {
                foreach($notifications as $key => $item) {
                    $distance = calculateTheDistance($subscription->latlng['lat'], $subscription->latlng['lng'], $item->product->address['latlng']['lat'], $item->product->address['latlng']['lng']);

                    if($distance > $subscription->radius * 1000)
                        $notifications->forget($key);
                }
            }


            if($notifications->count())
                $subscription->notify(new ProductNotification($notifications));
        }
    }
}
