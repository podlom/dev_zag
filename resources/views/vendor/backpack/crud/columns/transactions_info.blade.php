@php
  $transactions = $entry->transactions->sortByDesc('created_at');
@endphp

<span>
  @if(count($transactions))
    <p>Balance: <strong>${{ $transactions->first()->balance }}</strong></p>
    
    <h5>Transactions:</h5>
    <br>
    <table>
      <tr>
        <th>Date</th>
        <th>Type</th>
        <th>Description</th>
        <th>Order</th>
        <th>Review</th>
        <th>Change</th>
        <th>Balance</th>
      </tr>
      @foreach($transactions as $transaction)
      <tr>
        <td>{{ $transaction->created_at }}</td>
        <td>{{ $transaction->type }}</td>
        <td>{{ $transaction->description }}</td>
        <td><a href="{{ url('admin/order/' . $transaction->order_id . '/show') }}">{{ $transaction->order->code }}</a></td>
        <td><a href="{{ url('admin/review/' . $transaction->review_id . '/show') }}">{{ $transaction->review_id }}</a></td>
        <td>{{ $transaction->change > 0 ? '+' . $transaction->change : $transaction->change }}</td>
        <td>${{ $transaction->balance }}</td>
      </tr>
      @endforeach
    </table>
  @endif
</span>