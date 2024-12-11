<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ArticleRegion extends Model
{
  protected $table = 'article_regions';
  // protected $primaryKey = 'id';
  // public $timestamps = false;
  protected $guarded = ['id'];
  // protected $fillable = [];
  // protected $hidden = [];
  // protected $dates = [];

    protected static function boot()
    {
      parent::boot();
      if(config('aimix.aimix.enable_languages')) {
          static::addGlobalScope('language', function (Builder $builder) {
            $language = session()->has('lang')? session()->get('lang'): 'ru';
              $builder->where('language_abbr', $language);
          });
      }
    }
    
    public function clearGlobalScopes()
    {
      static::$globalScopes = [];
    }

    public function translations()
    {
        return $this->hasMany('\App\ArticleRegion', 'original_id');
    }

    public function original()
    {
        return $this->belongsTo('\App\ArticleRegion', 'original_id');
    }
}
