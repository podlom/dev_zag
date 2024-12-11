@extends('product.layouts.app')

@section('product_content')
<div class="product-page__main product-page__main-tabs ts-promotions-tab-ln-4">
    <div class="product-page__video">
        <h4 class="ts-product__video-title product-page__caption product-page__caption-l">{{ __('main.Акции о') }} {{ $product->name }}</h4>

        <div class="product-page__video-list">
            @if(isset($product->promotions) && $product->promotions->count())
                <ul class="product__list product__list-sale">
                    <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" @add-to-favorites="addToFavorites"></promotioncard>
                </ul>
            @else
                <span class="product-page__promotions-item">{{ __('main.У этого объекта нет пока акций') }}</span>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var product = @json($product);
    var promotions = @json($promotions);
</script>
<script src="{{ url('js/product/promotions.js?v=' . $version) }}"></script>
@endpush
