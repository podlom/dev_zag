<?php

namespace Aimix\Account\app\Observers;

use Aimix\Account\app\Models\Transaction;
use Aimix\Account\app\Notifications\WithdrawCompleted;

class TransactionObserver
{
    public function updated(Transaction $transaction) {
      if(!$transaction->is_completed || $transaction->balance !== null)
        return;
        
      $currentBalance = $transaction->usermeta->bonusBalance;
      
      $transaction->balance = $currentBalance + $transaction->change;
      $transaction->created_at = now();
      
      $transaction->save();
      
      if($transaction->type == 'withdraw')
        $transaction->usermeta->notify(new WithdrawCompleted($transaction));
    }
}
