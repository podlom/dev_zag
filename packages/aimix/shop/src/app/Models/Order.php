<?php

namespace Aimix\Shop\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'orders';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
      'info' => 'array'
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function toArray()
    {
      return [
        'id' => $this->id,
        'usermeta_id' => $this->usermeta_id,
        'delivery' => $this->delivery,
        'payment' => $this->payment,
        'code' => $this->code,
        'status' => $this->status,
        'is_paid' => $this->is_default,
        'price' => $this->price,
        'info' => $this->info,
        'created_at' => \Carbon\Carbon::parse($this->created_at)->format('d M Y'),
      ];
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function products()
    {
      return $this->belongsToMany('\Aimix\Shop\app\Models\Product')->withPivot('amount');
    }
    
    public function modifications()
    {
      return $this->belongsToMany('Aimix\Shop\app\Models\Modification')->withPivot('amount');
    }
    
    public function delivery()
    {
      return $this->belongsTo('Aimix\Shop\app\Models\Delivery');
    }
    
    public function payment()
    {
      return $this->belongsTo('Aimix\Shop\app\Models\Payment');
    }

    public function usermeta()
    {
      return $this->belongsTo('Aimix\Account\app\Models\Usermeta');
    }
    
    public function transactions() {
      return $this->hasMany('Aimix\Account\app\Models\Transaction');
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
