<?php

namespace Aimix\Account\app\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Notifications\Notifiable;

class Usermeta extends Model
{
    use CrudTrait;
    use Notifiable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'usermetas';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = [
      'birthday'
    ];
    protected $casts = [
      'extras' => 'array'
    ];
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
      public function toArray() {
        return [
          'id' => $this->id,
          'user_id' => $this->user_id,
          'firstname' => $this->firstname,
          'lastname' => $this->lastname,
          'fullname' => $this->fullname,
          'partonymic' => $this->partonymic,
          'gender' => $this->gender,
          'birthday' => $this->birthday,
          'telephone' => $this->telephone,
          'email' => $this->email,
          'address' => $this->address,
          'subscription' => $this->subscription,
          'referrer_id' => $this->referrer_id,
          'referral_code' => $this->referral_code,
          'extras' => $this->extras,
          'created_at' => $this->created_at->format('d.m.Y'),
          'is_registred' => $this->is_registred,
          'bonus_balance' => $this->bonus_balance,
          'total_earned_bonuses' => $this->total_earned_bonuses,
          'this_month_earned_bonuses' => $this->this_month_earned_bonuses,
          'referrals' => $this->referrals->where('is_registred', 1)->toArray(),
        ];
      }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function user() {
      return $this->belongsTo('App\User');
    }

    public function orders() {
      return $this->hasMany('Aimix\Shop\app\Models\Order');
    }

    public function referrer(){
      return $this->belongsTo('Aimix\Account\app\Models\Usermeta', 'referrer_id', 'id');
    }

    public function referrals(){
      return $this->hasMany('Aimix\Account\app\Models\Usermeta', 'referrer_id', 'id');
    }

    public function transactions() {
      return $this->hasMany('Aimix\Account\app\Models\Transaction');
    }

    public function thisMonthTransactions() {
      return $this->hasMany('Aimix\Account\app\Models\Transaction')->whereMonth('created_at', now()->format('m'));
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
    public function getFullnameAttribute() {
      return $this->firstname . ' ' . $this->lastname;
    }

    public function getIsRegistredAttribute() {
      return (boolean) $this->user;
    }

    public function getBonusBalanceAttribute() {
      $balance = 0;
      
      foreach($this->transactions->where('is_completed', 1)->where('balance', '!==', null) as $transaction) {
        $balance += $transaction->change;
      }

      return round($balance, 2);
    }

    public function getThisMonthEarnedBonusesAttribute() {
      $bonuses = 0;

      foreach($this->thisMonthTransactions->where('is_completed', 1)->where('change', '>', 0) as $transaction) {
        $bonuses += $transaction->change;
      }

      return round($bonuses, 2);
    }

    public function getTotalEarnedBonusesAttribute() {
      $bonuses = 0;

      foreach($this->transactions->where('is_completed', 1)->where('change', '>', 0) as $transaction) {
        $bonuses += $transaction->change;
      }

      return round($bonuses, 2);
    }

    // public function getReferralTreeAttribute() {
    //   $referralTree = [];

    //   $referrals = $this->referrals;
      
    //   for($i = 0; $i < config('aimix.account.referral_levels'); $i++) {
    //     foreach($referrals as $key => $referral) {
    //       $referrals = $referral->referrals;

    //       $referralTree
    //     }

    //   }

    //   return $referralTree;
    // }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    
}
