<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Backpack\NewsCRUD\app\Models\Article;
use App\Notifications\NewsNotification;
use App\Models\Subscription;

class SendNewsNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send new articles to subscribers';

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
        $articles = Article::where('date', '<', now())->where('date', '>', now()->subDays(7))->get();

        if(count($articles)) {
            $subscriptions = Subscription::where('news', 1)->get();
            
            foreach($subscriptions as $subscription) {
                $subscription->notify(new NewsNotification($articles));
            }
        }
    }
}
