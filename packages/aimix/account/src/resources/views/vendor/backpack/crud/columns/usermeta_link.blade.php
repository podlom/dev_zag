@php
  $usermeta = $entry->usermeta;
@endphp
@if($usermeta)
<span>
  <a href="{{ url('admin/usermeta/' . $entry->usermeta_id . '/show') }}">{{ $entry->usermeta->fullname }}</a>
</span>
@endif