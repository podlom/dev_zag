@extends('product.layouts.app')

@section('product_content')

<div class="product-page__main product-page__main-tabs" itemscope itemtype="http://schema.org/Apartment">
    <meta itemprop="accommodationCategory" content="Residential">
    <meta itemprop="address" content="{{ $product->city }}{{ $product->address_string? ', ' . $product->address_string : '' }}">
    <meta itemprop="latitude" content="{{ $product->lat }}">
    <meta itemprop="longitude" content="{{ $product->lng}}">
    <div class="product-page__general-wrapper slider">
        <div class="product-page__general__caption-wrapper">
            <meta itemprop="name" content="{{ $product->name }} - {{ $project->name }}">
            <h3 class="name-project">{{ $project->name }}</h3>
            <h5 class="ts-type-ln-14 type-project">{{ __('main.' . $project->type) }}</h5>
            <p class="product__status build product__status-tabs">{{ $project->status_string }}</p>
        </div>
        <div class="product-page__general-img js-general-image" itemprop="photo" itemscope itemtype="http://schema.org/ImageObject">
            <img src="{{ count($project->images)? url($project->images[0]) : url($project->product->image) }}" title="{{ __('main.Картинка') }}: {{ $product->name }} - {{ $project->name }}" alt="{{ __('main.Фото') }}: {{ $product->name }} - {{ $project->name }}" itemprop="url">
            <div class="product-page__img-header">
                @if(count($project->images) > 1)
                <div class="product-page__slider-number js-slider-number">
                    <span class="current">1</span>
                    <span>/</span>
                    <span class="all">{{ count($project->images) }}</span>
                </div>
                @endif
                <button class="general__open js-button" data-target="full-screen" title="{{ __('main.На весь экран') }}">
                    <span class="icon-full"></span>
                </button>
            </div>
        </div>
        @if(count($project->images) > 1)
        <div class="product-page__img-navigation">
            <button class="general-button js-image-button-prev disabled">
                <span class="icon-arrow-left"></span>
            </button>
            <ul class="product-page__img-list">
              @foreach($project->images as $key => $image)
                <li class="product-page__img-item js-image @if($key == 0) active @endif" data-index="{{ $key + 1 }}">
                    <img src="{{ url($image) }}" alt="{{ $project->name }}">
                </li>
              @endforeach
            </ul>
            <button class="general-button js-image-button-next">
                <span class="icon-arrow-right"></span>
            </button>
        </div>
        @endif
        @if($project->layouts && count($project->layouts))
        <div class="product-page__plan" itemprop="accommodationFloorPlan" itemscope itemtype="http://schema.org/FloorPlan">
            <h4 class="product-page__caption">{{ __('main.Планировки') }}</h4>
            <ul class="product-page__plan-list">
                @foreach($project->layouts as $key => $layout)
                <li class="product-page__plan-item js-button js-button-plan" data-target="full-screen-plan" data-index="{{ $key + 1 }}">
                    <p class="name" itemprop="name">{{ $layout['name'] }}</p>
                    <div class="product-page__plan-img" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
                        <img src="{{ url($layout['image']) }}" alt="Фото: {{ $layout['name'] }}" title="Картинка: {{ $layout['name'] }}" itemprop="url">
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    <div class="ts-project-blade product-page__info-wrapper product-page__info-wrapper-tabs">
        <div class="product-page__status">
            <p class="product__status build">{{ $project->status_string }}</p>
        </div>
        <div class="product-page__info-body ts-project-blade">
            <div class="product-page__buttons product-page__buttons-tabs ts-project-status__{{ $product->status }} ts-product-is_sold__{{ $product->is_sold }} ts-product-is_frozen__{{ $product->is_frozen }}">
                @if($product->is_sold == 0 && $product->status !== 'project' && $product->is_frozen == 0)
                    @if($product->phone)
                    <a href="tel:{{ explode(',', $product->phone)[0] }}" class="product-page__button general-button-color product-page__button-phone">
                        <span class="icon-phone"></span>
                        <span>{{ explode(',', $product->phone)[0] }}</span>
                    </a>
                    @endif

                    @if($product->site)
                    <a rel="nofollow" href="{{ strpos($product->site, 'http') !== false? $product->site.'?utm_source=zagorodna&utm_medium=referral&utm_campaign' : '//' . $product->site .'?utm_source=zagorodna&utm_medium=referral&utm_campaign'}}" target="_blank" class="product-page__button general-button-color product-page__button-question js-button" data-target="help">{{ __('main.Перейти на сайт') }}</a>
                    @endif
                @endif

                <!-- button class="product-page__button product-page__button-help filter-button js-button" data-target="questions">{{ __('main.Задать вопрос') }}</button>
                <button class="product-page__button catalog__filter-button product-page__button-meeting js-button" data-target="meeting">{{ __('main.Назначить визит') }}</button -->
            </div>
            <div class="wrapper">
                <div class="product-page__info-about-house">
                    @if($project->area)
                    <div class="area about-house-wrapper" itemprop="floorSize" itemscope itemtype="http://schema.org/QuantitativeValue">
                        <meta itemprop="value" content="{{ $project->area }}">
                        <meta itemprop="unitCode" content="MTK">
                        <div class="area-img img" style="background-image:url({{ url('img/area-icon.png') }})"></div>
                        <p>{{ $project->area }} {{ $project->area_unit }}</p>
                    </div>
                    @endif
                    @if($project->floors)
                    <div class="floor about-house-wrapper">
                        <div class="floor-img img" style="background-image:url({{ url('img/floor-icon.png') }})"></div>
                        <p>{{ $project->floors }}</p>
                    </div>
                    @endif
                    @if($project->bedrooms)
                    <div class="rooms about-house-wrapper">
                        <div class="rooms-img img" style="background-image:url({{ url('img/rooms-icon.png') }})"></div>
                        <p itemprop="numberOfBedrooms">{{ $project->bedrooms }}</p>
                    </div>
                    @endif
                </div>
                <div class="product-page__price">
                    <p>{{ $project->price }}</p>
                    <span>грн/{{ $project->area_unit }}</span>
                </div>
            </div>
        </div>
        @if($product->notBaseModifications->count() > 1)
        <div class="product-page__info-footer">
            <div class="product-page__same product-page__same-tabs">
                <h4 class="product-page__caption">{{ __('main.Другие типовые проекты') }}</h4>
                @foreach($types as $type => $projects)
                <div class="product-page__container">
                    <h5 class="product-page__same__caption">{{ __('plural.nominative.' . $type) }}</h5>
                    <ul class="product-page__same__list">
                      @foreach($projects as $item)
                        <li class="product-page__same__item">
                            <div class="img" style="background-image:url({{ $item->images? url($item->images[0]) : url($product->image) }}); @if($product->category_id === 2 || $product->category_id === 7) background-size: contain; @endif" ></div>
                            <p class="name">
                                <a href="{{ $item->link }}">{{ $item->name }}</a>
                            </p>
                            @if($item->price * $item->area)
                            <p class="price">от <strong>{{ $item->price * $item->area }}</strong> грн</p>
                            @endif
                            @if($item->area)
                            <p class="area">{{ $item->area }} {{ $item->area_unit }}</p>
                            @endif
                        </li>
                      @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if($project->layouts && count($project->layouts))
        <div class="product-page__plan product-page__plan-tablet">
            <h4 class="product-page__caption">{{ __('main.Планировки') }}</h4>
            <ul class="product-page__plan-list">
                @foreach($project->layouts as $key => $layout)
                <li class="product-page__plan-item js-button js-button-plan" data-target="full-screen-plan" data-index="{{ $key + 1 }}">
                    <p class="name">{{ $layout['name'] }}</p>
                    <div class="product-page__plan-img">
                        <img src="{{ url($layout['image']) }}" alt="Фото: {{ $layout['name'] }}" title="Картинка: {{ $layout['name'] }}">
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

@endsection

@section('product_after_content')
<section class="popup popup-full-screen slider-infinity" data-target="full-screen">
      <button class="close-popup js-close">
        <span class="decor"></span>
    </button>
    <div class="popup__wrapper popup-full-screen__wrapper">
        <ul class="popup-full-screen__list js-infinity-slider-list">
          @foreach($project->images as $key => $image)
            <li class="popup-full-screen__item js-slider-item-infinity @if($key == 0) show @endif" style="background-image:url({{ url($image) }})"></li>
          @endforeach
          @if(!count($project->images))
            <li class="popup-full-screen__item js-slider-item-infinity show" style="background-image:url({{ url($project->product->image) }})"></li>
          @endif
        </ul>
    </div>
    @if(count($project->images) > 1)
    <div class="popup-buttons js-arrow-infinity">
        <button class="popup-button prev">
            <span class="icon-arrow-left"></span>
        </button>
        <button class="popup-button next">
            <span class="icon-arrow-right"></span>
        </button>
    </div>
    @endif
</section>
@if($project->layouts && count($project->layouts))
<section class="popup popup-full-screen slider" data-target="full-screen-plan">
    <button class="close-popup js-close">
        <span class="decor"></span>
    </button>
    <div class="popup__wrapper popup-full-screen-plan__wrapper">
        <ul class="product-page__plan-list">
            @foreach($project->layouts as $key => $layout)
            <li class="product-page__plan-item js-slider-item show" data-index="{{ $key + 1 }}">
                <p class="name">{{ $layout['name'] }}</p>
                <div class="product-page__plan-img">
                    <img src="{{ url($layout['image']) }}" alt="Фото: {{ $layout['name'] }}" title="Картинка: {{ $layout['name'] }}">
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="popup-buttons js-arrows">
        <button class="popup-button prev">
            <span class="icon-arrow-left"></span>
        </button>
        <button class="popup-button next">
            <span class="icon-arrow-right"></span>
        </button>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>

</script>
<script src="{{ url('js/product/default.js?v=' . $version) }}"></script>
@endpush
