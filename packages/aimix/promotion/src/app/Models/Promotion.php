<?php

namespace Aimix\Promotion\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Promotion extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;
    
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'promotions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = ['dates'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
         parent::boot();

         if(config('aimix.aimix.enable_languages')) {
            static::addGlobalScope('language', function (Builder $builder) {
            	$language = session()->has('lang')? session()->get('lang'): 'ru';
                $builder->where('promotions.language_abbr', $language);
            });
        }

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('promotions.is_active', 1);
        });

        static::addGlobalScope('actual', function (Builder $builder) {
            $builder->where('promotions.start', '<=', now())->where('promotions.end', '>=', now());
        });
    }
    
    public function clearGlobalScopes()
    {
        static::$globalScopes = [];
    }
    
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_title',
            ],
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'original_id' => $this->original_id,
            'slug' => $this->slug,
            'title' => $this->title,
            'desc' => $this->desc,
            'start' => \Carbon\Carbon::createFromTimeStamp(strtotime($this->start))->format('d.m'),
            'end' => \Carbon\Carbon::createFromTimeStamp(strtotime($this->end))->format('d.m'),
            'image' => strpos($this->image, 'files') || strpos($this->image, 'glide')? url(str_replace('files', 'glide', $this->image) . '?w=350&h=350&fit=crop&fm=pjpg&q=75') : url('common/' . $this->image . '?w=350&h=350&fit=crop&fm=pjpg&q=75'),
            'product_name' => $this->product? $this->product->name : null,
            'product_link' => $this->product? $this->product->link . '/promotions' : null,
            'product_city' => $this->product && $this->product->address['city']? \App\City::where('city_id', $this->product->address['city'])->first()->name : null,
            'brand_name' => $this->brand? $this->brand->name : null,
            'brand_logo' => $this->brand && $this->brand->images['logo']? (strpos($this->brand->images['logo'], 'svg') === false? url('common/' . $this->brand->images['logo'] . '?w=17') : url($this->brand->images['logo'])) : '',
            'brand_link' => $this->brand? $this->brand->link . '/promotions': '',
            'brand_site' => $this->brand? $this->brand->contacts['site'] : '',
            'link' => $this->link
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function product()
    {
        return $this->belongsTo('Aimix\Shop\app\Models\Product');
    }

    public function brand()
    {
        return $this->product->brand();
    }

    public function translations()
    {
        return $this->hasMany('\Aimix\Promotion\app\Models\Promotion', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\Aimix\Promotion\app\Models\Promotion', 'original_id');
    }

    public function getTelegramImgAttribute() {
        return $this->image? url(str_replace('files', 'glide', $this->image) . '?w=510&fm=pjpg&q=85') : url('glide/NOVOSTI/news.jpg?w=510&fm=pjpg&q=85');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getSlugOrTitleAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }

        return $this->title;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
