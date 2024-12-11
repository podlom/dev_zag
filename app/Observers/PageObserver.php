<?php

namespace App\Observers;
use Backpack\PageManager\app\Models\Page;

class PageObserver
{
    public function created(Page $page) {
        \Artisan::call("optimize");
    }

    public function updated(Page $page) {
        \Artisan::call("optimize");
    }

    public function deleted(Page $page) {
        $page->translations()->delete();
        \Artisan::call("optimize");
    }
}
