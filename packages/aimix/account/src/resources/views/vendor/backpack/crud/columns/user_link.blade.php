@php
  $usermeta = $entry->user;
@endphp
@if($usermeta)
<span>
  <a href="{{ url('admin/user/' . $entry->user->id . '/show') }}">{{ $entry->user->name }}</a>
</span>
@endif