<?php

namespace Backpack\LangFileManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Support\Arr;

class Language extends Model
{
    use CrudTrait;

    protected $table = 'languages';

    protected $fillable = ['name', 'flag', 'abbr', 'native', 'active', 'default'];

    public $timestamps = false;

    public static function getActiveLanguagesArray()
    {
        $active_languages = self::where('active', 1)->get()->toArray();
        $localizable_languages_array = [];

        if (count($active_languages)) {
            foreach ($active_languages as $key => $lang) {
                $localizable_languages_array[$lang['abbr']] = $lang;
            }

            return $localizable_languages_array;
        }

        return config('laravellocalization.supportedLocales');
    }
    
    public static function getActiveLanguagesNames()
    {
      return array_map(function($item){
        return $item['native'];
      }, self::getActiveLanguagesArray() );
    }

    public static function findByAbbr($abbr = false)
    {
        return self::where('abbr', $abbr)->first();
    }
}
