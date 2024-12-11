@extends('layouts.app', [
  'meta_title' => 'Рейтинг '  . mb_strtolower(__('main.type_' . $type . '_genitive')) . ' ' . $product->name,
  'meta_desc' => 'Рейтинг ' . mb_strtolower(__('main.type_' . $type . '_plural_genitive_alt')) . ' ➨ Рейтинг '  . mb_strtolower(__('main.type_' . $type . '_genitive')) . ' ' . $product->name,
])

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            {{ Breadcrumbs::render('product_tab', $product, 'Рейтинг', 'rating') }}
        </div>
    </section>
    <section class="product-page-tabs product-page-tabs--rating">
        <div class="product-page-tabs__wrapper container">
            <div class="product-page-tabs__header">
                <div class="product-page-tabs__header-img">
                    <img src="{{ $product->image? url($product->image) : url('files/47/net-fot500x500.jpg') }}" alt="{{ __('main.Фото') }}: {{ $product->name }}" title="{{ __('main.Картинка') }}: {{ $product->name }}">
                </div>
                <div class="product-page-tabs__header-info">
                    <div class="wrapper">
                        <h1 class="main-caption-l main-caption-l--transform">Рейтинг {{ mb_strtolower(__('main.type_' . $type . '_genitive')) }} {{ $product->name }}</h1>
                        <div class="general-noty__buttons-container">
                            @if($product->category_id == 2 || $product->category_id == 7)
                            <button class="general-noty__button general-noty__button-compare" @click="addToComparison({{ $product }})" :class="{active: comparison.includes({{ $product->id }}) || comparison.includes({{ $product->original_id }})}" title="{{ __('main.Добавить в сравнение') }}">
                                <span class="icon-compare"></span>
                            </button>
                            @endif
                            <button class="general-noty__button general-noty__button-favorite" @click="addToFavorites({{ $product }}, 'products')" :class="{active: favorites['products'].includes({{ $product->id }}) || favorites['products'].includes({{ $product->original_id }})}" title="{{ __('main.Добавить в избранное') }}">
                                <span class="icon-heart-outline"></span>
                            </button>
                            <button class="general-noty__button general-noty__button-sing-up" @click="addToNotifications({{ $product }}, 'products')" :class="{active: notifications['products'].includes({{ $product->id }}) || notifications['products'].includes({{ $product->original_id }})}" title="{{ __('main.Добавить в уведомления') }}">
                                <span class="icon-bell-outline"></span>
                            </button>
                        </div>
                    </div>
                    <div class="info">
                        <div class="rating-info">
                            <span class="rating-icon"></span>
                            <p class="rating-info__name"><strong>{{ $rating_position }} {{ __('main.место в общем рейтинге') }}</strong></p>
                        </div>
                        @if($product->brand)
                        <div class="project">
                            <span>{{ __('main.Проект от') }}:</span>
                            <h5>{{ $product->brand->name }}</h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="rating-block__table rating-block__table--project">
                <div class="rating-block__table__container">
                    <div class="rating-block__table__caption">
                        <p class="table-type"><strong>Характеристика</strong></p>
                        <p class="table-description"><strong>{{ __('main.Описание') }}</strong></p>
                        <p class="table-rating"><strong>{{ __('main.Оценка') }}</strong></p>
                    </div>
                    <div class="wrapper">
                        @foreach($table as $attr => $row)
                        <div class="rating-block__table__item">
                            <p class="table-type">{{ $attr }}</p>
                            <p class="table-description">{{ $row['value'] }}</p>
                            <p class="table-rating table-rating--active">{{ $row['rating'] }}</p>
                        </div>
                        @endforeach
                        <div class="rating-block__table__item">
                            <p class="table-type">{{ __('main.Общая сумма баллов') }}</p>
                            <p class="table-description"></p>
                            <p class="table-rating table-rating--active">{{ $product->top_rating }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Рядом в рейтинге') }}</h2>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="products.data.length">
                    <productcard v-for="(product, key) in products.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="{{ url('img/preload-for-files.gif') }}" style="margin:auto" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </section>
    @if(false)
    <section class="info-block">
        <div class="info-block__wrapper container">
            <!-- <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"></h2>
            </div> -->
            <div class="info-block__container">
                <div class="info-block__inner" >
                
                </div>
            </div>
        </div>
    </section>
    @endif
</main>
@endsection

@push('scripts')
<script src="{{ url('js/product/rating.js?v=' . $version) }}"></script>
@endpush