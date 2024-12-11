<?php

namespace Aimix\Gallery\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Gallery extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'galleries';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'images' => 'array'
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
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
            'images' => $this->imagesArray,
            'link' => $this->link
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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
    public function getSlugOrNameAttribute()
    {
        if ($this->slug != '') {
            return $this->slug;
        }
        return $this->name;
    }

    public function getImagesArrayAttribute()
    {
        if(!$this->images)
            return null;

        return array_map(function($item) {
            return [
                // 'name' => $item['name'],
                'image' => url($item['image']),
                // 'desc' => $item['desc']
            ];
        }, $this->images);
    }

    public function getLinkAttribute()
    {
        return url('lookbook#' . $this->slug);
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setImagesAttribute()
    {
        $value = request()->file('images');
        $oldValue = request()->input('images');
      $images_array = [];
      foreach($value as $key => $item) {
        $img = $item['image'];
        
        $extension = $img->getClientOriginalExtension();
        \Storage::disk('common')->put('uploads/' . $img->getFilename().'.'.$extension,  \File::get($img));
        $images_array[$key]['image'] = 'uploads/' . $img->getFilename().'.'.$extension;
      }
      foreach($oldValue as $key => $item) {
        $images_array[$key] = $item;
      }
      
      $this->attributes['images'] = json_encode($images_array);
    }
}
