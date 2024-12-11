<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Company extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
		
    public function toArray($request)
    {
        return [
	        'id' => $this->id,
	        'original_id' => $this->original_id,
	        'name' => $this->name,
	        'slug' => $this->slug,
	        'category_id' => $this->category_id,
	        'category_name' => $this->category->name,
	        'description' => $this->description,
	        'images' => $this->images,
	        'contacts' => $this->contacts,
	        'extras' => $this->extras,
	        'achievements' => $this->achievements,
	        'link' => $this->link,
	        'is_popular' => $this->is_popular,
	        'address' => $this->address,
	        'city' => $this->city,
	        'area' => $this->area,
	        'region' => $this->region,
	        'business_card' => $this->images['business_card']? url($this->images['business_card']) : url('/img/fireplace-rect.svg')
        ];
    }
}
