@extends('product.layouts.app')

@section('product_content')

@if(count($types))
<div class="product-page__same">
    <h4 class="ts-typical-project-price-ln-7 product-page__caption">{{ __('main.Цены на типовые проекты') }}</h4>
    @foreach($types as $type => $projects)
    <div class="product-page__container">
        @if($type)
        <h5 class="product-page__same__caption">{{ __('plural.nominative.' . $type) }}</h5>
        @endif
        <ul class="product-page__same__list">
          @foreach($projects as $item)
            <li class="product-page__same__item">
                <div class="img" v-lazy:background-image="'{{ $item->images? url('common/' . $item->images[0]) : url('common/' . $product->image) }}'" @if($product->category_id === 2 || $product->category_id === 7) style="background-size: contain;" @endif></div>
                <p class="name">
                    <a href="{{ $item->link }}">{{ $item->name }}</a>
                </p>
                @if($item->price && $item->area != 0)
                <p class="price">{{ __('main.от') }} <strong>{{ $item->price * $item->area }}</strong> грн</p>
                @else
                    @if($item->status)
                        <p class="ts-item-status item__status"> {{ __('main.' . $item->status) }}</p>
                    @endif

                    @if($item->building)
                        <p class="ts-item-status-building item__status-building"> {{ $item->building }}</p>
                    @endif

                    @if($item->status_done)
                        <p class="ts-item-status-done item__status-done"> {{ $item->status_done }}</p>
                    @endif

                    @if($item->status_project)
                        <p class="ts-item-status-project item__status-project"> {{ $item->status_project }}</p>
                    @endif
                @endif

                @if($item->area != 0)
                    <p class="area">{{ $item->area }} {{ $item->area_unit }}</p>
                @endif
            </li>
          @endforeach
        </ul>
    </div>
    @endforeach
</div>
@endif


@if(!empty($product->updated_at->format('d.m.Y')))
<div class="product-page__status-wrapper">
    <h4 class="ts-home-build-status-ln-36 product-page__caption">{{ __('main.Статус строительства домов') }}</h4>

    <div class="product-status__info ts-2024-08-05-ln-41">
        <p class="product-page__status-info">{{ __('main.Информация предоставлена отделом продаж состоянием на') }} {{ $product->updated_at->format('d.m.Y') }}</p>
    </div>


    @if(isset($statuses_array['Земельный участок']) && !empty($statuses_array))
        <!-- @ts $statuses_array: {{ var_export($statuses_array, true) }} -->
    @else
        <!-- @ts $statuses_array is not set or empty -->
    @endif

    
    @if(isset($type) && ($type != 2))
    @if(count($statuses_array) > 1 || (count($statuses_array) === 1 && !isset($statuses_array['Земельный участок'])))
    <div class="product-page__status-table">
        <div class="product-page__status-table__header">
            <p class="type">{{ __('main.Тип') }}</p>
            <p class="in-project">{{ __('main.В проекте') }}</p>
            <p class="build">{{ __('main.Строятся') }}</p>
            <p class="status">{{ __('main.Завершенные') }}</p>
        </div>
        <ul class="product-page__status-table__list ts-status-type--{{ $type }}}">
          @foreach($statuses_array as $type => $statuses)
          @if($type !== 'Земельный участок')
            <li class="product-page__status-table__item">
                <p class="type">{{ __('plural.nominative.' . $type) }}</p>
                <p class="in-project">{{ $statuses['project'] === null? '0' : $statuses['project'] }}</p>
                <p class="build">{{ $statuses['building'] === null? '0' : $statuses['building'] }}</p>
                <p class="status">{{ $statuses['done'] === null? '0' : $statuses['done'] }}</p>
            </li>
            @endif
          @endforeach
        </ul>
    </div>
    @endif
    @endif
    @if(isset($statuses_array['Земельный участок']))
    <div class="product-page__status-table">
        <div class="product-page__status-table__header">
            <p class="type">{{ __('main.Тип') }}</p>
            <p class="in-project">{{ __('main.Свободные') }}</p>
            <p class="build">{{ __('main.Застраиваются') }}</p>
            <p class="status">{{ __('main.Застроенные', ['type' => '']) }}</p>
        </div>
        <ul class="product-page__status-table__list">
            @foreach($statuses_array as $type => $statuses)
            @if($type === 'Земельный участок')
            <li class="product-page__status-table__item">
                <p class="type">{{ __('plural.nominative.' . $type) }}</p>
                <p class="in-project">{{ $statuses['project'] === null? 'н.д' : $statuses['project'] }}</p>
                <p class="build">{{ $statuses['building'] === null? 'н.д' : $statuses['building'] }}</p>
                <p class="status">{{ $statuses['done'] === null? 'н.д' : $statuses['done'] }}</p>
            </li>
            @endif
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endif

<div class="product-page__characteristic-wrapper js-drop-item">
    <h4 class="product-page__caption">{{ __('main.Характеристики') }}</h4>
    <ul class="product-page__characteristic-list">
        @if($product->category_id === 1 || $product->category->original_id === 1)
            @if($houses_area['min'] || $houses_area['max'])
            <li class="product-page__characteristic-item">
                <div class="img" style="background-image:url({{ url('img/characteristic-img-1.png') }})"></div>
                <div class="info">
                    <h5 class="caption">{{ __('main.Площадь домовладения') }}</h5>
                    <p>{{ $houses_area['min']? $houses_area['min'] : '' }} {{ $houses_area['min'] && $houses_area['max'] && $houses_area['min'] != $houses_area['max']? ' - ' : '' }} {{ $houses_area['min'] != $houses_area['max'] ? $houses_area['max'] : '' }} {{ !$houses_area['min'] && !$houses_area['max'] ? 'н.д.' : '' }} ({{ $product->area_unit }})</p>
                </div>
            </li>
            @endif
        @elseif($product->category_id === 2 || $product->category->original_id === 2)
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url({{ url('img/characteristic-img-1.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Площадь квартиры') }}</h5>
                <p>{{ $houses_area['min'] }} {{ $houses_area['min'] != $houses_area['max'] ? '- ' . $houses_area['max'] : '' }} (м<sup>2</sup>)</p>
            </div>
        </li>
        @endif
        @if($plot_area)
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url({{ url('img/characteristic-img-2.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Площадь участка') }}</h5>
                <p>{{ $plot_area['min'] }} {{ $plot_area['min'] != $plot_area['max'] ? '- ' . $plot_area['max'] : '' }} (сот.)</p>
            </div>
        </li>
        @endif
        @if(isset($product->extras['area']))
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url({{ url('img/characteristic-img-3.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Площадь застройки') }}</h5>
                <p>{{ $product->area_m2 }} (га)</p>
            </div>
        </li>
        @endif
        <!-- <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(./img/characteristic-img-4.png)"></div>
            <div class="info">
                <h5 class="caption">Количество участков</h5>
                <p>5</p>
            </div>
        </li>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(./img/characteristic-img-5.png)"></div>
            <div class="info">
                <h5 class="caption">Количество таунхаусов</h5>
                <p>5</p>
            </div>
        </li>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(./img/characteristic-img-6.png)"></div>
            <div class="info">
                <h5 class="caption">Количество дуплексов</h5>
                <p>4</p>
            </div>
        </li> -->
        @foreach($types as $type => $projects)
        @if($projects->sum('total'))
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url({{ url('img/characteristic-img-4.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Количество') }} {{ __('plural.genitive.' . $type) }}</h5>
                <p>{{ $projects->sum('total') }}</p>
            </div>
        </li>
        @endif
        @endforeach
        @if($product->category_id === 1 || $product->category->original_id === 1)
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url({{ url('img/characteristic-img-7.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Количество домовладений') }}</h5>
                <p>{{ $houses_amount? $houses_amount : 0 }}</p>
            </div>
        </li>
        @elseif($product->category_id === 2 || $product->category->original_id === 2)
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url({{ url('img/characteristic-img-7.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Количество квартир') }}</h5>
                @if($product->flats_count)
                <p>{{ $product->flats_count }}</p>
                @else
                <p>{{ $houses_amount? $houses_amount : 0 }}</p>
                @endif
            </div>
        </li>
        @endif
        @if(isset($product->wall_material))
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url({{ url('img/characteristic-img-8.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Материал стен') }}</h5>
                <p>{{ $product->wall_material? __('attributes.wall_materials.' . $product->wall_material) : 'н.д.' }}</p>
            </div>
        </li>
        @endif
        @if(isset($product->roof_material))
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Материал крыши') }}</h5>
                <p>{{ $product->roof_material? __('attributes.roof_materials.' . $product->roof_material) : 'н.д.' }}</p>
            </div>
        </li>
        @endif
        @if(($product->category_id === 2 || $product->category_id === 7) && isset($product->newbuild_type))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption ts-2024-07-29-ln-200">{{ __('main.Тип') }}</h5>
                <p>{{ __('main.' . $product->newbuild_type) }}</p>
            </div>
        </li>
        @endif
        @if(isset($product->floors))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Этажность') }}</h5>
                <p>{{ $product->floors }}</p>
            </div>
        </li>
        @endif
        @if(isset($product->technology))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Технология строительства') }}</h5>
                <p>{{ $product->technology }}</p>
            </div>
        </li>
        @endif
        @if(isset($product->class))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Класс') }}</h5>
                <p>{{ $product->class }}</p>
            </div>
        </li>
        @endif
        @if(isset($product->insulation))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Утепление') }}</h5>
                <p>{{ $product->insulation }}</p>
            </div>
        </li>
        @endif
        @if(isset($product->ceilings))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Высота потолков') }}</h5>
                <p>{{ $product->ceilings }}</p>
            </div>
        </li>
        @endif
        @if(isset($product->condition))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Состояние квартиры') }}</h5>
                <p>{{ $product->condition }}</p>
            </div>
        </li>
        @endif
        @if(isset($product->closed_area))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Закрытая территория') }}</h5>
                <p>{{ $product->closed_area }}</p>
            </div>
        </li>
        @endif
        @if(isset($product->parking))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Паркинг') }}</h5>
                <p>{{ $product->parking }}</p>
            </div>
        </li>
        @endif

        @if(isset($product->area_cottage))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Размер участка под коттедж') }}</h5>
                <p>{{ $product->area_cottage }}</p>
            </div>
        </li>
        @endif

        @if(isset($product->area_townhouse))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Придомовой участок таунхауса') }}</h5>
                <p>{{ $product->area_townhouse }}</p>
            </div>
        </li>
        @endif

        @if(isset($product->area_duplex))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Придомовой участок дуплекса') }}</h5>
                <p>{{ $product->area_duplex }}</p>
            </div>
        </li>
        @endif

        @if(isset($product->area_quadrex))
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url({{ url('img/characteristic-img-9.png') }})"></div> -->
            <div class="info">
                <h5 class="caption">{{ __('main.Придомовой участок квадрекса') }}</h5>
                <p>{{ $product->area_quadrex }}</p>
            </div>
        </li>
        @endif

        @if($product->infrastructure)
        <li class="product-page__characteristic-item product-page__characteristic-item-big">
            <div class="img" style="background-image:url({{ url('img/characteristic-img-10.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Инфраструктура') }}</h5>
                <p>{{ $product->infrastructure }}</p>
            </div>
        </li>
        @endif
        @if($product->communications_string)
        <li class="product-page__characteristic-item product-page__characteristic-item-big">
            <div class="img communication" style="background-image:url({{ url('img/characteristic-img-11.png') }})"></div>
            <div class="info">
                <h5 class="caption">{{ __('main.Коммуникации') }}</h5>
                <p>{{ $product->communications_string }}</p>
            </div>
        </li>
        @endif
    </ul>
    @if($product->description)
    <div class="product-page__characteristic-description">
        <h5>{{ __('main.Описание') }}</h5>
        {!! $product->description !!}
    </div>
    @endif
    <button class="main-button-more js-drop-button"></button>
</div>

@endsection

@section('product_after_content')
<section class="product" v-if="other_products.total">
    <div class="product__wrapper slider-infinity">
        <div class="general-heading container">
            <h2 class="main-caption-l main-caption-l--transform">{{ $product->category_id == 1 || $product->category_id == 6? __('main.Рядом с городком') : __('main.Рядом с комплексом') }}</h2>
        </div>
        <ul class="product__list product-slider__list js-infinity-slider-list">
            <productcard v-for="(product, key) in other_products.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
        </ul>
        <div class="general-button__wrapper js-arrow-infinity general-button--hide container">
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
@if($companies_count)
<section class="best-company-info">
    <div class="best-company-info__wrapper container">
        <div class="general-heading">
            <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Лучшие застройщики') }}</h2>
            <p class="calc-product">{{ $companies_count }}<span>{{ __('main.Всего') }}</span></p>
        </div>
        <ul class="best-company-info__list">
            <companycard v-for="(company, key) in companies" :key="key" :data-company="company" @add-to-favorites="addToFavorites" @add-to-notifications="addToNotifications"></companycard>
        </ul>
        <a href="{{ route($lang . '_companies') }}" class="main-button-more">
            <span class="text">{{ __('main.Смотреть все компании') }}</span>
            <span class="icon-arrow-more"></span>
        </a>
    </div>
</section>
@endif
@if($promotions_count)
<section class="product">
    <div class="product__wrapper slider-infinity">
        <div class="general-heading container">
            <h2 class="main-caption-l main-caption-l--transform">{{ __('main.Акции от застройщиков') }}</h2>
            <p class="calc-product">{{ $promotions_count }} <span>{{ __('main.Всего') }}</span></p>
        </div>
        <ul class="product__list product__list-sale product-slider__list js-infinity-slider-list">
            <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites"></promotioncard>
        </ul>
        <div class="general-button__wrapper js-arrow-infinity container">
            <div class="wrapper @if($promotions->count() < 5) hide @endif">
                <button class="general-button prev">
                    <span class="icon-arrow-left"></span>
                </button>
                <button class="general-button next">
                    <span class="icon-arrow-right"></span>
                </button>
            </div>
            <a href="{{ route($lang . '_promotions') }}" class="main-button-more">
                <span class="text">{{ __('main.Смотреть все акции') }}</span>
                <span class="icon-arrow-more"></span>
            </a>
        </div>
    </div>
</section>
@endif
<section class="popup popup-full-screen slider-infinity" data-target="full-screen">
      <button class="close-popup js-close">
        <span class="decor"></span>
    </button>
    <div class="popup__wrapper popup-full-screen__wrapper">
        <ul class="popup-full-screen__list js-infinity-slider-list">
            <li class="popup-full-screen__item js-slider-item-infinity show" v-lazy:background-image="'{{ $product->image? url('common/' . $product->image . '?w=1000&q=90') : url('files/47/net-fot500x500.jpg') }}'"></li>
            @if(isset($product->images) && (is_array($product->images) || $product->images instanceof Countable))
                @foreach($product->images as $key => $image)
                  <code class="ts-key">$key: {{ $key }}</code>
                  <code class="ts-image">$image: {{ $image }}</code>

                <!-- li class="popup-full-screen__item js-slider-item-infinity" v-lazy:background-image="'{{ url('common/' . $image . '?w=1000&q=90') }}'"></li -->
                @endforeach
            @endif
        </ul>
    </div>
    <div class="popup-buttons js-arrow-infinity">
        <button class="popup-button prev">
            <span class="icon-arrow-left"></span>
        </button>
        <button class="popup-button next">
            <span class="icon-arrow-right"></span>
        </button>
    </div>
</section>
@endsection

@push('styles')
<style>
    .product-page__same__item .img[lazy="loading"],
    .popup-full-screen__item[lazy="loading"] {
        background-size: auto 1px;
    }
</style>
@endpush

@push('scripts')
<script>
    var product = @json($product);
    var companies = @json($companies);
    var promotions = @json($promotions);
    var product_id = @json($product->id);
</script>
<script src="{{ url('js/product/show.js?v=' . $version) }}"></script>
@endpush
