<?php

namespace App\Console\Commands;

use DB; // Add this import
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\BackpackUser;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\TelegramArticleNotification;
use Backpack\NewsCRUD\app\Models\Article;

class SendTelegramNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send news to telegram channel';

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
        // Listen for database queries
        DB::listen(function ($query) {
            $this->info('SQL: ' . $query->sql);
            $this->info('Bindings: ' . implode(', ', $query->bindings));
            $this->info('Time: ' . $query->time . 'ms');
        });

        $start = Carbon::now();
        $this->info("Command started at: " . $start->toDateTimeString());

        $admin = BackpackUser::whereHas('roles', function (Builder $query) {
            $query->where('name', 'admin');
        })->first();

        // @ts send articles in Ukrainian language
        $article = Article::select('articles.*')
            ->distinct('atricles.id')
            ->join('categories', 'categories.id', '=', 'articles.category_id')
            ->where(function ($query) {
                $news = [14, 26, 27, 28];
                $query->whereIn('categories.id', $news)->orWhereIn('categories.parent_id', $news);
            })
            ->where('in_telegram', 0)
            ->where('status', 'PUBLISHED')
            ->first();

        if (!$article) {
            $this->info("No news was found to send.");
            return;
        }

        $admin->notify(new TelegramArticleNotification($article));

        $end = Carbon::now();
        $this->info("Command ended at: " . $end->toDateTimeString());
    }
}
