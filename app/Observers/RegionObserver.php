<?php

namespace App\Observers;
use App\Region;

class RegionObserver
{
    public function creating(Region $region)
    {
        if($region->original)
            $region->region_id = $region->original->region_id;
        else
            $region->region_id = Region::max('region_id') + 1;
    }

    public function deleted(Region $region)
    {
        $region->translations()->delete();
    }

    public function saving(Region $region){
        if($region->original) {
            $region->is_active = $region->original->is_active;
        }

        if($region->translations->count()) {
            $region->translations()->update([
                'is_active' => $region->is_active,
            ]);
        }
    }
}
