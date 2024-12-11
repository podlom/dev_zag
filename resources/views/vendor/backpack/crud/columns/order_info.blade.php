@php
  $products = $entry->info['products'];
  $info = \Illuminate\Support\Arr::except($entry->info, ['products']);
  $usermeta = isset($entry['usermeta_id'])? \Aimix\Account\app\Models\Usermeta::find($entry['usermeta_id']) : null;
@endphp

<span>
  @if($usermeta)
  <p>Имя: <a href="{{ url('admin/usermeta/' . $entry['usermeta_id'] . '/show' ) }}"><strong>{{ $usermeta->fullname }}</strong></a></p>
  @endif
  @if(isset($info['extras']))
  @foreach($info['extras'] as $key => $item)
    <p>{{ $key }}: <strong>{{ $item }}</strong></p>
  @endforeach
  @endif
  @if(isset($info['tel']))
  <p>Телефон: <strong>{{ $info['tel'] }}</strong></p>
  @endif
  @if(isset($info['email']))
  <p>Email: <strong>{{ $info['email'] }}</strong></p>
  @endif
  <p>Способ оплаты: <strong>{{ $info['payment'] }}</strong></p>
  <p>Способ доставки: <strong>{{ $info['delivery'] }}</strong></p>
  @if(isset($info['point']))
  <p>Пункт выдачи: <strong>{{ $info['point'] }}</strong></p>
  @endif
  @if(isset($info['comment']))
  <p>Комментарий: <strong>{{ $info['comment'] }}</strong></p>
  @endif
  <h5>Товары:</h5>
  <br>
  @foreach($products as $product)
  <p>
    <strong>{{ $product['name'] }}</strong>
    <br>
    <p>Код товара: <strong>{{ $product['code'] }}</strong></p>
    <p>Цена: <strong>{{ $product['price'] }} {{ config('aimix.shop.currency_default') }}</strong></p>
    @if($product['old_price'])
    <p>Старая цена: <strong>{{ $product['old_price'] }} {{ config('aimix.shop.currency_default') }}</strong></p>
    @endif
    <p>Количество: <strong>{{ $product['amount'] }} шт</strong></p>
    <p>Сумма: <strong>{{ $product['price'] * $product['amount'] }} {{ config('aimix.shop.currency_default') }}</strong></p>
  </p>
  <hr>
  @endforeach
  <h4>Сумма заказа: <strong>{{ $entry->price }} {{ config('aimix.shop.currency_default') }}</strong></h4>
</span>