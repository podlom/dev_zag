@component('mail::message')
# Order info

@component('mail::table')
| Product       | Price         | Amount   |
| ------------- |:-------------:| --------:|
@foreach($order->info['products'] as $product)
@if(config('aimix.shop.modifications_in_order'))
@foreach($product as $modification)
| {{ $modification['product_name'] }} {{ $modification['name'] }}      | {{ $modification['price'] }} {{ config('aimix.shop.currency_default') }}      | {{ $modification['amount'] }}      |
@endforeach
@else
| {{ $product['name'] }}      | {{ $product['price'] }} {{ config('aimix.shop.currency_default') }}      | {{ $product['amount'] }}      |
@endif
@endforeach
@endcomponent

Total: {{ $order->price }} {{ config('aimix.shop.currency_default') }}

@if($order->transactions->where('type', 'bonuses_used')->first())
Bonuses used: {{ - $order->transactions->where('type', 'bonuses_used')->first()->change }} {{ config('aimix.shop.currency_default') }}

Total to pay: {{ $order->price + $order->transactions->where('type', 'bonuses_used')->first()->change }} {{ config('aimix.shop.currency_default') }}
@endif



@component('mail::button', ['url' => url('/')])
Back to site
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
