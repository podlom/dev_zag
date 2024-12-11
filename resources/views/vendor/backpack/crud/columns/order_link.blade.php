@php
  $order = $entry->order;
@endphp
@if($order)
<span>
  <a href="{{ url('admin/order/' . $entry->order_id . '/show') }}">{{ $entry->order->code }}</a>
</span>
@endif