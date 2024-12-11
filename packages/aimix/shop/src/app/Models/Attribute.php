<?php

namespace Aimix\Shop\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
    
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Attribute extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'attributes';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
      'values' => 'object'
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function clearGlobalScopes()
    {
        static::$globalScopes = [];
    }
    
    public function toArray()
    {
      
      return [
        'id' => $this->id,
        // 'name' => $this->name,
        'slug' => $this->slug,
        'attribute_group_id' => $this->attribute_group_id,
        'icon' => $this->icon,
        'description' => $this->description,
        'si' => $this->si,
        'default_value' => $this->default_value,
        'values' => $this->type == 'colors'? $this->colorsValues : $this->values,
        'type' => $this->type,
        'is_important' => $this->is_important,
        'is_active' => $this->is_active,
        'in_filters' => $this->in_filters,
        'in_properties' => $this->in_properties,
        'human_value' => $this->humanValue,

        // remove ?
        'name' => isset(__('main')[$this->name])? __('main.' . $this->name) : $this->name,
        'trans_values' => $this->trans_values
      ];
    }
    
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_name',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function categories()
    {
        return $this->belongsToMany('Aimix\Shop\app\Models\Category');
    }
    
    public function modifications()
    {
        return $this->belongsToMany('Aimix\Shop\app\Models\Modification')->using('Aimix\Shop\app\Models\AttributeModification')->withPivot('value');
    }
    
    public function attributeGroup()
    {
      return $this->belongsTo('Aimix\Shop\app\Models\AttributeGroup');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
     public function scopeNoEmpty($query){
      return $query->has('modifications');
    }
    
    public function scopeAllFromCategory($query, $category) {
      if(!$category)
        return $query->get();
      else
        return $category->attributes()->get();
    }
    
    public function scopeImportant($query)
    {
      return $query->where('is_important', 1);
    }
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getSlugOrNameAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }

        return $this->name;
    }
    
    public function getMaxValueAttribute(){
     if(!$this->modifications->count())
      return 0;
     
     $max = $this->modifications->max('pivot.value');
     return $max;
    }
    
    public function getMinValueAttribute(){
     if(!$this->modifications->count())
      return 0;
       
     $min = $this->modifications->min('pivot.value');
     return $min;
    }
    
    public function getJsonAllAttribute(){
     return collect($this->getAttributes())->toJson();   
    }
    
    public function getInputValuesAttribute(){
      $values = array(
      'color' => null,
      'number' => [
        'step' => 0,
        'min' => '',
        'max' => ''
      ],
      'range' => [
        'step' => 0,
        'min' => '',
        'max' => ''
      ],
      'datetime' => [
        'datetime' => null,
        'date' => null,
        'daterange' => null,
      ],
      'select' => null,  
        );
        
        if($this->type == 'range')
        {
          if(isset($this->values->step))
          $values['range']['step'] = $this->values->step;
          
          if(isset($this->values->min))
          $values['range']['min'] = $this->values->min;
          
          if(isset($this->values->max))
          $values['range']['max'] = $this->values->max;
          
        }elseif($this->type == 'color')
        {
          $values['color'] = $this->values;

        }elseif($this->type == 'number')
        {
          if(isset($this->values->step))
          $values['number']['step'] = $this->values->step;
          
          if(isset($this->values->min))
          $values['number']['min'] = $this->values->min;
          
          if(isset($this->values->max))
          $values['number']['max'] = $this->values->max;
          
        }elseif($this->type == 'datetime')
        {
          if(isset($this->values) && $this->values == 'datetime')
          $values['datetime']['datetime'] = 'selected="selected"';
          
          if(isset($this->values) && $this->values == 'date')
          $values['datetime']['date'] = 'selected="selected"';
          
          if(isset($this->values) && $this->values == 'daterange')
          $values['datetime']['daterange'] = 'selected="selected"';
          
        }elseif($this->type == 'select' || $this->type == 'checkbox' || $this->type == 'radio')
        {
          if(isset($this->values))
          $values['select'] = $this->values;
          
        }
        return $values;
      }
      
      public function getPivotValueAttribute(){
        if(!$this->pivot || !$this->pivot->value)
          return null;
          
          return $this->pivot->value;
      }
        
      public function getHumanValueAttribute()
      {
        if(gettype($this->pivotValue) == 'string')
          return $this->pivotValue !== null? ($this->pivotValue . $this->si) : null;
        else
          return $this->pivotValue;
      }

      public function getColorsValuesAttribute()
      {
        if(!$this->type == 'colors')
          return null;
        
        $colors = [];

        foreach($this->modifications as $modification) {
          foreach($modification->attrs->where('name', 'Цвет')->first()->pivotValue as $color) {
            $colors[] = $color['name'];
          }
        }

        return array_unique($colors);
      }
    
    
      public function getTransValuesAttribute()
      {
        $trans = [];
        foreach($this->values as $key => $val) {
          $trans[$key] = __('attributes.cottage_types.' . $val);
        }
        return $trans;
      }
    
    
    
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    // public function setSiAttribute($value){
    //   $requestValue = \Request::all()['value'];
    //   if(is_array($requestValue))
    //   $attr_value = collect(array_filter($requestValue))->toJson();
    //  else
    //   $attr_value = json_encode($requestValue);

    //   $this->attributes['value'] = $attr_value;
      
    // }
    public function setTypeAttribute($value){
    
      $this->attributes['type'] = $value['type'];
      $this->attributes['values'] = isset($value['values']) ? json_encode($value['values']) : null;
    }
}
