<?php

namespace Aimix\Account\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'transactions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function toArray() {
      return [
        'id' => $this->id,
        'usermeta_id' => $this->usermeta_id,
        'order_id' => $this->order_id,
        'change' => $this->change,
        'balance' => $this->balance,
        'is_completed' => $this->is_completed,
        'type' => $this->type,
        'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        'description' => nl2br($this->description),
      ];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function usermeta(){
        return $this->belongsTo('Aimix\Account\app\Models\Usermeta');
    }

    public function order(){
        return $this->belongsTo('Aimix\Shop\app\Models\Order');
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
