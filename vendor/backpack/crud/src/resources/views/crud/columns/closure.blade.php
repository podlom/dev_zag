{{-- closure function column type --}}
@php
    $style = isset($column['style'])? $column['style'] : '';
@endphp
<span style="{{ $style }}">
    {!! $column['function']($entry) !!}
</span>