<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'old_status' => $this->old_status,
            'old_status_string' => $this->old_status_string,
            'old_price' => $this->old_price,
            'status' => $this->status,
            'status_string' => $this->status_string,
            'price' => $this->price,
            'product_name' => $this->product->name,
            'product_type' => $this->product->type,
            'brand_name' => $this->product->brand->name,
            'product_image' => url($this->product->image),
            'product_link' => $this->product->link,
        ];
    }

    public function product()
    {
        return $this->belongsTo('\Aimix\Shop\app\Models\Product');
    }

    public function getStatusStringAttribute()
    {
        return __('main.product_statuses.' . $this->status);
    }

    public function getOldStatusStringAttribute()
    {
        return __('main.product_statuses.' . $this->old_status);
    }
}
