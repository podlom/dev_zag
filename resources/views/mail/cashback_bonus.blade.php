@component('mail::message')
# Cashback info

You received a cashback bonus from your order.

Bonus amount: {{ $transaction->change }} {{ config('aimix.shop.currency_default') }}

Bonus balance: {{ $transaction->balance }} {{ config('aimix.shop.currency_default') }}

@component('mail::button', ['url' => url('/')])
Back to site
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
