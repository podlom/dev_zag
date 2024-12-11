<header class="header">
    <div class="header__top">
        <!-- <div class="header__city general-drop js-drop-item">
            <p class="header__city__wrapper js-drop-button general-drop__text">
                <span class="icon-place"></span>
                <span class="header__city__text">{{ $selectedRegion? $selectedRegion->name : __('main.Вся Украина') }}</span>
                <span class="icon-drop"></span>
            </p>
            <div class="general-drop__container">
                <form action="{{ route('setRegion') }}" method="post" class="general-drop__wrapper" id="region-form">
                    @csrf
                    <ul class="general-drop__list">
                        <li class="general-drop__item @if(!$selectedRegion) active @endif">
                            <label onclick="document.querySelector('#region-form').submit()"><input type="radio" name="selectedRegion" value="">{{ __('main.Вся Украина') }}</label>
                        </li>
                        @foreach($allRegions as $slug => $region)
                        <li class="general-drop__item @if($selectedRegion && $selectedRegion->slug == $slug) active @endif">
                            <label onclick="document.querySelector('#region-form').submit()"><input type="radio" name="selectedRegion" value="{{ $slug }}">{{ $region }}</label>
                        </li>
                        @endforeach
                    </ul>
                </form>
            </div>
        </div> -->
        <div class="header__search header__search-tablet">
            <form action="#" class="header__form">
                <label class="input__wrapper">
                    <input type="text" class="main-input" placeholder="{{ __('main.Что искать') }}?" v-model="search_filter">
                    <span class="header__search-button">
                        <span class="icon-search"></span>
                    </span>
                </label>
            </form>
        </div>
        <div class="header__navigation">
            <button class="header-burger">
                <span class="header-burger__decor"></span>
                <span class="header-burger__decor"></span>
            </button>
            <button class="header__button-search">
                <span class="icon-search"></span>
                <span class="decor"></span>
            </button>
            <a href="#" class="header__button header__button-general">
                <span class="icon-house"></span>
            </a>
            <div class="header__buttons__wrapper header__buttons__wrapper-mobile">
                  <a href="{{ route($lang . '_favorite') }}" class="header__button header__button-favorite" @click="validateFav($event)" :class="{active: totalFavorites}">
                    <span class="icon-heart-outline"></span>
                </a>
                <button class="header__button header__button-noty js-noty-button">
                    <span class="icon-bell-outline"></span>
                    <span class="header__button-noty-decor"></span>
                </button>
            </div>
        </div>
        <nav class="header__nav">
            <ul class="header__nav-list">
                @foreach($headerMenu->children->sortBy('lft') as $key => $menuItem)
                @if($menuItem->children->count())
                <li class="header__nav-item js-drop-item">
                    <button class="js-drop-button">
                        <span class="header__nav-tablet-icon">
                        @include('includes.icons.header-icon-' . (($key < 5)?($key + 1) : 7))
                        </span>
                        <span class="header__nav-item-text">{{ $menuItem->name }}</span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="general-drop__wrapper">
                        <ul class="general-drop__list">
                            @foreach($menuItem->children->sortBy('lft') as $childItem)
                            <li class="general-drop__item">
                                <a href="{{ $childItem->url() }}" class="general-drop__link">{{ $childItem->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
                @else
                <li class="header__nav-item">
                    <a href="{{ $menuItem->url() }}" class="header__nav-link">{{ $menuItem->name }}</a>
                </li>
                @endif
                @endforeach
            </ul>
            <div class="header__nav__buttons">
                <button class="button-pick-up js-button" data-target="free_selection">
                    <span class="icon-house"></span>
                    <span>{{ __('main.Подобрать бесплатно') }}</span>
                </button>
                <div class="header__change-lang general-drop js-drop-item">
                    <button class="js-drop-button">
                        <span class="header__change-lang__text">{{ $lang == 'ru' ? 'Рус' : 'Укр' }}</span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="general-drop__wrapper">
                        <ul class="general-drop__list">
                            <li class="general-drop__item">
                            @if(request()->getUri() === url('uk'))
                                <a href="{{ url('') }}" class="general-drop__link">Рус</a>
                            @elseif(isset($translation_link) && $lang != 'ru')
                                <a href="{{ $translation_link }}" class="general-drop__link">Рус</a>
                            @elseif(isset($translation_link))
                                <a href="{{ $translation_link }}" class="general-drop__link">Укр</a>
                            @elseif($lang != 'ru')
                                <a href="{{ url( 'ru/'.substr(\Request::path(),3) ) }}" class="general-drop__link">Рус</a>
                            @else
                                <a href="{{ url('uk/'.substr(\Request::path(),3)) }}" class="general-drop__link">Укр</a>
                            @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div class="header__change-lang general-drop">
            <span class="header__change-lang__text">{{ $lang == 'ru' ? 'Рус' : 'Укр' }}</span>
            <span class="icon-drop"></span>
            <div class="general-drop__wrapper">
                <ul class="general-drop__list">
                    <li class="general-drop__item">
                    @if(request()->getUri() === url('uk'))
                        <a href="{{ url('') }}" class="general-drop__link">Рус</a>
                    @elseif(isset($translation_link) && $lang != 'ru')
                        <a href="{{ $translation_link }}" class="general-drop__link">Рус</a>
                    @elseif(isset($translation_link))
                        <a href="{{ $translation_link }}" class="general-drop__link">Укр</a>
                    @elseif($lang != 'ru')
                        <a href="{{ url( 'ru/'.substr(\Request::path(),3) ) }}" class="general-drop__link">Рус</a>
                    @else
                        <a href="{{ url('uk/'.substr(\Request::path(),3)) }}" class="general-drop__link">Укр</a>
                    @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header__dow">
        <a href="{{ $lang === 'ru'? url('') : route($lang . '_home') }}" class="header__logo" @if(request()->getUri() === url('') . '/'  || request()->getUri() == url($lang)) style="pointer-events: none;" @endif>
            @include('includes.logo')
            <p class="header__logo__text">{{ __('main.Портал загородной недвижимости №1') }} <br> {{ __('main.в Украине') }}</p>
        </a>
        <ul class="header__list">
            <li class="header__item">
                <a href="{{ route($lang . '_promotions') }}" class="header__link">
                    <span class="header__link-img"></span>
                    <span class="header__link-text">{{ __('main.Акции') }}</span>
                </a>
            </li>
            <li class="header__item header__item--drop">
                <!-- <a href="{{-- route($lang . '_precatalog', $selectedRegion? 'kottedzhnye-gorodki-poselki-region=' . $selectedRegion->slug : 'kottedzhnye-gorodki-poselki-ukrainy') --}}" class="header__link"> -->
                <a href="{{ route($lang . '_precatalog', $cottage_slug) }}" class="header__link">
                
                    <span class="header__link-img"></span>
                    <span class="header__link-text">{{ __('main.Коттеджные городки') }}</span>
                </a>
                <div class="general-drop__wrapper">
                    <ul class="general-drop__list">
                        <li class="general-drop__item">
                            <a href="{{ route($lang . '_precatalog', $cottage_slug) . '/catalog' }}" class="general-drop__link">{{ __('main.Каталог') }} {{ mb_strtolower(__('main.type_cottage_plural_genitive')) }}</a></li> 
                        <li class="general-drop__item">
                            <a href="{{ route($lang . '_map', $cottage_slug) }}" class="general-drop__link">{{ __('main.Карта') }} {{ mb_strtolower(__('main.type_cottage_plural_genitive')) }}</a>
                        </li>
                        <!-- <li class="general-drop__item">
                            <a href="#" class="general-drop__link">{{ __('main.Застройщики') }} {{ mb_strtolower(__('main.type_cottage_plural_genitive')) }}</a>
                        </li> -->
                    </ul>
                </div>
            </li>
            <li class="header__item header__item--drop">
                <!-- <a href="{{-- route($lang . '_precatalog', $selectedRegion && $selectedRegion->region_id != 29? 'novostrojki-prigoroda-region=' . $selectedRegion->slug : 'novostrojki-prigoroda') --}}" class="header__link"> -->
                <a href="{{ route($lang . '_precatalog', $newbuild_slug) }}" class="header__link" >
                    <span class="header__link-img"></span>
                    <span class="header__link-text">{{ __('main.Новостройки') }}</span>
                </a>
                <div class="general-drop__wrapper">
                    <ul class="general-drop__list">
                        <li class="general-drop__item">
                            <a href="{{ route($lang . '_precatalog', $newbuild_slug) . '/catalog' }}" class="general-drop__link">{{ __('main.Каталог') }} {{ mb_strtolower(__('main.type_newbuild_plural_genitive')) }} {{ __('main.в пригороде') }}</a></li> 
                        <li class="general-drop__item">
                            <a href="{{ route($lang . '_map', $newbuild_slug) }}" class="general-drop__link">{{ __('main.Карта') }} {{ mb_strtolower(__('main.type_newbuild_plural_genitive')) }} {{ __('main.в пригороде') }}</a>
                        </li>
                        <!-- <li class="general-drop__item">
                            <a href="#" class="general-drop__link">{{ __('main.Застройщики') }} {{ mb_strtolower(__('main.type_newbuild_plural_genitive')) }} {{ __('main.в пригороде') }}</a>
                        </li> -->
                    </ul>
                </div>
            </li>
            <li class="header__item">
                <a href="{{ route($lang . '_companies') }}" class="header__link">
                    <span class="header__link-img"></span>
                    <span class="header__link-text">{{ __('main.Компании и услуги') }}</span>
                </a>
            </li>
        </ul>
        <div class="header__search">
            <div class="header__form">
                <label class="input__wrapper">
                    <input type="text" class="main-input" placeholder="{{ __('main.Что искать') }}?" v-model="search_filter">
                    <span class="header__search-button">
                        <span class="icon-search"></span>
                    </span>
                </label>
            </div>
            <div class="header__livesearch" :class="{active: search_filter}">
                <div class="header__livesearch__header">
                    <div class="general-tabs">
                        <ul class="general-tabs__list">
                            <li class="general-tabs__item" @click="search_type = 'cottages'" :class="{active: search_type == 'cottages'}" @click="search_type = 'cottages'" :class="{active: search_type == 'cottages'}">{{ __('main.Коттеджи') }}</li>
                            <li class="general-tabs__item" @click="search_type = 'newbuilds'" :class="{active: search_type == 'newbuilds'}">{{ __('main.Новостройки') }}</li>
                            <li class="general-tabs__item" @click="search_type = 'promotions'" :class="{active: search_type == 'promotions'}">{{ __('main.Акции') }}</li>
                            <li class="general-tabs__item" @click="search_type = 'companies'" :class="{active: search_type == 'companies'}">{{ __('main.Компании') }}</li>
                            <li class="general-tabs__item" @click="search_type = 'articles'" :class="{active: search_type == 'articles'}">{{ __('main.Журнал') }}</li>
                            <li class="general-tabs__item general-tabs__item-empty"></li>
                        </ul>
                    </div>
                </div>

                <div class="header__livesearch__body">
                    <ul class="header__livesearch__list"
                        v-if="search_list.length"
                        :class="{'header__livesearch__list-company': search_type_changed === 'companies',
                                 'header__livesearch__list-sale': search_type_changed === 'promotions',
                                 'header__livesearch__list-news': search_type_changed === 'articles'}" 
                    >
                        <li class="header__livesearch__item" v-if="search_type_changed === 'articles'">
                            <h4 class="header__livesearch__item__caption">{{ __('main.Новости') }}</h4>
                        </li>
                        <template v-for="item in search_list">
                            <li class="header__livesearch__item" v-if="search_type_changed === 'companies'">
                                <a :href="item.link" class="header__livesearch__item__link">
                                    <div class="header__livesearch__item__img" :style="{backgroundImage:'url(' + item.business_card + ')'}" style="background-size:contain;"></div>
                                    <div class="header__livesearch__item-info">
                                        <div class="header__livesearch__item__info__header">
                                            <h4>@{{ item.name }}</h4>
                                        </div>
                                        <div class="header__livesearch__item__info__body">
                                            <p class="name">@{{ item.category_name }}</p>
                                        </div>
                                        <div class="header__livesearch__item__info__footer">
                                            <p>@{{ item.contacts['site'] }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="header__livesearch__item" v-else-if="search_type_changed === 'cottages' || search_type_changed === 'newbuilds'">
                                <a :href="item.link" class="header__livesearch__item__link">
                                    <div class="header__livesearch__item__img" :style="{backgroundImage:'url(' + item.image + ')'}"></div>
                                    <div class="header__livesearch__item-info">
                                        <div class="header__livesearch__item__info__header">
                                            <h4>@{{ item.name }}</h4>
                                        </div>
                                        <div class="header__livesearch__item__info__body">
                                            <p class="name">@{{ item.type }}</p>
                                            <p>@{{ item.brand_name }}</p>
                                        </div>
                                        <div class="header__livesearch__item__info__footer">
                                            <p>@{{ item.city }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="header__livesearch__item" v-else-if="search_type_changed === 'promotions'">
                                <a :href="item.link" class="header__livesearch__item__link">
                                    <div class="header__livesearch__item__img" :style="{backgroundImage:'url(' + item.image + ')'}"></div>
                                    <div class="header__livesearch__item-info">
                                        <div class="header__livesearch__item__info__header">
                                            <p class="product__sale">@{{ item.title }}</p>
                                            <p class="date">@{{ item.start }} - @{{ item.end }}</p>
                                        </div>
                                        <div class="header__livesearch__item__info__body">
                                            <p class="name" v-html="item.desc"></p>
                                        </div>
                                        <div class="header__livesearch__item__info__footer">
                                            <p>@{{ item.product_name }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="header__livesearch__item" v-else-if="search_type_changed === 'articles' && (item.parent_category_id == 1 || item.parent_category_id == 14)">
                                <a :href="item.link" class="header__livesearch__item__link">
                                    <div class="header__livesearch__item-info">
                                        <div class="header__livesearch__item__info__header">
                                            <h5>@{{ item.title }}</h5>
                                        </div>
                                        <div class="header__livesearch__item__info__body">
                                            <p class="name">@{{ item.category }}</p>
                                        </div>
                                        <div class="header__livesearch__item__info__footer">
                                            <p>@{{ item.date }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </template>
                        <li class="header__livesearch__item" v-if="search_type_changed === 'articles'">
                            <h4 class="header__livesearch__item__caption">{{ __('main.Статьи') }}</h4>
                        </li>
                        <template v-for="item in search_list">
                            <li class="header__livesearch__item" v-if="search_type_changed === 'articles' && item.parent_category_id != 1 && item.parent_category_id != 14">
                                <a :href="item.link" class="header__livesearch__item__link">
                                    <div class="header__livesearch__item-info">
                                        <div class="header__livesearch__item__info__header">
                                            <h5>@{{ item.title }}</h5>
                                        </div>
                                        <div class="header__livesearch__item__info__body">
                                            <p class="name">@{{ item.category }}</p>
                                        </div>
                                        <div class="header__livesearch__item__info__footer">
                                            <p>@{{ item.date }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </template>
                    </ul>

                    <div class="header__livesearch__list" style="pointer-events:none;display:flex" v-else-if="search_preload">
                        <img src="{{ url('img/preload-for-files.gif') }}" style="display:block;margin:auto" alt="">
                    </div>

                    <div class="header__livesearch__list" style="pointer-events:none" v-else-if="search_filter.length < 3"><div class="header__livesearch__item"><div class="header__livesearch__item__link">{{ __('main.Введите минимум 3 символа') }}.</div></div></div>

                    <div class="header__livesearch__list" style="pointer-events:none" v-else><div class="header__livesearch__item"><div class="header__livesearch__item__link">{{ __('main.По вашему запросу ничего не найдено') }}.</div></div></div>
                </div>
                <!-- <div class="header__livesearch__footer">
                    <a href="#">{{ __('main.Все результаты') }}</a>
                </div> -->
            </div>
        </div>
        <div class="header__buttons__wrapper">
            <button class="header__general-button general-button-color js-button" data-target="free_selection">{{ __('main.Подобрать бесплатно') }}</button>
            <button class="header__button header__button-general js-button" data-target="free_selection" title="{{ __('main.Подобрать бесплатно') }}">
                <span class="icon-house"></span>
            </button>
            <a href="{{ route($lang . '_favorite') }}" class="header__button header__button-favorite" @click="validateFav($event)" :class="{active: totalFavorites}" title="{{ __('main.Избранное') }}">
                <span class="icon-heart-outline"></span>
            </a>
            <button class="header__button header__button-noty js-noty-button" title="{{ __('main.Уведомления') }}">
                <span class="icon-bell-outline"></span>
                <span class="header__button-noty-decor" v-if="hasNew"></span>
            </button>
        </div>
    </div>
    <div class="header__noty__wrapper js-noty-wrapper" v-cloak>
        <ul class="header__noty__list">
            <li class="header__noty__item" v-for="item in notificationsCollection" :class="{new: !isSeen(item)}" @mouseover="isSeen(item)? false : notificationsSeen.push(item.id)">
                <a :href="item.product_link" class="header__noty__img" :style="{backgroundImage:'url(' + item.product_image + ')'}">
                    <div class="header__noty__new"></div>
                </a>
                <div class="header__noty__info">
                    <div class="noty__info__header">
                        <p class="noty__info__name">@{{ item.product_type }}</p>
                        <p class="noty__info__name-company">@{{ item.brand_name }}</p>
                    </div>
                    <p class="noty__info__description">
                        <a :href="item.product_link">@{{ item.product_name }}</a>
                    </p>

                    <!-- PRICE -->
                    <div v-if="item.type === 'old'" class="noty__info__price">
                        <p>@{{ item.old_price }} грн/кв.м</p>
                        <template v-if="item.old_price != item.price">
                            <span class="icon-arrow-small"></span>
                            <p>@{{ item.price }} грн/кв.м</p>
                        </template>
                    </div>
                    <!-- END PRICE -->

                    <div class="noty__info__footer" v-if="item.type == 'new'">{{ __('main.Новый объект') }}</div>
                    <div class="noty__info__footer" v-else>
                        <p :class="{'completed': item.old_status == 'done', 'build': item.old_status == 'building'}">@{{ item.old_status_string }}</p>
                        <template v-if="item.old_status != item.status">
                            <span class="icon-arrow-small"></span>
                            <p :class="{'completed': item.status == 'done', 'build': item.status == 'building'}">@{{ item.status_string }}</p>
                        </template>
                    </div>
                </div>
            </li>
            <li class="header__noty__item empty" v-if="!totalNotifications">{{ __('main.Список уведомлений пуст') }}</li>
        </ul>
    </div>
</header>
<section class="push_notification">
    <ul class="push_notification__list">
        <!-- <button class="noty__cancel">Отменить</button> -->
    </ul>
</section>