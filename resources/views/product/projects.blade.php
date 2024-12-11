@extends('product.layouts.app')

@section('product_content')

<div class="product-page__main product-page__main-tabs">
    <div class="product__wrapper product__wrapper-list-position">
        <ul class="product__list product__list-tabs ts-ln-7">
            <projectcard v-for="(project, key) in projects" :key="key" :data-project="project" @add-to-favorites="addToFavorites"></projectcard>
        </ul>
    </div>
</div>

@endsection
@push('scripts')
<script>
    var product = @json($product);
    var projects = @json($projects);
</script>
<script src="{{ url('js/product/projects.js?v=' . $version) }}"></script>
@endpush
