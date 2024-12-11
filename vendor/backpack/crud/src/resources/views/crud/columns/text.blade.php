{{-- regular object attribute --}}
@php
	$value = data_get($entry, $column['name']);
	$style = isset($column['style'])? $column['style'] : '';
	if (is_array($value)) {
		$value = json_encode($value);
	}
@endphp

<span style="{{ $style }}">{{ (array_key_exists('prefix', $column) ? $column['prefix'] : '').str_limit(strip_tags($value), array_key_exists('limit', $column) ? $column['limit'] : 40, "[...]").(array_key_exists('suffix', $column) ? $column['suffix'] : '') }}</span>