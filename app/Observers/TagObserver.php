<?php

namespace App\Observers;

use Backpack\NewsCRUD\app\Models\Tag;

class TagObserver
{
    public function deleted(Tag $tag)
    {
        $tag->articles()->detach();
        $tag->translations()->each(function($translation) {
            $translation->delete();
        });
    }

    public function created(Tag $tag)
    {
        if($tag->original && $tag->original->articles->count()) {
            foreach($tag->original->articles as $article) {
                if($article->translations()->where('language_abbr', $tag->language_abbr)->first())
                    $article->translations()->where('language_abbr', $tag->language_abbr)->first()->tags()->attach($tag);
            }
        }
    }
}
