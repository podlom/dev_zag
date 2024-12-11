<?php

namespace Aimix\Shop\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Aimix\Shop\app\Models\Attribute;

class Modification extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;
    
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'modifications';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
      'extras' => 'array',
      'images' => 'array',
      'layouts' => 'array',
    ];

    protected $with = ['attrs'];
    
    public $attributes_array;
    
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        
        parent::boot();

        static::addGlobalScope('with_attrs', function (Builder $builder) {
          $builder->has('attrs');
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
                'source' => 'slug_or_name',
            ],
        ];
    }

    public function toArray()
    {
      return [
        'id' => $this->id,
        'name' => $this->name,
        'slug' => $this->slug,
        'price' => $this->price,
        'old_price' => $this->old_price,
        'is_active' => $this->is_active,
        'is_default' => $this->is_default,
        'is_pricehidden' => $this->is_pricehidden,
        'in_stock' => $this->in_stock,
        'code' => $this->code,
        'attrs' => $this->getPluckedAttributesArray(),
        'extras' => $this->extras,
        'images' => $this->images_array,
        'layouts' => $this->layouts,
        'amount' => isset($this->amount)? $this->amount : 1,
        'product_name' => $this->product->name,
        'product_image' => url($this->product->image),
        'product_link' => $this->product->link,
        // remove from package
        'type' => $this->type,
        'area' => $this->area,
        'status' => $this->status,
        'floors' => $this->floors,
        'bedrooms' => $this->bedrooms,
        'amount' => $this->amount,
        'link' => $this->link,
        'rooms' => $this->rooms,
        'status_string' => $this->status_string,
        'product_category_id' => $this->product->category_id,
        'product_original_category_id' => $this->product->category->original_id,
      ];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function attrs()
    {
        return $this->belongsToMany('Aimix\Shop\app\Models\Attribute')->using('Aimix\Shop\app\Models\AttributeModification')->withPivot('value');
    }
    
    public function product()
    {
        return $this->belongsTo('Aimix\Shop\app\Models\Product');
    }

    public function translations()
    {
        return $this->hasMany('\Aimix\Shop\app\Models\Modification', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\Aimix\Shop\app\Models\Modification', 'original_id');
    }
    
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeBase($query)
    {
      return $query->where('is_default', 1)->first();
    }
    public function scopeNotBase($query)
    {
      return $query->where('is_default', 0);
    }
    
    public function scopeComplectation($query, $name)
    {
      return $query->where('extras', 'like', '%'.$name.'%');
    }
    
    public function scopeActive($query)
    {
      return $query->where('is_active', 1);
    }
    
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getPluckedAttributesArray()
    {
        //dd($this->attrs);
        return $this->attrs->pluck('pivot.value', 'id');

    }
    
    public function getSlugOrNameAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }

        return $this->name;
    }

    public function getProductImageAttribute()
    {
        return $this->product->image;
    }

    public function getProductLinkAttribute()
    {
        return $this->product->link;
    }

    public function getProductNameAttribute()
    {
        return $this->product->name;
    }

    public function getPivotAmountAttribute()
    {
        return $this->pivot->amount;
    }

    // remove all these getters from package
    public function getAreaAttribute()
    {
      return $this->attrs->where('slug', 'area')->first()? $this->attrs->where('slug', 'area')->first()->pivotValue : 0;
    }

    public function getTypeAttribute()
    {
      if($this->product->category->id === 2 || $this->product->category->original_id === 2)
        return $this->product->extras['newbuild_type'];

      if($this->attrs->where('slug', 'type')->first())
        return __('attributes.cottage_types.' . $this->attrs->where('slug', 'type')->first()->pivotValue);

      return null;
    }

    public function getTypeKeyAttribute()
    {
      if($this->product->category->id === 2 || $this->product->category->original_id === 2)
        return $this->product->extras['newbuild_type'];

      if($this->attrs->where('slug', 'type')->first())
        return $this->attrs->where('slug', 'type')->first()->pivotValue;

      return null;
    }

    public function getFloorsAttribute()
    {
      if($this->product->category->id === 2 || $this->product->category->original_id === 2)
        return null;

      if($this->attrs->where('slug', 'floor')->first())
        return $this->attrs->where('slug', 'floor')->first()->pivotValue;

      return null;
    }

    public function getBedroomsAttribute()
    {
      if($this->product->category->id === 2 || $this->product->category->original_id === 2)
        return null;

      return $this->attrs->where('slug', 'bedroom')->first()? $this->attrs->where('slug', 'bedroom')->first()->pivotValue : 0;
    }

    public function getRoomsAttribute()
    {
      if($this->product->category_id === 1 || $this->product->category_id === 6 || !$this->attrs->where('slug', 'rooms')->first())
        return null;

        return $this->attrs->where('slug', 'rooms')->first()->pivotValue;
    }

    public function getAmountAttribute()
    {
      return [
        'project' => $this->attrs->where('slug', 'status_project')->first()? $this->attrs->where('slug', 'status_project')->first()->pivotValue : 0,
        'building' => $this->attrs->where('slug', 'status_building')->first()? $this->attrs->where('slug', 'status_building')->first()->pivotValue : 0,
        'done' => $this->attrs->where('slug', 'status_done')->first()? $this->attrs->where('slug', 'status_done')->first()->pivotValue : 0,
      ];
    }

    public function getStatusAttribute()
    {
      return $this->attrs->where('slug', 'status')->first()? $this->attrs->where('slug', 'status')->first()->pivotValue : null;
    }

    public function getLinkAttribute()
    {
      return $this->product->link . '/projects/' . $this->slug;
    }

    public function getTotalAttribute()
    {
      if(!$this->attrs->where('slug', 'status_project')->first() || !$this->attrs->where('slug', 'status_building')->first() || !$this->attrs->where('slug', 'status_done')->first())
        return 0;
        
      return $this->attrs->where('slug', 'status_project')->first()->pivotValue + $this->attrs->where('slug', 'status_building')->first()->pivotValue + $this->attrs->where('slug', 'status_done')->first()->pivotValue;
    }

    public function getAddressAttribute()
    {
      return $this->product? $this->product->address : null;
    }

    public function getImagesArrayAttribute()
    {
      return array_map(function($item) {
        return url($item);
      }, $this->images);
    }

    public function getStatusStringAttribute()
    {
      return __('main.modification_statuses.' . $this->status);
    }

    public function getAreaUnitAttribute()
    {
      if($this->type == __('attributes.cottage_types.Земельный участок'))
        return 'сот';

      return 'кв.м';
    }

    public function getTranslationLinkAttribute() {
      $lang = $this->language_abbr === 'ru'? 'uk' : 'ru';
      $scopes = static::$globalScopes;
      $this->clearGlobalScopes();
      
      if($lang === 'ru' && $this->original) {
          $link = $this->original()->first()->link;
      } elseif($lang !== 'ru' && $this->translations->where('language_abbr', $lang)->first()){
          $link = $this->translations->where('language_abbr', $lang)->first()->link;
      } else {
          $link = url($lang);
      }

      static::$globalScopes = $scopes;
      return $link;
  }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    
    public function setAttrsAttribute($value)
    {
      $attrs = $value;
      foreach($attrs as $id => $attr) {
        if(Attribute::find($id)->type === 'number')
          $attrs[$id]['value'] = (float) $attr['value'];
      }
      $this->attributes_array = $attrs;
    }

    public function setImagesAttribute($value)
    {
      $this->attributes['images'] = json_encode(array_map(function($img) {
        return str_replace(url(''), '', $img);
      }, $value));
    }
}
