<?php

namespace App\Observers;
use Backpack\MenuCRUD\app\Models\MenuItem;

class MenuItemObserver
{
    public function deleted(MenuItem $item)
    {
        $item->translations()->delete();
    }

    public function saving(MenuItem $item){
        if($item->original) {
            if($item->original->parent)
                $item->parent_id = $item->original->parent->translations->where('language_abbr', 'uk')->first()->id;
                
            $item->lft = $item->original->lft;
            $item->rgt = $item->original->rgt;
            $item->depth = $item->original->depth;
            // $item->link = $item->original->link;
            $item->type = $item->original->type;
        }

        if($item->translations->count()) {
            $item->translations()->update([
                'parent_id' => $item->translations->where('language_abbr', 'uk')->first()->parent->id,
                'lft' => $item->lft,
                'rgt' => $item->rgt,
                'depth' => $item->depth,
                // 'link' => $item->link,
                'type' => $item->type,
            ]);
        }
    }
}
