@component('mail::message')
# Referral bonus info

You received a bonus from your referral's order.

Bonus amount: {{ $transaction->change }} {{ config('aimix.shop.currency_default') }}

Bonus balance: {{ $transaction->balance }} {{ config('aimix.shop.currency_default') }}

@component('mail::button', ['url' => url('/')])
Back to site
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
