<?php

namespace App\Observers;
use App\Models\PollQuestion;
use Backpack\NewsCRUD\app\Models\Article;

class PollQuestionObserver
{
    public function created(PollQuestion $question)
    {
        $lang = $question->language_abbr;
        $title = $question->title;
        $article = new Article;
        $article->language_abbr = $lang;
        $article->poll_id = $question->id;
        $article->title = $question->id;
        $article->category_id = $lang == 'ru'? 371 : 372;
        $article->original_id = $lang == 'ru'? null : Article::where('category_id', 371)->where('poll_id', $question->original->id)->first()->id;
        $article->save();
    }
    public function deleted(PollQuestion $question)
    {
        Article::where('poll_id', $question->id)->delete();

        $question->translations()->delete();
    }

    public function saving(PollQuestion $question){
        if($question->original) {
            $question->is_active = $question->original->is_active;
            $question->is_multiple = $question->original->is_multiple;
            $question->type = $question->original->type;
        }

        if($question->translations->count()) {
            $question->translations()->update([
                'is_active' => $question->is_active,
                'is_multiple' => $question->is_multiple,
                'type' => $question->type,
            ]);
        }
    }
}
