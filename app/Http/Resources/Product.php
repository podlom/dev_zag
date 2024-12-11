<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
		
    public function toArray($request)
    {
				if(session()->has('lang')) {
					$lang = session('lang');
					\App::setLocale($lang);
				}

        return [
	        'id' => $this->id,
	        'original_id' => $this->original_id,
	        'name' => $this->name,
	        'slug' => $this->slug,
	        'category_id' => $this->category_id,
 	        'image' => $this->image? url(str_replace(['files', 'uploads', ' '], ['glide', 'common/uploads', '%20'], $this->image) . '?w=350&fit=crop&fm=pjpg&q=75') : url('common/uploads/product_images/eafe7ffb4e7c6d846592f328ba4c47c2.jpg?w=350&fit=crop&fm=pjpg&q=75'),
 	        'link' => $this->link,
	        'rating' => $this->true_rating,
					'extras' => $this->extras,
					'extras_translatable' => $this->extras_translatable,
					'status_string' => $this->status_string,
					'second_status' => $this->second_status,
					'image_title' => __('main.Картинка') . ': ' . $this->name,
					'image_alt' => __('main.Фото') . ': ' . $this->name,
	        // because async
					// 'price' => '',
					// 'type' => '',
	        // 'city' => '',
					'fake' => 0,
					
					'show_price' => $this->show_price,
					'lat' => $this->lat,
					'lng' => $this->lng,
					'area_unit' => $this->area_unit,
					'brand_name' => $this->brand? $this->brand->name : null,
					'communications' => $this->communications_string,
					'infrastructure' => isset($this->extras_translatable['infrastructure'])? $this->extras_translatable['infrastructure'] : '-',
					'wall_material' => isset($this->extras['wall_material'])? __('attributes.wall_materials.' . $this->extras['wall_material']) : '-',
					'prices' => $this->prices,
					'statistics_price' => $this->statistics_price,
					'statistics_price_plot' => $this->statistics_price_plot,
	        'type' => $this->type,
	        'city' => $this->city,
	        'price' =>  $this->price?$this->price: $this->statistics_price,
        ];
    }
}
