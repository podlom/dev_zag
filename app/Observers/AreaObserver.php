<?php

namespace App\Observers;
use App\Area;

class AreaObserver
{
    public function creating(Area $area)
    {
        if($area->original)
            $area->area_id = $area->original->area_id;
        else
            $area->area_id = Area::max('area_id') + 1;
    }

    public function deleted(Area $area)
    {
        $area->translations()->delete();
    }

    public function saving(Area $area){
        if($area->original) {
            $area->region_id = $area->original->region_id;
        }

        if($area->translations->count()) {
            $area->translations()->update([
                'region_id' => $area->region_id,
            ]);
        }
    }
}
