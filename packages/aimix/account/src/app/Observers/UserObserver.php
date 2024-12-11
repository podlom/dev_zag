<?php

namespace Aimix\Account\app\Observers;

use App\User;
use Aimix\Account\app\Models\Usermeta;
use Aimix\Account\app\Notifications\ReferralRegistred;
use Aimix\Account\app\Notifications\UserRegistred;


class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */

    public function created(User $user)
    {
        $usermeta = new Usermeta;
        $usermeta->user_id = $user->id;
        $usermeta->firstname = $user->name;
        $usermeta->email = $user->email;
        
        $usermeta->lastname = request()->input('lastname');
        $usermeta->telephone = request()->input('telephone');
        $usermeta->patronymic = request()->input('patronymic');
        $usermeta->gender = request()->input('gender');
        $usermeta->birthday = request()->input('birthday');
        $usermeta->address = request()->input('address');
        $usermeta->subscription = request()->input('subscription');
        $usermeta->extras = request()->input('extras');

        if(config('aimix.account.enable_referral_system')) {
            $usermeta->referral_code = $this->generateUniqueReferralCode();
            $usermeta->referrer_id = request()->input('referrer_id');
        }
        
        $usermeta->save();

        $usermeta->notify(new UserRegistred($usermeta));

        if(config('aimix.account.enable_referral_system')) {
            $referrer = $usermeta;
            for($i = 0; $i < config('aimix.account.referral_levels'); $i++) {
                $referrer = $referrer->referrer;
                $level = $i + 1;

                if(!$referrer)
                    return;

                $referrer->notify(new ReferralRegistred($usermeta, $level));
            }
        }
    }
    

    public function generateUniqueReferralCode() {
        $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'), 0, 12);

        if(Usermeta::where('referral_code', $code)->first()) {
            $this->generateUniqueReferralCode();
            return;
        }

        return $code;
    }
}
