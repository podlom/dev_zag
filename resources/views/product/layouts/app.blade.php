@extends('layouts.app', [
  'meta_title' => $meta_title,
  'meta_desc' => $meta_desc,
  'og_image' => $product->image? url('common/' . $product->image . '?w=600&q=90') : url('files/47/net-fot500x500.jpg'),
])

@push('header_scripts')
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": ["Apartment", "Product"],
        "name": "{{ $product->name }}",
        "sku": "{{ $product->id }}",
        "identifier": "{{ $product->id }}",
        "productID": "{{ $product->id }}",
        "mpn": "{{ $product->id }}",
        "latitude": "{{ $product->lat }}",
        "longitude": "{{ $product->lng }}",
        "address": "{{ $product->city }}{{ $product->address_string? ', ' . $product->address_string : '' }}",
        "image": [
            "{{ $product->image? url($product->image) : '' }}"
            @if (isset($product->images) && is_countable($product->images) && count($product->images) > 0)
                @foreach($product->images as $img)
                ,"{{ url($img) }}"
                @endforeach
            @endif
        ],
        "offers": {
            "@type":"AggregateOffer",
            "offerCount":"{{ $product->flats_count? $product->flats_count : ($houses_amount ?? 1) }}",
            "lowPrice":{{ $product->modifications->where('price', '!=', 0)->min('price')?? 0 }},
            "highPrice":{{ $product->modifications->where('price', '!=', 0)->min('price') != $product->modifications->where('old_price', '!=', 0)->max('old_price') ? $product->modifications->max('old_price') : ($product->modifications->max('price')?: 0) }},
            "priceCurrency":"UAH"
        },
        "description": "{{ strip_tags($product->description) }}",
        "material": "{{ $product->wall_material? __('attributes.wall_materials.' . $product->wall_material) : '' }}",
        "url": "{{ $product->link }}",
        "brand": "{{ $product->category_id == 2 || $product->category_id == 7? __('main.type_newbuild') : __('main.type_cottage') }}",
        "AggregateRating": {
            "@type": "AggregateRating",
            "itemReviewed": {
                "name": "{{ $product->name }}"
            },
            "ratingCount": {{ $product->old_rating_count? $product->old_rating_count : 1 }},
            "reviewCount": {{ count($product->reviews)? count($product->reviews) : 1 }},
            "ratingValue": 5
        }
        @if(count($product->reviews))
        ,
        "review": {
            "@type": "Review",
            "itemReviewed": [
                '@type' => 'Product',
                "name": "{{ $product->name }}"
            ],
            "author": {
                "@type": "Person",
                "name": "{{ $product->reviews->first()->name }}"
            },
            "reviewBody": "{{ $product->reviews->first()->text }}",
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": 5
            }
        }
        @endif
    }
</script>
@endpush

@section('content')
<main>
    <div class="decor-background" style="background-image:url({{ url('img/background-img-2.png') }})"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            @if($project)
                {{ Breadcrumbs::render('product_tab', $product, $tab_name, $tab, $project) }}
            @elseif($tab)
                {{ Breadcrumbs::render('product_tab', $product, $tab_name, $tab) }}
            @else
                {{ Breadcrumbs::render('product', $product) }}
            @endif
        </div>
    </section>
@if(!$tab)
<div class="product-page">
  <div class="general-sitebar general-sitebar-product">
      <div class="general__map js-map-wrapper">
            <button class="general__open js-button js-button-map" data-target="full-map" title="{{ __('main.На весь экран') }}">
                <span class="icon-full"></span>
            </button>
            <div id="general__map" style="height: 860px;">
        </div>
      </div>
      <div class="general-voting" v-if="poll" v-cloak>
          <h5>@{{ poll.title }}</h5>
          <div class="general-voting__form">
              <label class="checkbox__wrapper" v-for="option in poll.options">
                <input :type="poll.is_multiple? 'checkbox' : 'radio'" class="input-checkbox" name="poll" :value="option.original_id? option.original_id : option.id" v-model="selectedAnswers" v-if="!poll_voted">
                  <span class="custome-checkbox" v-if="!poll_voted">
                      <span class="icon-active"></span>
                  </span>
                  <span class="checkbox-text">@{{ option.title }} @{{ poll_voted? '- ' + answerVotes(option) : '' }}</span>
              </label>
              <button class="main-button disabled" @click="vote()" v-if="!poll_voted">{{ __('main.Проголосовать') }}</button>
          </div>
      </div>
  </div>
  <div class="general-heading container">
      <h1 class="ts-product-layout-main-header main-caption-l main-caption-l--transform">{{ $h1 }}</h1>
      <div class="general-noty__buttons-container">
          @if($product->category_id == 2 || $product->category_id == 7)
          <button class="general-noty__button general-noty__button-compare" @click="addToComparison({{ $product }})" :class="{active: comparison.includes({{ $product->id }}) || comparison.includes({{ $product->original_id }})}">
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
  <div class="product-page__wrapper container">
    <h4 class="product-page__type">{{ $product->category->name }}</h4>
    <div class="product-page__main">
        <div class="product-page__general-wrapper slider">
            <div class="product-page__img-header">
                <div class="product-page__slider-number js-slider-number">
                    <span class="current">1</span>
                    <span>/</span>
                    @if (isset($product->images) && is_countable($product->images) && count($product->images) > 0)
                        <span class="all">{{ count($product->images) + 1 }}</span>
                    @endif
                </div>
                <button class="general__open js-button" data-target="full-screen" title="{{ __('main.На весь экран') }}">
                    <span class="icon-full"></span>
                </button>
            </div>
            <div class="product-page__general-img js-general-image">
                <img v-lazy="'{{ $product->image? url('common/' . $product->image . '?w=600&q=80') : url('files/47/net-fot500x500.jpg') }}'" alt="{{ __('main.Фото') }}: {{ $product->name }}" title="{{ __('main.Картинка') }}: {{ $product->name }}">
            </div>
            <div class="product-page__img-navigation">
                <button class="general-button js-image-button-prev disabled">
                    <span class="icon-arrow-left"></span>
                </button>
                <ul class="product-page__img-list">
                    <li class="product-page__img-item js-image active" data-index="1">
                        <img v-lazy="'{{ $product->image? url('common/' . $product->image . '?w=600&q=80') : url('files/47/net-fot500x500.jpg') }}'" alt="{{ __('main.Фото') }}: {{ $product->name }}" title="{{ __('main.Картинка') }}: {{ $product->name }}">
                    </li>
                    @if (isset($product->images) && is_countable($product->images) && count($product->images) > 0)
                  @foreach($product->images as $key => $image)
                    <li class="product-page__img-item js-image" data-index="{{ $key + 2 }}">
                        <img v-lazy="'{{ url('common/' . $image . '?w=600&q=80') }}'" alt="{{ __('main.Фото') }}: {{ $product->name }}" title="{{ __('main.Картинка') }}: {{ $product->name }}">
                    </li>
                  @endforeach
                    @endif
                </ul>
                <button class="general-button js-image-button-next @if(isset($product->images) && is_countable($product->images) && !count($product->images)) disabled @endif">
                    <span class="icon-arrow-right"></span>
                </button>
            </div>
        </div>
        <div class="product-page__info-wrapper">
            <div class="product-page__info-header">
                <div class="product-page__rating">
                    @if($product->true_rating)
                    <p class="product__rating">{{ $product->true_rating }}</p>
                    <p>(<span>{{ $product->old_rating_count }}</span> {{ __('main.оценок') }})</p>
                    @else
                    <p class="product__rating empty">0</p>
                    @endif
                </div>
                <div class="product-page__rating">
                    @if($product->top_rating)
                    <a href="{{ $product->link . '/rating' }}" class="product__rating">{{ $rating_position }} <span>{{ __('main.место') }}</span></a>
                    @endif
                </div>
                <div class="product-page__status ts-2024-07-29-ln-177">

                    @if($product->is_frozen)
                        <p class="product__status">{{ __('main.Заморожено') }}</p>
                    @endif

                    @if($product->status)
                        <p class="product__status build">{{ __('main.product_statuses.' . $product->status) }}</p>
                    @endif

                    @if($product->is_sold)
                        <p class="product__status completed is_sold sold product__status_double">{{ __('main.product_statuses.sold') }}</p>
                    @endif

                </div>
            </div>
            <div class="ts-product-app-blade product-page__info-body">

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

                    <button class="product-page__button product-page__button-help filter-button js-button" data-target="questions">{{ __('main.Задать вопрос') }}</button>
                    <button class="product-page__button catalog__filter-button product-page__button-meeting js-button" data-target="meeting">{{ __('main.Назначить визит') }}</button>
                </div>

                <div class="wrapper">
                    @if($product->brand)
                    <div class="product-page__info-name">
                        <span>{{ __('main.Проект от') }}:</span>
                        <h5><a href="{{ $product->brand->link }}">{{ $product->brand->name }}</a></h5>
                    </div>
                    @endif
                    @if($product->showPrice)
                        @php
                            $common_modifications = $product->category_id == 1 || $product->category_id == 6? $product->modifications()
                                                    ->where('modifications.is_default', 0)
                                                    ->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')
                                                    ->where('attribute_modification.attribute_id', 1)
                                                    ->whereJsonDoesntContain('attribute_modification.value', 'Земельный участок')
                                                    ->select('modifications.*') :
                            $product->modifications()
                                                    ->where('modifications.is_default', 0);
                        @endphp
                        @if($common_modifications->count() && $common_modifications->where('price', '!=', 0)->min('price'))
                        <div class="product-page__price">
                            <p>{{ $common_modifications->where('price', '!=', 0)->min('price') }}
                            @if($common_modifications->where('old_price', '!=', 0)->max('old_price') && $common_modifications->where('price', '!=', 0)->min('price') != $common_modifications->where('old_price', '!=', 0)->max('old_price'))
                            {{ '- ' . $common_modifications->max('old_price') }}
                            @elseif($common_modifications->where('price', '!=', 0)->min('price') != $common_modifications->where('price', '!=', 0)->max('price'))
                            {{ '- ' . $common_modifications->max('price') }}
                            @endif
                            </p>
                            <span>грн/кв.м</span>
                        </div>
                        @endif

                        @php
                            $plot_modifications = $product->modifications()
                                                    ->where('modifications.is_default', 0)
                                                    ->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')
                                                    ->where('attribute_modification.attribute_id', 1)
                                                    ->whereJsonContains('attribute_modification.value', 'Земельный участок')
                                                    ->select('modifications.*');
                        @endphp
                        @if($plot_modifications->count())
                        <div class="product-page__price">
                            <p>{{ $plot_modifications->where('price', '!=', 0)->min('price') }}
                            @if($plot_modifications->where('old_price', '!=', 0)->max('old_price') && $plot_modifications->where('price', '!=', 0)->min('price') != $plot_modifications->where('old_price', '!=', 0)->max('old_price'))
                            {{ '- ' . $plot_modifications->max('old_price') }}
                            @elseif($plot_modifications->where('price', '!=', 0)->min('price') != $plot_modifications->where('price', '!=', 0)->max('price'))
                            {{ '- ' . $plot_modifications->max('price') }}
                            @endif
                            </p>
                            <span>грн/сот</span>
                        </div>
                        @endif


                    @endif
                </div>

                <div class="ts-to-remove-ln-269 product-page__buttons">
                    <!-- button class="product-page__button product-page__button-help filter-button js-button" data-target="questions">{{ __('main.Задать вопрос') }}</button -->
                    @if(!$product->is_sold && !$product->is_frozen && $product->status != 'project' && $product->status != 'sold')
                    <!-- <button class="product-page__button catalog__filter-button product-page__button-meeting js-button" data-target="meeting">{{ __('main.Назначить визит') }}</button> -->
                    @endif
                </div>

            </div>
            <div class="product-page__info-footer">
                <div class="product-page__address">
                    <p>{{ $product->region !== $product->city? $product->region : '' }}{{ $product->area && $product->area !== $product->city? ', ' . $product->area : '' }}</p>
                    <strong>{{ $product->city }}{{ $product->address_string? ', ' . $product->address_string : '' }}</strong>
                    <p class="procut__distance">
                        <span class="icon-city-variant-outline"></span>
                        <span>{{ $product->distance }} <span>км</span></span>
                    </p>
                    <span class="icon-place-big"></span>
                </div>
                <div class="general-social__wrapper">
                    <h5>Поделиться ссылкой:</h5>
                    <ul class="general__socila-list">
                        <li class="general__social-item">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ request()->url() }}" target="_blank" class="general__social-link social-facebook" title="Facebook" rel="nofollow">
                                <span class="icon-facebook"></span>
                            </a>
                        </li>
                        <li class="general__social-item">
                            <a href="https://twitter.com/intent/tweet?text={{ $product->name }}&url={{ request()->url() }}" target="_blank" class="general__social-link social-twitter" title="Twitter" rel="nofollow">
                                <span class="icon-twitter"></span>
                            </a>
                        </li>
                        <li class="general__social-item">
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ request()->url() }}" target="_blank" class="general__social-link social-linkedin" title="Linkedin" rel="nofollow">
                                <span class="icon-linkedin"></span>
                            </a>
                        </li>
                        <li class="general__social-item">
                            <a href="https://telegram.me/share/url?url={{ request()->url() }}&text={{ $product->name }}" target="_blank" class="general__social-link social-telegram" title="Telegram" rel="nofollow">
                                <span class="icon-telegram"></span>
                            </a>
                        </li>
                        <li class="general__social-item copy">
                            <input type="text" value="{{ request()->url() }}">
                            <a href="#" title="{{ __('main.Скопировать ссылку') }}" class="general__social-link social-link" rel="nofollow">
                                <span class="icon-link-variant"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@else
<div class="product-page-tabs">
    <div class="general-sitebar general-sitebar-tabs">
        @if($tab != 'map')
        <div class="general__map" id="general__map">
            <button class="general__open" title="{{ __('main.На весь экран') }}">
                <span class="icon-full"></span>
            </button>
        </div>
        @endif
        <div class="general-voting" v-if="poll" v-cloak>
            <h5>@{{ poll.title }}</h5>
            <div class="general-voting__form">
                <label class="checkbox__wrapper" v-for="option in poll.options">
                    <input :type="poll.is_multiple? 'checkbox' : 'radio'" class="input-checkbox" name="poll" :value="option.original_id? option.original_id : option.id" v-model="selectedAnswers"  v-if="!poll_voted">
                    <span class="custome-checkbox" v-if="!poll_voted">
                        <span class="icon-active"></span>
                    </span>
                    <span class="checkbox-text">@{{ option.title }} @{{ poll_voted? '- ' + answerVotes(option) : '' }}</span>
                </label>
                <button class="main-button disabled" @click="vote()" v-if="!poll_voted">{{ __('main.Проголосовать') }}</button>
            </div>
        </div>
    </div>
    <div class="product-page-tabs__wrapper container">
        <div class="product-page-tabs__header">
            <div class="product-page-tabs__header-img">
                <img v-lazy="'{{ $product->image? url('common/' . $product->image . '?w=600&q=90') : url('files/47/net-fot500x500.jpg') }}'" title="{{ __('main.Картинка') }}: {{ $product->name }}" alt="{{ __('main.Фото') }}: {{ $product->name }}">
            </div>
            <div class="product-page-tabs__header-info">
                <div class="wrapper">
                    <h1 class="ts-product-layouts-app-blade-ln-352 main-caption-l main-caption-l--transform">{{ $h1 }}</h1>
                    <div class="general-noty__buttons-container">
                        <button class="general-noty__button general-noty__button-favorite" @click="addToFavorites({{ $product }}, 'products')" :class="{active: favorites['products'].includes({{ $product->id }}) || favorites['products'].includes({{ $product->original_id }})}" title="{{ __('main.Добавить в избранное') }}">
                            <span class="icon-heart-outline"></span>
                        </button>
                    </div>
                </div>
                <div class="info">
                    <p class="name">{{ $product->category->name }}</p>
                    @if($product->brand)
                    <div class="project">
                        <span>{{ __('main.Проект от') }}:</span>
                        <h5>{{ $product->brand->name }}</h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>
@endif
<div class="general-tabs">
    <ul class="general-tabs__list general-tabs__list-product">
        <li class="general-tabs__item @if(!$tab) active @endif"><a href="{{ $product->link }}">{{ $product->category_id == 1 || $product->category_id == 6? __('main.О городке') : __('main.Про комплекс') }}</a></li>
        <li class="general-tabs__item @if($tab == 'projects') active @endif @if(!$product->notBaseModifications->count()) disabled @endif"><a href="{{ $product->link . '/projects' }}">{{ __('main.Типовые проекты') }}</a></li>
        <li class="general-tabs__item @if($tab == 'map') active @endif"><a href="{{ $product->link . '/map' }}">{{ __('main.Карта') }}</a></li>
        <li class="general-tabs__item @if($tab == 'video') active @endif"><a href="{{ $product->link . '/video' }}">{{ __('main.Видео') }}</a></li>
        <li class="general-tabs__item @if($tab == 'reviews') active @endif"><a href="{{ $product->link . '/reviews' }}">{{ __('main.Отзывы') }}</a></li>
        <li class="general-tabs__item @if($tab == 'promotions') active @endif"><a href="{{ $product->link . '/promotions' }}">{{ __('main.Акции') }}</a></li>
        <li class="general-tabs__item general-tabs__item-empty"></li>
    </ul>
</div>

@yield('product_content')
        <div class="general-voting general-voting-tablet" v-if="poll" v-cloak>
            <h5>@{{ poll.title }}</h5>
            <div class="general-voting__form">
                <div class="wrapper">
                    <label class="checkbox__wrapper" v-for="option in poll.options">
                        <input :type="poll.is_multiple? 'checkbox' : 'radio'" class="input-checkbox" name="poll" :value="option.original_id? option.original_id : option.id" v-model="selectedAnswers" v-if="!poll_voted">
                        <span class="custome-checkbox" v-if="!poll_voted">
                            <span class="icon-active"></span>
                        </span>
                        <span class="checkbox-text">@{{ option.title }} @{{ poll_voted? '- ' + answerVotes(option) : '' }}</span>
                    </label>
                    <button class="main-button disabled" @click="vote()" v-if="!poll_voted">{{ __('main.Проголосовать') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@yield('product_after_content')

<!-- <section class="seo-block seo-block-product-page">
    <div class="seo-block__wrapper container">
        <h2 class="main-caption-l">Коттеджные городки и поселки Украины на Zagorodna.com</h2>
        <div class="seo-block__content">
            <div class="wrapper">
                <p>Таунхаус в Киеве для себя и родителей. Дуплекс в пригороде. Коттедж под Киевом малоэтажный на 2 семьи. Купить дачу под Киевом. Купить дом в Украине. Вилла на побережье... Кто не мечтает жить там, где плещется море, шумят реликтовые сосны и можно не вспоминать о суматохе городских "джунглей"?! Каждый! База (каталог) коттеджных городков и поселков Украины, собранная на нашем портале, то, что вам нужно! У нас все функционально, доступно, просто и... в полном объеме! Коттеджные городки Киевской области, построенные коттеджные городки под Киевом, строящиеся и проектируемые коттеджные поселки во всех регионах Украины - к вашим услугам! Позитивная энергетика, или смена места жительства на пользу.
                </p>
                <p>
                Коттеджный городок под Киевом в начале 2000-ных привлекал каждого. КПП на въезде, круглосуточная охрана, водоем в центре коттеджных поселков или на каждом участке, система "умный дом", богатая социальная инфраструктура и доступность транспорта, перспективы жить в однородном окружении манили многих. Но со временем вкусы менялись, время шло. Коттеджные городки Одессы (с участками на первой и последующих береговых линиях), коттеджные поселки во Львове (в историческом центре) и на востоке страны стали не менее привлекательными. Коттеджные поселки Крыма чего стоят! Купить дом в коттеджном поселке под Севастополем или Ялтой - значит позволить себе жить у вечно плещущейся воды, наслаждаться местными красотами, теплым климатом,
                </p>стройными кипарисами. Земля под Киевом не нужна! Мы лучше купим дом под Киевом, дачу под Киевом или любую недвижимость в Киевской области. Бесконечные предложения от портала: купить дом/продать дачу легко…
                Портал загородной недвижимости предлагает сотни коттеджных городков от застройщиков, крупных девелоперов со всей территории Украины. Они предлагают купить дом в Украине! На любой вкус и финансовые возможности. Изучите варианты при помощи удобной формы поиска, рассмотрите фото, обратите внимание на параметры, свяжитесь с продавцом для уточнения деталей. Первый раз совершаете покупку дома в коттеджном поселке под Киевом? Цены в коттеджных городках под Киевом вас не пугают, но вы не знаете, как должен правильно выглядеть договор купли-продажи, какие госпошлины и налоги стоит уплатить, какие справки собрать? Сотрудники нашего портала помогут вам в любом вопросе, проведя профессиональную консультацию.
                <p>Недвижимость за городом можете приобретать, не раздумывая, выбрав интересный вариант в интересующем вас городе страны. Купить дачу под Киевом, или недвижимость в Киевской области не составит труда! Продать-купить коттедж в коттеджном городке или земельный участок в коттеджном поселке с нашей помощью - легко. Для продажи собственного коттеджа (в связи со сменой работы, места учебы детей, прочими нюансами) нужно заполнить специальную форму, все информационные поля. Покупатель оценит не только техническую информацию, нюансы, касающиеся вашего объекта недвижимости, но и цену на коттедж. К вашим услугам наши профессионалы, которые помогут правильно оценить дом или участок с тем, чтобы не прогадать при совершении сделки. Продажа дома под Киевом с нашей помощью - быстрый и эффективный результат! Недвижимость в Киевской области, или коттеджный городок под Киевом Коттеджный городок с развлекательным центром, ресторанным комплексом, конной школой и множеством спортивных секций, медицинскими учреждениями
                </p>
                <p>это мини-город в городе. Коттеджный поселок под Киевом, выстроенный вдали от промзон и проезжих трасс, привлечет внимание неспешным ритмом жизни истинных консерваторов. Или новаторов, стремящихся попробовать нечто новое. Коттеджные городки Киевской области - вот "заповедная" территория для тех, кто желает проводить свободное время вдали от бизнес-центра, суматохи и вечных проблем. Коттеджи под Киевом пользуются стабильным спросом. Купить дом под Киевом - это значит очертить круг собственного комфорта, желаний и возможностей! Недвижимость в Киевской области всегда пользовалась повышенной популярностью. Это связано с большим потоком смелых и креативных, приезжающих покорять столицу. Земельные участки в коттеджных городках - вот предел мечтаний многих, тех, кто желает выстроить дом по собственному эскизу. Дома под Киевом не привлекает полной готовностью, зачастую большим спросом пользуются те, где еще можно проявить фантазию. С нашим порталом найти любую недвижимость за городом просто! Источник: © Zagorodna.com</p>
            </div>

        </div>
    </div>
</section> -->
<section class="popup @error('question_email') active @enderror" data-target="questions" id="question">
    <div class="popup__wrapper popup-call-back">
        <button class="close-popup js-close close-popup-inner">
            <span class="decor"></span>
        </button>
        <h5 class="popup-caption">{{ __('main.Задайте ваш вопрос по наличию и ценам') }}</h5>
        <form action="{{ url('feedback/create/question') }}" method="post" class="popup-call-back__form">
            @csrf
            <label class="input__wrapper @error('question_name') error @enderror">
                <span class="input__caption">{{ __('main.Имя') }}</span>
                <input type="text" class="main-input" name="question_name" value="{{ old('question_name') }}" placeholder="{{ __('forms.placeholders.Как к вам обращаться?') }}?">
                @error('question_name')
                    <span class="error-text" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </label>
            <label class="input__wrapper @error('question_phone') error @enderror">
                <span class="input__caption">{{ __('main.Контактный телефон') }}*</span>
                <input type="tel" class="main-input" name="question_phone" value="{{ old('question_phone') }}" placeholder="{{ __('forms.placeholders.Номер телефона') }}">
                @error('question_phone')
                    <span class="error-text" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </label>
            <label class="input__wrapper @error('question_emails') error @enderror">
                <span class="input__caption">Email*</span>
                <input type="email" class="main-input" name="question_email" value="{{ old('question_email') }}" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}">
                @error('question_email')
                    <span class="error-text" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </label>
            <label class="textarea__wrapper">
                <span class="input__caption">{{ __('main.Какой проект вам нужен?') }}</span>
                <textarea class="main-textarea" name="question_text" placeholder="{{ __('forms.placeholders.Напишите дополнительно') }}">{{ old('question_text')? old('question_text') : $product->name }}</textarea>
            </label>
            <button class="main-button">{{ __('main.Отправить') }}</button>
        </form>
    </div>
</section>
<!-- <section class="popup @error('selection_email') active @enderror" data-target="help">
    <div class="popup__wrapper popup-call-back">
        <button class="close-popup js-close close-popup-inner">
            <span class="decor"></span>
        </button>
        <h5 class="popup-caption">{{ __('main.Поможем подобрать дом в нашем городке') }}</h5>
        <form action="{{ url('feedback/create/selection') }}" method="post" class="popup-call-back__form">
        @csrf
            <label class="input__wrapper @error('selection_name') error @enderror">
                <span class="input__caption">{{ __('main.Имя') }}</span>
                <input type="text" class="main-input" name="selection_name" value="{{ old('selection_name') }}" placeholder="{{ __('forms.placeholders.Как к вам обращаться?') }}?">
                @error('selection_name')
                    <span class="error-text" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </label>
            <label class="input__wrapper @error('selection_phone') error @enderror">
                <span class="input__caption">{{ __('main.Контактный телефон') }}*</span>
                <input type="tel" class="main-input" name="selection_phone" value="{{ old('selection_phone') }}" placeholder="{{ __('forms.placeholders.Номер телефона') }}">
                @error('selection_phone')
                    <span class="error-text" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </label>
            <label class="input__wrapper @error('selection_emails') error @enderror">
                <span class="input__caption">Email*</span>
                <input type="email" class="main-input" name="selection_email" value="{{ old('selection_email') }}" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}">
                @error('selection_email')
                    <span class="error-text" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </label>
            <label class="textarea__wrapper">
                <span class="input__caption">{{ __('main.Какой проект вам нужен?') }}</span>
                <textarea class="main-textarea" name="selection_text" placeholder="{{ __('forms.placeholders.Напишите дополнительно') }}">{{ old('selection_text')? old('selection_text') : $product->name }}</textarea>
            </label>
            <button class="main-button">{{ __('main.Отправить') }}</button>
        </form>
    </div>
</section> -->
<section class="popup @error('visit_email') active @enderror" data-target="meeting">
    <div class="popup__wrapper popup-call-back">
        <button class="close-popup js-close close-popup-inner">
            <span class="decor"></span>
        </button>
        <h5 class="popup-caption">{{ __('main.Назначить визит в отдел продаж') }}</h5>
        <form action="{{ url('feedback/create/visit') }}" method="post" class="popup-call-back__form">
                @csrf
                <label class="input__wrapper">
                    <span class="input__caption">{{ __('main.Имя') }}</span>
                    <input type="text" class="main-input" placeholder="{{ __('forms.placeholders.Как к вам обращаться?') }}" name="visit_name" value="{{ old('visit_name') }}">
                    @error('visit_name')
                        <span class="error-text" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                </label>
                <div class="popup-call-back__meeting-wrapper">
                    <label class="input__wrapper">
                        <span class="input__caption">{{ __('main.Контактный телефон') }}*</span>
                        <input type="tel" class="main-input" placeholder="{{ __('forms.placeholders.Номер телефона') }}" name="visit_phone" value="{{ old('visit_phone') }}">
                        @error('visit_phone')
                            <span class="error-text" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>
                    <label class="input__wrapper">
                        <span class="input__caption">Email*</span>
                        <input type="email" class="main-input" placeholder="{{ __('forms.placeholders.Ваш электронный адрес') }}" name="visit_email" value="{{ old('visit_email') }}">
                        @error('visit_email')
                            <span class="error-text" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>
                </div>
                <label class="input__wrapper">
                    <span class="input__caption">{{ __('main.Дата визита') }}*</span>
                    <input type="date" class="main-input" name="visit_extras[Дата визита]" value="{{ old('visit_extras')['Дата визита'] }}" placeholder="{{ __('main.Удобная вам дата визита') }}">
                </label>
                <label class="textarea__wrapper">
                    <span class="input__caption">{{ __('main.Какой проект вам нужен?') }}</span>
                    <textarea class="main-textarea" name="visit_text">{{ old('visit_text')? old('visit_text') : $product->name }}</textarea>
                </label>
                <button class="main-button">{{ __('main.Отправить') }}</button>
            </form>
    </div>
</section>
<section class="popup popup-full-map" data-target="full-map">
        <div class="popup__wrapper popup__wrapper--map js-container-map">
            <button class="close-popup js-close close-popup-inner">
                <span class="decor"></span>
            </button>
            <div id="general__map_popup" style="height:500px"></div>
        </div>
    </section>
</main>
@endsection

<script>
var poll = @json($poll);
var poll_voted = @json($poll_voted);
var poll_answers = @json($poll_answers);
var product_original_id = @json($product->original_id? $product->original_id : $product->id);
var pollSuccess = "{{ __('main.Спасибо, Ваш голос учтён') }}!"
</script>

@if($product->lat && $product->lng)
@push('styles')
<!-- link href='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.css' rel='stylesheet' / -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />
<style>
    .product-page__general-img img[lazy="loading"],
    .product-page__img-item img[lazy="loading"],
    .product-page-tabs__header-img img[lazy="loading"] {
        height: 1px;
        width: auto;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
</style>
@endpush

@push('scripts')
<!-- script src='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js'></script -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>

<script>
    mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';

document.map = new mapboxgl.Map({
  language: '{{ $lang }}',
  container: 'general__map',
  style: 'mapbox://styles/mapbox/streets-v11',
  center: [{{ $product->lng }}, {{ $product->lat }}],
  zoom: 11,
  minZoom: 3.7
});

var marker = new mapboxgl.Marker()
.setLngLat([{{ $product->lng }}, {{ $product->lat }}])
.setPopup(new mapboxgl.Popup().setText('{{ $product->name }}'))
.addTo(document.map);

currentMarkers.push(marker);

document.map.on('load', function() {
    document.map.getStyle().layers.forEach(function(thisLayer){
        if(thisLayer.type == 'symbol'){
            document.map.setLayoutProperty(thisLayer.id, 'text-field', ['get','name_ru'])
        }
    });
});
</script>
@endpush
@endif