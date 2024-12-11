<?php

namespace App\Observers;

use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Tag;

class ArticleObserver
{
    public function deleted(Article $article)
    {
        $article->translations()->each(function($translation) {
            $translation->delete();
        });
    }

    public function saving(Article $article){
        (new Article)->clearGlobalScopes();
        if($article->original) {
            $article->date = $article->original->date;
            $article->image = $article->original->image;
            $article->images = $article->original->images;
            $article->region = $article->original->region;
            $article->status = $article->original->status;
            $article->featured = $article->original->featured;
            $article->hide_from_index = $article->original->hide_from_index;
            $article->nofollow_links = $article->original->nofollow_links;
            $article->show_form = $article->original->show_form;
            $article->category_id = $article->original->category? $article->original->category->translations->where('language_abbr', $article->language_abbr)->first()->id : null;
        }

        if($article->translations->count()) {
            $article->translations()->update([
                'date' => $article->date,
                'image' => $article->image,
                'images' => $article->images,
                'region' => $article->region,
                'status' => $article->status,
                'featured' => $article->featured,
                'hide_from_index' => $article->hide_from_index,
                'nofollow_links' => $article->nofollow_links,
                'show_form' => $article->show_form,
                'category_id' => $article->category? ($article->category->translations->where('language_abbr', 'uk')->first()? $article->category->translations->where('language_abbr', 'uk')->first()->id : null) : null,
            ]);
        }

        if($article->date)
            $article->date = $article->date->addSecond();
        
    }
    public function saved(Article $article){
          $alert = false;

        if($article->original) {
            foreach($article->original->tags as $tag) {
                $article->tags()->sync($tag->translations->where('language_abbr', $article->language_abbr)->first());

                if(!$tag->translations->where('language_abbr', $article->language_abbr)->first())
                    $alert = true;
            }
        }

        if($article->translations->count()) {
            foreach($article->translations as $item) {

                $item->tags()->sync(Tag::whereHas('original', function($query) use ($article) {
                    $query->whereIn('id', $article->tags->pluck('id', 'id'));
                })->get());

                if($item->tags->count() != $article->tags->count())
                        $alert = true;

            }
        
        }

        if($alert)
            \Alert::warning('Одна или несколько меток не имеют перевода');

    }
}
