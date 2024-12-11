<?php

namespace Aimix\Review\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log; // added by @ts 2024-08-16
use DB as BaseDb; // added by @ts 2024-08-16 15:17

class Review extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'reviews';
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
    public function toArray()
    {
      return [
        "id" => $this->id,
        "is_moderated" => $this->is_moderated,
        "type" => $this->type,
        "name" => $this->name,
        "email" => $this->email,
        "reviewable_id" => $this->reviewable_id,
        "reviewable_type" => $this->reviewable_type,
        "rating" => $this->rating,
        "file" => $this->file? str_replace('files', 'glide', $this->file) . '?w=160&h=160&fit=crop&fm=pjpg&q=85' : '',
        "images" => $this->images? $this->imagesArray : null,
        "text" => $this->text,
        "created_at" => $this->date,
        'reviewable_name' => $this->reviewable? ($this->reviewable->name? $this->reviewable->name : $this->reviewable->title) : null,
        'reviewable_image' => $this->reviewableImage? url(str_replace('files', 'glide', $this->reviewableImage) . '?w=160&h=160&fit=crop&fm=pjpg&q=85') : '',
        // remove
        'reviewable_reviews_count' => $this->reviewable? $this->reviewable->reviews->count() : null,
        'created_at_number' => $this->created_at->format('d.m.Y'),
        'profession' => $this->profession,
        'robots_date' => $this->created_at->format('Y-m-d'),
        'reviewable_link' => $this->reviewable? $this->reviewable->link : '',
      ];
    }

    // remove from package
    protected static function boot()
    {
        // Listen for database queries
        /* BaseDb::listen(function ($query) {
            Log::info(__METHOD__ . ' SQL: ' . $query->sql);
            Log::info(__METHOD__ . ' Bindings: ' . implode(', ', $query->bindings));
            Log::info(__METHOD__ . ' Time: ' . $query->time . 'ms');
        }); */

        parent::boot();
        if(config('aimix.aimix.enable_languages')) {
          static::addGlobalScope('language', function (Builder $builder) {
            $language = session()->has('lang')? session()->get('lang'): 'ru';
              Log::info(__METHOD__ . ' +' . __LINE__ . ' @ts session $language: ' . var_export($language, true)); // added by @ts 2024-08-16 15:04
              $builder->where('language_abbr', $language);
          });
      }
    }

    public function clearGlobalScopes()
    {
        static::$globalScopes = [];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function reviewable()
    {
        return $this->morphTo();
    }

    // remove
    public function translations()
    {
        return $this->hasMany('\Aimix\Review\app\Models\Review', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\Aimix\Review\app\Models\Review', 'original_id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopePublished($query)
    {
      return $query->where('is_moderated', 1);
    }
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getImagesArrayAttribute()
    {
      return array_map(function($img) {
        return url($img);
      }, $this->images);
    }

    public function getDateAttribute()
    {
      return $this->created_at->format('d.m.') . $this->created_at->isoFormat('YY');
      // return \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
    }

    public function getReviewableImageAttribute()
    {
      if(!$this->reviewable)
        return null;

      if($this->reviewable->image)
        return $this->reviewable->image;

      return null;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
