@component('mail::message')
# Withdrawal request processed

{!! nl2br($transaction->description) !!}

Withdraw amount: {{ - $transaction->change }} {{ config('aimix.shop.currency_default') }}

Bonus balance: {{ abs(round($transaction->balance, 2)) }} {{ config('aimix.shop.currency_default') }}

@component('mail::button', ['url' => url('/')])
Back to site
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
