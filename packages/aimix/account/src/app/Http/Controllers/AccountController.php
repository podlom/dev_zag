<?php

namespace Aimix\Account\app\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;

use Illuminate\Http\Request;
use Aimix\Account\app\Rules\EquallyPassword;
use Aimix\Account\app\Models\Transaction;
use Aimix\Account\app\Models\Usermeta;
use Aimix\Shop\app\Models\Order;

class AccountController extends BaseController
{
    public function index(Request $request) {
      $orders = Order::where('usermeta_id', \Auth::user()->usermeta->id)->orderBy('created_at', 'desc')->paginate(5);

      return view('account.index')->with('orders', $orders);
    }

    public function history(Request $request) {
      $orders = Order::where('usermeta_id', \Auth::user()->usermeta->id)->orderBy('created_at', 'desc')->paginate(5);

      if($request->isJson)
        return response()->json(['orders' => $orders->withPath($request->url().'&page='.$request->page)]);
      else
        return view('account.history')->with('orders', $orders);
    }

    public function transactions(Request $request) {
      $transactions = Transaction::where('usermeta_id', \Auth::user()->usermeta->id)->where('is_completed', 1)->orderBy('created_at', 'desc')->paginate(1);
      
      $referrals = Usermeta::where('referrer_id', \Auth::user()->usermeta->id)->paginate(1);

      if($request->isJson)
        return response()->json(['transactions' => $transactions->withPath($request->url().'&page='.$request->page), 'referrals' => $referrals->withPath($request->url().'&page='.$request->page)]);
      else
        return view('account.transactions')->with('transactions', $transactions)->with('referrals', $referrals);
    }

    public function edit(Request $request) {
      $user = \Auth::user();
      $usermeta = $user->usermeta;

      foreach($request->input() as $key => $value) {
        if($key == 'email')
          $user[$key] == $value;
        elseif($key != '_token') 
          $usermeta[$key] = $value;

        if($key == 'firstname')
          $user['name'] = $value;
      }

      $user->save();
      $usermeta->save();

      return redirect('account')->with('type', 'success')->with('message', 'Your account has been successfully updated!');
    }

    public function changePassword(Request $request) {
      $user = \Auth::user();
      $newPass = $request->input('password');
      $confirmPass = $request->input('password_confirmation');

      $validatedData = $request->validate([
          'old_password' => ['sometimes', 'required', new EquallyPassword],
          'password' => ['required', 'confirmed'],
          
      ], [
        'required' => 'Поле обязательно для заполнения'
      ]);
      
      $user->password = \Hash::make($newPass);
      $user->save();

      return redirect('account')->with('type', 'success')->with('message', 'Your password has been successfully changed!');
    }

    public function createTransaction(Request $request) {
      $transaction = new Transaction;

      $transaction->type = $request->input('transaction_type');
      $transaction->is_completed = 0;
      $transaction->change = $transaction->type == 'withdraw'? 0 - $request->input('transaction_change') : $request->input('transaction_change');
      $transaction->usermeta_id = \Auth::user()->usermeta->id;
      $transaction->description = 'Withdraw method: ' . $request->input('transaction_method') . "\r\n"
                                 .'Card number/ID: ' . $request->input('transaction_requisites');

      $transaction->save();

      return redirect('account')->with('type', 'success')->with('message', 'Your withdrawal request successfully sent!');
    }
}
