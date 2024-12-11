<?php

namespace App\Observers;
use App\Models\Meta;

class MetaObserver
{
    public function saving(Meta $meta){
        if($meta->original) {
            $meta->address = $meta->original->address;
            $meta->type = $meta->original->type;
            $meta->status = $meta->original->status;
            $meta->is_map = $meta->original->is_map;
        }

        if($meta->translations->count()) {
            $meta->translations()->update([
                'address' => $meta->address,
                'type' => $meta->type,
                'status' => $meta->status,
                'is_map' => $meta->is_map,
            ]);
        }
      }

    public function deleted(Meta $meta)
    {
        $meta->translations()->delete();
    }
}
