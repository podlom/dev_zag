<?php

namespace App\Observers;
use App\City;

class CityObserver
{
    public function creating(City $city)
    {
        if($city->original)
            $city->city_id = $city->original->city_id;
        else
            $city->city_id = City::max('city_id') + 1;
    }

    public function deleted(City $city)
    {
        $city->translations()->delete();
    }

    public function saving(City $city){
        if($city->original) {
            $city->area_id = $city->original->area_id;
        }

        if($city->translations->count()) {
            $city->translations()->update([
                'area_id' => $city->area_id,
            ]);
        }
    }
}
