@php
  $products = $entry->info['products'];
  $info = \Illuminate\Support\Arr::except($entry->info, ['products']);
  $usermeta = isset($entry['usermeta_id'])? \Aimix\Account\app\Models\Usermeta::find($entry['usermeta_id']) : null;
  $bonus_used = $entry->transactions->where('type', 'bonuses_used')->first();
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
  @foreach($product as $modification)
  <p>
    <strong>{{ $modification['product_name'] }} {{ $modification['name'] }}</strong>
    <br>
    <p>Цена: <strong>${{ $modification['price'] }}</strong></p>
    @if($modification['old_price'])
    <p>Старая цена: <strong>{{ $modification['old_price'] }} {{ config('aimix.shop.currency_default') }}</strong></p>
    @endif
    <p>Количество: <strong>{{ $modification['amount'] }} шт</strong></p>
    <p>Сумма: <strong>{{ $modification['price'] * $modification['amount'] }} {{ config('aimix.shop.currency_default') }}</strong></p>
  </p>
  <hr>
  @endforeach
  @endforeach
  <h4>Сумма заказа: <strong>{{ $entry->price }} {{ config('aimix.shop.currency_default') }}</strong></h4>
  @if($bonus_used)
  <br>
  <h5>Бонусов использовано: <strong>{{ - $bonus_used->change }} {{ config('aimix.shop.currency_default') }}</strong></h5>
  <br>
  <h4>Сумма к оплате: <strong>{{ $entry->price + $bonus_used->change }} {{ config('aimix.shop.currency_default') }}</strong></h4>
  @endif
</span>