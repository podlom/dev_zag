{{-- checkbox with loose false/null/0 checking --}}
@php
$checkValue = data_get($entry, $column['name']);

$checkedIcon = data_get($column, 'icons.checked', 'la-check-circle');
$uncheckedIcon = data_get($column, 'icons.unchecked', 'la-circle');

$exportCheckedText = data_get($column, 'labels.checked', trans('backpack::crud.yes'));
$exportUncheckedText = data_get($column, 'labels.unchecked', trans('backpack::crud.no'));

$icon = $checkValue == false ? $uncheckedIcon : $checkedIcon;
$text = $checkValue == false ? $exportUncheckedText : $exportCheckedText;
@endphp

<span>
    <i class="la {{ $icon }}"></i>
</span>

<span class="sr-only">{{ $text }}</span>
