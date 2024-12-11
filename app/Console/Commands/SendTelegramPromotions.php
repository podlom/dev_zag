<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Aimix\Promotion\app\Models\Promotion;
use App\Models\BackpackUser;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\TelegramPromotionNotification;

class SendTelegramPromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:promotions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send promotions to telegram channel';

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

        // @ts send promotions in Ukrainian language
        $promotion = Promotion::where('language_abbr', 'uk')->
        where('in_telegram', 0)->
        first();

        if (!$promotion) {
            $this->info("No promotions found to send.");
        } else {
            $admin->notify(new TelegramPromotionNotification($promotion));
            $this->info("Promotion sent successfully.");
        }

        $end = Carbon::now();
        $this->info("Command ended at: " . $end->toDateTimeString());
    }
}
