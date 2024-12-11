@php
  $original = isset($entry)? $entry->original : null;
  $translations = isset($entry)? $entry->translations : null;
@endphp

@if($original)
@php 
if($field['model'] != 'meta')
  $title = $original->title? $original->title : ($original->name? $original->name : ($original->question? $original->question : 'Без названия'));
else
  $title = $original->extras['cottage_h1']? $original->extras['cottage_h1'] : ($original->extras['newbuild_h1']? $original->extras['newbuild_h1'] : 'Без названия');
  
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
  if($field['model'] != 'meta')
    $title = $item->title? $item->title : ($item->name? $item->name : ($item->question && $item->getTable() != 'poll_options'? $item->question : 'Без названия'));
  else
    $title = $item->extras['cottage_h1']? $item->extras['cottage_h1'] : ($item->extras['newbuild_h1']? $item->extras['newbuild_h1'] : 'Без названия');
    
  @endphp
  <br>
  <a href="{{ url('admin/' .  $field['model'] . '/' . $item->id . '/edit') }}">{{ $title }} ({{ $item->language_abbr }})</a>
  @endforeach
</div>
@endif