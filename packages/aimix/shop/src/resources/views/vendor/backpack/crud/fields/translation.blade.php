@php
  $original = isset($entry)? $entry->original : null;
  $translations = isset($entry)? $entry->translations : null;
@endphp

@if($original)
@php 
  $title = $original->title? $original->title : ($original->name? $original->name : 'Без названия');
@endphp
<div class="form-group col-sm-12">
  <label>Оригинал</label>
  <br>
  <a href="{{ url('admin/' .  $field['model'] . '/' . $original->id . '/edit') }}">{{ $title }}</a>
</div>
@elseif($translations && $translations->count())
<div class="form-group col-sm-12">
  <label>Переводы</label>
  
  @foreach($translations as $item)
  @php 
    $title = $item->title? $item->title : ($item->name? $item->name : 'Без названия');
  @endphp
  <br>
  <a href="{{ url('admin/' .  $field['model'] . '/' . $item->id . '/edit') }}">{{ $title }} ({{ $item->language_abbr }})</a>
  @endforeach
</div>
@endif