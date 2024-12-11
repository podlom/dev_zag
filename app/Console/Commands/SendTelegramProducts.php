<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Notification;
use App\Models\BackpackUser;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\TelegramProductNotification;

class SendTelegramProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send products to telegram channel';

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
        $start = Carbon::now();
        $this->info("Command started at: " . $start->toDateTimeString());

        $admin = BackpackUser::whereHas('roles', function (Builder $query) {
            $query->where('name', 'admin');
        })->first();

        // @ts send products in Ukrainian language
        $noty = Notification::select('notifications.*')->
            distinct('notifications.id')->
            join('products', 'products.id', '=', 'notifications.product_id')->
            where('products.is_active', 1)->
            where('products.language_abbr', 'uk')->
            where('notifications.in_telegram', 0)->
            orderBy('notifications.created_at', 'asc')->
            first();

        if (!$noty) {
            $this->info("No product notifications found to send.");
            return;
        }

        if ($noty->product->is_active) {
            $admin->notify(new TelegramProductNotification($noty->product, $noty));
        }

        $end = Carbon::now();
        $this->info("Command ended at: " . $end->toDateTimeString());
    }
}
