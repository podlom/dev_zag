<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParsingLog extends Model
{
    protected $table = 'parsing_logs';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    protected $casts = [
        'details' => 'array'
    ];

    public function getDateAttribute()
    {
        return $this->created_at->format('d.m.Y H:i:s');
    }
}
