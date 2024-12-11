<?php
    $start = microtime(true);
    $kyivdistrict_id = $kyivdistrict? $kyivdistrict->kyivdistrict_id : null;
    $city_id = $city? $city->city_id : null;
    $area_id = $area? $area->area_id : ($city? $city->area_id : null);
    $region_id = $region? $region->region_id : ($area? $area->region_id : ($city? $city->area->region_id : ($kyivdistrict? 29 : null)));
?>

<?php $__env->startSection('content'); ?>

<main>
    <div class="decor-background decor-background--pre-catalog" style="background-image:url(<?php echo e(url('image/background-img-1.png?q=60&fm=pjpg')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render('precatalog',
            $category,
            $region_id? \App\Region::where('region_id', $region_id)->first() : null,
            $area_id? \App\Area::where('area_id', $area_id)->first() : null,
            $city_id? \App\City::where('city_id', $city_id)->first() : null,
            $kyivdistrict_id? \App\Kyivdistrict::where('kyivdistrict_id', $kyivdistrict_id)->first() : null)); ?>

        </div>
    </section>
    <section class="pre-catalog-filter">
        <div class="catalog-filter__wrapper container">
            <form action="<?php echo e(route($lang . '_catalog', $category->slug)); ?>" method="get" class="catalog-filter__form">
                <label class="input__wrapper js-filter" data-target="filter">
                    <input type="hidden" name="address[region]" v-model="address.region" v-if="address.region">
                    <input type="hidden" name="address[city]" v-model="address.city" v-if="address.city">
                    <template v-if="address.area || address.kyivdistrict">
                        <input type="hidden" name="address[area]" v-model="address.area" v-if="address.region != 29">
                        <input type="hidden" name="address[kyivdistrict]" v-model="address.kyivdistrict" v-else>
                        <input type="hidden" name="latlng[lat]" v-model="latlng.lat">
                        <input type="hidden" name="latlng[lng]" v-model="latlng.lng">
                        <input type="hidden" name="radius" v-model="radius">
                    </template>
                    <input type="text" class="main-input" :value="fullAddress" placeholder="<?php echo e(__('main.Вся Украина')); ?>" readonly>
                    <span class="icon-place-big"></span>
                    <button type="button" class="button-distance js-filter" v-cloak data-target="distance" v-if="address.area">+{{ radius }} км</button>
                    <span class="line"></span>
                </label>
                <label class="input__wrapper">
                    <input type="text" class="main-input main-input-filter" name="filters[search_value]" placeholder="<?php echo e(__('forms.placeholders.КГ, адрес или компания')); ?>">
                </label>
                <button class="catalog__filter-button">
                    <span class="icon-search"></span>
                    <span class="filter-button__text"><?php echo e(__('main.искать в каталоге')); ?></span>
                </button>
            </form>
            <div class="catalog-filter__distance catalog-drop js-filter-drop" data-target="distance" v-if="address.area">
                <p class="caption"><?php echo e(__('main.Расстояние в радиусе')); ?>, км</p>
                <div class="range-slider">
                    <vue-slider v-model="radius"></vue-slider>
                </div>
            </div>
            <div class="catalog-filter__drop js-filter-drop catalog-drop" data-target="filter">
                <div class="wrapper active" :class="{'mobile-active': !address.region}">
                    <input type="text" placeholder="<?php echo e(__('main.Выберите область')); ?>" v-model="search.region" class="caption">
                    <div class="general-drop__container">
                        <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                <li class="general-drop__item" :class="{active: !address.region}" @click="address.region = ''">
                                    <span><?php echo e(__('main.Вся Украина')); ?></span>
                                    <span class="icon-drop"></span>
                                </li>
                                <template v-for="(region, key) in regions">
                                    <li class="general-drop__item"  :class="{active: address.region == key}" @click="address.region = key" v-if="region.toLowerCase().includes(search.region.toLowerCase())">
                                        <span>{{ region }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="wrapper" :class="{active: address.region, 'mobile-active': address.region && !address.area}">
                    <input type="text" placeholder="<?php echo e(__('main.Выберите район')); ?>" v-model="search.area" class="caption">
                    <div class="general-drop__container">
                        <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                <li class="general-drop__item" :class="{active: !address.area && !address.kyivdistrict, 'mobile-active': address.area || address.kyivdistrict}" @click="address.area = '', address.kyivdistrict = ''">
                                    <span><?php echo e(__('main.Все районы')); ?></span>
                                    <span class="icon-drop"></span>
                                </li>
                                <template v-for="(area, key) in areas">
                                    <li class="general-drop__item"  :class="{active: address.area == key || address.kyivdistrict == key}" @click="address.region == 29? address.kyivdistrict = key : address.area = key" v-if="area.toLowerCase().includes(search.area.toLowerCase())">
                                        <span>{{ area }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="wrapper" :class="{active: address.area && address.region != 29, 'mobile-active': address.area && address.region != 29}">
                    <input type="text" placeholder="<?php echo e(__('main.Выберите нас пункт')); ?>" v-model="search.city" class="caption">
                    <div class="general-drop__container">
                        <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                <li class="general-drop__item" :class="{active: !address.city}" @click="address.city = ''">
                                    <span><?php echo e(__('main.Все нас пункты')); ?></span>
                                    <span class="icon-drop"></span>
                                </li>
                                <template v-for="(city, key) in cities">
                                    <li class="general-drop__item"  :class="{active: address.city == key}" @click="address.city = key" v-if="city.toLowerCase().includes(search.city.toLowerCase())">
                                        <span>{{ city }}</span>
                                        <span class="icon-drop"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="filter-selected__wrapper">
                <h1 class="filter-selected__caption"><?php echo e($h1); ?></h1>
                <?php if($region || ($area && $area->is_center)): ?>
                <h5 class="filter-selected__sub-caption"><?php echo e(__('main.Районы')); ?></h5>
                <?php elseif(($area && !$area->is_center) || $city): ?>
                <h5 class="filter-selected__sub-caption"><?php echo e(__('main.Населенные пункты')); ?></h5>
                <?php else: ?>
                <h5 class="filter-selected__sub-caption"><?php echo e(__('main.Области')); ?></h5>
                <?php endif; ?>
                <div class="filter-selected__list-wrapper js-drop-item">
                    <?php if($region || $area || $city || $kyivdistrict): ?>
                    <?php
                        if($region || $kyivdistrict)
                            $link = route($lang . '_precatalog', $category->slug);
                        elseif($area)
                            $link = route($lang . '_precatalog', $category->slug) . '/region/' . $area->region->slug;
                        else {
                            $link = route($lang . '_precatalog', $category->slug) . '/area/' . $city->area->slug;
                        }

                        $link = $status? $link . '/' . \Str::slug($status) : $link;
                        $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                    ?>
                    <a href="<?php echo e($link); ?>" class="filter-selected__back general-button">
                        <span class="icon-arrow-more"></span>
                    </a>
                    <?php endif; ?>
                    <div class="filter-selected__list-container">
                        <ul class="filter-selected__list js-filter-selected">
                            <?php if(!$region  && !$area && !$city && !$kyivdistrict): ?>

                            <?php $__currentLoopData = $regions_collection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $cache_key = 'count_region:' . $item->region_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->region', $item->region_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            ?>

                            <?php if($prodCount): ?>
                            <?php
                                $link = route($lang . '_precatalog', $category->slug) . '/region/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                            ?>
                                <?php if($type === 'cottage' || $item->region_id != 29): ?>
                                <li class="filter-selected__item"><a href="<?php echo e($link); ?>"><?php echo e($item->name); ?></a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php elseif($kyivdistrict || ($region && $region->region_id === 29)): ?>

                            <?php $__currentLoopData = $kyivdistricts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $cache_key = 'count_kyivdistrict:' . $item->kyivdistrict_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->kyivdistrict', $item->kyivdistrict_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            ?>
                            <?php if($prodCount): ?>
                            <?php
                                $link = route($lang . '_precatalog', $category->slug) . '/kyivdistrict/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                            ?>
                                <li class="filter-selected__item <?php if($kyivdistrict && $kyivdistrict->kyivdistrict_id === $item->kyivdistrict_id): ?> active <?php endif; ?>"><a href="<?php echo e($link); ?>"><?php echo e($item->name); ?></a></li>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php elseif($region || ($area && $area->is_center)): ?>

                            <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $cache_key = 'count_area:' . $item->area_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->area', $item->area_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            ?>
                            <?php if($prodCount): ?>
                            <?php
                                $link = route($lang . '_precatalog', $category->slug) . '/area/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                            ?>
                            <li class="filter-selected__item <?php if($area && $area->area_id === $item->area_id): ?> active <?php endif; ?>"><a href="<?php echo e($link); ?>"><?php echo e($item->name); ?></a></li>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php elseif(($area && !$area->is_center) || $city): ?>

                            <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $cache_key = 'count_city:' . $item->city_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->city', $item->city_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            ?>
                            <?php if($prodCount): ?>
                            <?php
                                $link = route($lang . '_precatalog', $category->slug) . '/city/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;
                            ?>
                            <li class="filter-selected__item <?php if($city && $city->city_id === $item->city_id): ?> active <?php endif; ?>"><a href="<?php echo e($link); ?>"><?php echo e($item->name); ?></a></li>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="filter-selected__more js-filter-selected-more">
                        <button class="catalog__open-more js-drop-button"></button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="product product-pre-catalog">
        <form action="<?php echo e(route($lang . '_catalog', $category->slug)); ?>" method="get" class="product__wrapper product__wrapper-list-position container">
            <ul class="product__list" :style="{height: products.data.length? 'auto' : '300px'}">
                <template v-if="products.data.length">
                    <productcard v-for="(product, key) in products.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" alt="" v-else>
            </ul>

            <?php if($region_id): ?>
            <input type="hidden" name="address[region]" value="<?php echo e($region_id); ?>">
            <?php endif; ?>
            <?php if($city_id): ?>
            <input type="hidden" name="address[city]" value="<?php echo e($city_id); ?>">
            <?php endif; ?>
            <?php if($area_id): ?>
            <input type="hidden" name="address[area]" value="<?php echo e($area_id); ?>">
            <?php endif; ?>
            <?php if($kyivdistrict_id): ?>
            <input type="hidden" name="address[kyivdistrict]" value="<?php echo e($kyivdistrict_id); ?>">
            <?php endif; ?>
            <?php if($type == 'cottage' && $objectType): ?>
            <input type="hidden" name="filters[attributes][1][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
            <?php elseif($type == 'newbuild' && $objectType): ?>
            <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
            <?php endif; ?>
            <?php if($status): ?>
            <input type="hidden" name="filters[product_attributes][status][]" value="<?php echo e($status); ?>">
            <?php endif; ?>
            <button class="main-button-more product"><?php echo e(__('main.Смотреть каталог')); ?></button>
        </form>
    </section>

    <!-- section class="call-back" v-lazy:background-image="'<?php echo e(url('image/call-back-bg.png?q=60&fm=pjpg')); ?>'">
        <div class="call-back__wrapper container">
            <h4 class="call-back__caption"><?php echo e(__('main.Подписывайтесь на обновления по выбранному местоположению')); ?>!</h4>
            <form action="<?php echo e(route('subscribe')); ?>" method="post" class="call-back__form" id="subscription_product">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="subscription_type" value="product">
                <input type="hidden" name="subscription_region" v-model="address.region" v-if="!address.area">
                <input type="hidden" name="subscription_latlng[lat]" v-model="latlng.lat" v-if="address.area">
                <input type="hidden" name="subscription_latlng[lng]" v-model="latlng.lng" v-if="address.area">
                <div class="call-back__header">
                    <button class="js-filter callback-region-button" type="button" data-target="filter-callback">
                        <input type="hidden" :value="fullAddress" placeholder="<?php echo e(__('main.Вся Украина')); ?>">
                        <span class="icon-place-big"></span>
                        <p class="call-back__name" v-if="fullAddress">{{ fullAddress }}</p>
                        <p class="call-back__name" v-else><?php echo e(__('main.Вся Украина')); ?></p>
                    </button>
                    <template v-if="address.area">
                        <button type="button" class="button-distance js-filter" v-cloak data-target="distance-callback">+{{ radius }} км</button>
                        <div class="catalog-filter__distance catalog-drop js-filter-drop" data-target="distance-callback" >
                            <input type="hidden" name="subscription_radius" v-model="radius">
                            <p class="caption"><?php echo e(__('main.Расстояние в радиусе')); ?>, км</p>
                            <div  class="range-slider">
                                <vue-slider v-model="radius"></vue-slider>
                            </div>
                        </div>
                    </template>
                    <div class="catalog-filter__drop js-filter-drop catalog-drop callback-drop" data-target="filter-callback">
                    <div class="wrapper active" :class="{'mobile-active': !address.region}">
                        <input type="text" placeholder="<?php echo e(__('main.Выберите область')); ?>" v-model="search.region" class="caption">
                        <div class="general-drop__container">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item" :class="{active: !address.region}" @click="address.region = ''">
                                        <span><?php echo e(__('main.Вся Украина')); ?></span>
                                        <span class="icon-drop"></span>
                                    </li>
                                    <template v-for="(region, key) in regions">
                                        <li class="general-drop__item"  :class="{active: address.region == key}" @click="address.region = key" v-if="region.toLowerCase().includes(search.region.toLowerCase())">
                                            <span>{{ region }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="wrapper" :class="{active: address.region, 'mobile-active': address.region && !address.area}">
                        <input type="text" placeholder="<?php echo e(__('main.Выберите район')); ?>" v-model="search.area" class="caption">
                        <div class="general-drop__container">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item" :class="{active: !address.area && !address.kyivdistrict, 'mobile-active': address.area || address.kyivdistrict}" @click="address.area = '', address.kyivdistrict = ''">
                                        <span><?php echo e(__('main.Все районы')); ?></span>
                                        <span class="icon-drop"></span>
                                    </li>
                                    <template v-for="(area, key) in areas">
                                        <li class="general-drop__item"  :class="{active: address.area == key || address.kyivdistrict == key}" @click="address.region == 29? address.kyivdistrict = key : address.area = key" v-if="area.toLowerCase().includes(search.area.toLowerCase())">
                                            <span>{{ area }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="wrapper" :class="{active: address.area && address.region != 29, 'mobile-active': address.area && address.region != 29}">
                        <input type="text" placeholder="<?php echo e(__('main.Выберите нас пункт')); ?>" v-model="search.city" class="caption">
                        <div class="general-drop__container">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item" :class="{active: !address.city}" @click="address.city = ''">
                                        <span><?php echo e(__('main.Все нас пункты')); ?></span>
                                        <span class="icon-drop"></span>
                                    </li>
                                    <template v-for="(city, key) in cities">
                                        <li class="general-drop__item"  :class="{active: address.city == key}" @click="address.city = key" v-if="city.toLowerCase().includes(search.city.toLowerCase())">
                                            <span>{{ city }}</span>
                                            <span class="icon-drop"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="general-drop general-top__drop js-drop-item call-back__drop">
                        <button type="button" class="general-top__drop__button js-drop-button general-drop__text">
                            <span class="text"><?php echo e(__('main.Типы объектов')); ?></span>
                            <span class="icon-drop"></span>
                        </button>
                        <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                <?php $__currentLoopData = __('attributes.' . $type . '_types'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="general-drop__item">
                                    <label class="checkbox__wrapper">
                                        <input type="checkbox" class="input-checkbox" name="subscription_types[]" value="<?php echo e($key); ?>" checked>
                                        <span class="custome-checkbox">
                                            <span class="icon-active"></span>
                                        </span>
                                        <span class="checkbox-text"><?php echo e($item); ?></span>
                                    </label>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="call-back__body">
                    <label class="checkbox__wrapper">
                        <input type="checkbox" class="input-checkbox" name="subscription_adding" value="1" checked>
                        <span class="custome-checkbox">
                            <span class='icon-active'></span>
                        </span>
                        <span class="checkbox-text"><?php echo e(__('main.Добавление новой недвижимости')); ?></span>
                    </label>
                    <label class="checkbox__wrapper">
                        <input type="checkbox" class="input-checkbox" name="subscription_status" value="1" checked>
                        <span class="custome-checkbox">
                            <span class='icon-active'></span>
                        </span>
                        <span class="checkbox-text"><?php echo e(__('main.Изменение статуса')); ?></span>
                    </label>
                    <label class="checkbox__wrapper">
                        <input type="checkbox" class="input-checkbox" name="subscription_price" value="1" checked>
                        <span class="custome-checkbox">
                            <span class='icon-active'></span>
                        </span>
                        <span class="checkbox-text"><?php echo e(__('main.Изменение цен')); ?></span>
                    </label>
                </div>
                <div class="call-back__footer">
                    <label class="input__wrapper <?php $__errorArgs = ['subscription_email_product'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <input type="email" class="main-input" name="subscription_email_product" placeholder="<?php echo e(__('forms.placeholders.Ваш электронный адрес')); ?>">
                        <?php $__errorArgs = ['subscription_email_product'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error-text" role="alert">
                                <?php echo e($message); ?>

                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>
                    <button class='call-back__button'><?php echo e(__('main.Подписаться')); ?></button>
                </div>
            </form>
        </div>
    </section -->

    <!-- OBJECTS STATUS: DONE -->
    <?php if($status != 'done'): ?>
    <section class="product" v-if="done.total">
        <form action="<?php echo e(route($lang . '_catalog', $category->slug)); ?>" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <?php if($objectType && $objectType != 'Земельный_участок'): ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Построенные', ['type' => mb_strtolower($objectTypePlural)])); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php elseif($objectType): ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Застроенные', ['type' => mb_strtolower($objectTypePlural)])); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php else: ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($type === 'cottage'? __('main.Построенные коттеджные городки') : __('main.Построенные новостройки')); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php endif; ?>
                <p class="calc-product">
                <?php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=done';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                ?>
                    <a href="<?php echo e($link); ?>" v-cloak>{{ done.total }}</a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="done.data.length">
                    <productcard v-for="(product, key) in done.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: done.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <?php if($region_id): ?>
                <input type="hidden" name="address[region]" value="<?php echo e($region_id); ?>">
                <?php endif; ?>
                <?php if($city_id): ?>
                <input type="hidden" name="address[city]" value="<?php echo e($city_id); ?>">
                <?php endif; ?>
                <?php if($area_id): ?>
                <input type="hidden" name="address[area]" value="<?php echo e($area_id); ?>">
                <?php endif; ?>
                <?php if($type == 'cottage' && $objectType): ?>
                <input type="hidden" name="filters[attributes][1][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php elseif($type == 'newbuild' && $objectType): ?>
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php endif; ?>
                <input type="hidden" name="filters[product_attributes][status][]" value="done">
                <button type="submit" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Смотреть все')); ?></span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>
    <?php endif; ?>
    <!-- OBJECTS STATUS: BUILDING -->
    <?php if($status != 'building'): ?>
    <section class="product" v-if="building.total">
        <form action="<?php echo e(route($lang . '_catalog', $category->slug)); ?>" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <?php if($objectType && $objectType != 'Земельный_участок'): ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Строящиеся', ['type' => mb_strtolower($objectTypePlural)])); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php elseif($objectType): ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Застраиваемые', ['type' => mb_strtolower($objectTypePlural)])); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php else: ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($type === 'cottage'? __('main.Строящиеся коттеджные городки') : __('main.Строящиеся новостройки')); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php endif; ?>
                <p class="calc-product">
                <?php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=building';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                ?>
                    <a href="<?php echo e($link); ?>" v-cloak>{{ building.total }}</a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="building.data.length">
                    <productcard v-for="(product, key) in building.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: building.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>

                <?php if($region_id): ?>
                <input type="hidden" name="address[region]" value="<?php echo e($region_id); ?>">
                <?php endif; ?>
                <?php if($city_id): ?>
                <input type="hidden" name="address[city]" value="<?php echo e($city_id); ?>">
                <?php endif; ?>
                <?php if($area_id): ?>
                <input type="hidden" name="address[area]" value="<?php echo e($area_id); ?>">
                <?php endif; ?>
                <?php if($type == 'cottage' && $objectType): ?>
                <input type="hidden" name="filters[attributes][1][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php elseif($type == 'newbuild' && $objectType): ?>
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php endif; ?>
                <input type="hidden" name="filters[product_attributes][status][]" value="building">
                <button type="submit" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Смотреть все')); ?></span>
                    <span class="icon-arrow-more"></span>
                </button>
            </form>
        </div>
    </section>
    <?php endif; ?>
    <!-- OBJECTS STATUS: PROJECT -->
    <?php if($status != 'project'): ?>
    <section class="product" v-if="project.total">
        <form action="<?php echo e(route($lang . '_catalog', $category->slug)); ?>" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <?php if($objectType): ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Проектируемые', ['type' => mb_strtolower($objectTypePlural)])); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php else: ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($type === 'cottage'? __('main.Проектируемые коттеджные городки') : __('main.Проектируемые новостройки')); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php endif; ?>
                <p class="calc-product">
                <?php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=project';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                ?>
                    <a href="<?php echo e($link); ?>" v-cloak>{{ project.total }}</a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="project.data.length">
                    <productcard v-for="(product, key) in project.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: project.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>

                <?php if($region_id): ?>
                <input type="hidden" name="address[region]" value="<?php echo e($region_id); ?>">
                <?php endif; ?>
                <?php if($city_id): ?>
                <input type="hidden" name="address[city]" value="<?php echo e($city_id); ?>">
                <?php endif; ?>
                <?php if($area_id): ?>
                <input type="hidden" name="address[area]" value="<?php echo e($area_id); ?>">
                <?php endif; ?>
                <?php if($type == 'cottage' && $objectType): ?>
                <input type="hidden" name="filters[attributes][1][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php elseif($type == 'newbuild' && $objectType): ?>
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php endif; ?>
                <input type="hidden" name="filters[product_attributes][status][]" value="project">
                <button type="submit" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Смотреть все')); ?></span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>
    <?php endif; ?>
    <!-- OBJECTS STATUS: SOLD -->
    <?php if($status != 'sold'): ?>
    <section class="product" v-if="sold.total">
        <form action="<?php echo e(route($lang . '_catalog', $category->slug)); ?>" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <?php if($objectType): ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Проданные', ['type' => mb_strtolower($objectTypePlural)])); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php else: ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($type === 'cottage'? __('main.Проданные коттеджные городки') : __('main.Проданные новостройки')); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php endif; ?>
                <p class="calc-product">
                <?php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=sold';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                ?>
                    <a href="<?php echo e($link); ?>" v-cloak>{{ sold.total }}</a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="sold.data.length">
                    <productcard v-for="(product, key) in sold.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: sold.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>

                <?php if($region_id): ?>
                <input type="hidden" name="address[region]" value="<?php echo e($region_id); ?>">
                <?php endif; ?>
                <?php if($city_id): ?>
                <input type="hidden" name="address[city]" value="<?php echo e($city_id); ?>">
                <?php endif; ?>
                <?php if($area_id): ?>
                <input type="hidden" name="address[area]" value="<?php echo e($area_id); ?>">
                <?php endif; ?>
                <?php if($type == 'cottage' && $objectType): ?>
                <input type="hidden" name="filters[attributes][1][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php elseif($type == 'newbuild' && $objectType): ?>
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php endif; ?>
                <input type="hidden" name="filters[product_attributes][status][]" value="sold">
                <button type="submit" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Смотреть все')); ?></span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>
    <?php endif; ?>
    <!-- OBJECTS STATUS: FROZEN -->
    <?php if($status != 'frozen'): ?>
    <section class="product" v-if="frozen.total">
        <form action="<?php echo e(route($lang . '_catalog', $category->slug)); ?>" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <?php if($objectType): ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Замороженные', ['type' => mb_strtolower($objectTypePlural)])); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php else: ?>
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($type === 'cottage'? __('main.Замороженные коттеджные городки') : __('main.Замороженные новостройки')); ?> <?php echo e($region_name_genitive); ?></h2>
                <?php endif; ?>
                <p class="calc-product">
                <?php
                $link = route($lang . '_catalog', $category->slug) . '?filters[product_attributes][status][]=frozen';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                if($type == 'cottage' && $objectType)
                    $link = $link . '&filters[attributes][1][]=' . str_replace('_', ' ', $objectType);
                elseif($type == 'newbuild' && $objectType)
                    $link = $link . '&filters[product_attributes][newbuild_type][]' . str_replace('_', ' ', $objectType);
                ?>
                    <a href="<?php echo e($link); ?>" v-cloak>{{ frozen.total }}</a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="frozen.data.length">
                    <productcard v-for="(product, key) in frozen.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: frozen.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>

                <?php if($region_id): ?>
                <input type="hidden" name="address[region]" value="<?php echo e($region_id); ?>">
                <?php endif; ?>
                <?php if($city_id): ?>
                <input type="hidden" name="address[city]" value="<?php echo e($city_id); ?>">
                <?php endif; ?>
                <?php if($area_id): ?>
                <input type="hidden" name="address[area]" value="<?php echo e($area_id); ?>">
                <?php endif; ?>
                <?php if($type == 'cottage' && $objectType): ?>
                <input type="hidden" name="filters[attributes][1][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php elseif($type == 'newbuild' && $objectType): ?>
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php endif; ?>
                <input type="hidden" name="filters[product_attributes][status][]" value="frozen">
                <button type="submit" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Смотреть все')); ?></span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>
    <?php endif; ?>
    <!-- OBJECT TYPES -->
    <?php if(!$region && !$area && !$city && !$kyivdistrict && !$objectType && !$status): ?>
    <section class="best-company">
        <div class="best-company__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($type === 'cottage'? __('main.Коттеджи') : __('main.Новостройки')); ?> <?php echo e($region_name_genitive); ?> <?php echo e(__('main.по типу')); ?></h2>
                <!-- <p class="calc-product">240 <span><?php echo e(__('main.Всего')); ?></span></p> -->
            </div>
            <ul class="best-company__list">
                <?php $__currentLoopData = __('attributes.' . $type . '_types'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($key !== 'Эллинг'): ?>
                <?php
                    $link = route($lang . '_precatalog', $category->slug);

                    if($kyivdistrict)
                        $link = $link . '/kyivdistrict/' . $kyivdistrict->slug;
                    if($city)
                        $link = $link . '/city/' . $city->slug;
                    elseif($area)
                        $link = $link . '/area/' . $area->slug;
                    elseif($region)
                        $link = $link . '/region/' . $region->slug;

                    $link = $status? $link . '/' . \Str::slug($status) : $link;
                    $link = $link . '/' . \Str::slug($key);
                ?>
                <li class="best-company__item">
                    <a href="<?php echo e($link); ?>">
                        <img v-lazy="'<?php echo e(url('common/' . $page[$type . '_types_' . $key] . '?w=350&fm=pjpg&q=80')); ?>'" alt="<?php echo e(__('main.Фото')); ?>: <?php echo e(__('plural.nominative.' . $key)); ?> <?php echo e($region_name_genitive); ?>" title="<?php echo e(__('main.Картинка')); ?>: <?php echo e(__('plural.nominative.' . $key)); ?> <?php echo e($region_name_genitive); ?>">
                    </a>
                    <div class="best-company__name">
                        <a href="<?php echo e($link); ?>">
                            <h5><?php echo e(__('plural.nominative.' . $key)); ?></h5>
                            <!-- <span>575</span> -->
                        </a>
                    </div>
                </li>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <div class="best-company__text"><?php echo $page[$type . '_types_text']; ?></div>
        </div>
    </section>
    <?php endif; ?>
    <!-- OBJECTS FREE SEARCH -->
    <?php echo $__env->make('includes.freeSearch', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- OBJECT'S REVIEWS -->
    <?php if($reviews_total): ?>
    <section class="reviews">
        <div class="reviews__wrapper container slider-infinity">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Отзывы')); ?> <?php echo e($type === 'cottage'? __('main.о коттеджных городках') : __('main.о новостройках')); ?> <?php echo e($region_name_genitive); ?></h2>
                <p class="calc-product">
                    <a href="<?php echo e(route($lang . '_reviews')); ?>"><?php echo e($reviews_total); ?></a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="reviews__list reviews__list-construction js-infinity-slider-list reviews-slider__list">
                <template v-if="reviews.length">
                    <reviewCard v-for="(review, key) in reviews" :data-review="review" data-type="precatalog" :key="key" :data-classes="key == 0? 'reviews-slider__item js-slider-item-infinity show' : 'reviews-slider__item js-slider-item-infinity'"></reviewCard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper <?php if($reviews_total < 4): ?> hide <?php endif; ?>">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- OBJECTS OTHER CATEGORY -->
    <section class="product" v-if="other_category.total">
        <form action="<?php echo e(route($lang . '_catalog', $other_category->slug)); ?>" method="get" class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l"><?php echo e($type === 'cottage'? __('main.Новостройки') : __('main.Коттеджные городки и поселки')); ?> <?php echo e($region_name_genitive); ?></h2>
                <p class="calc-product">
                <?php
                $link = route($lang . '_catalog', $other_category->slug) . '?filters[search_value]=';
                $link = $region_id? $link . '&address[region]=' . $region_id : $link;
                $link = $area_id? $link . '&address[area]=' . $area_id : $link;
                $link = $city_id? $link . '&address[city]=' . $city_id : $link;
                $link = $kyivdistrict_id? $link . '&address[kyivdistrict]=' . $kyivdistrict_id : $link;
                $link = $status? $link . '&filters[product_attributes][status][]' . $status : $link;
                ?>
                    <a href="<?php echo e($link); ?>" v-cloak>{{ other_category.total }}</a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="other_category.data.length">
                    <productcard v-for="(product, key) in other_category.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" alt="" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper" :class="{hide: other_category.total < 5}">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <?php if($region_id): ?>
                <input type="hidden" name="address[region]" value="<?php echo e($region_id); ?>">
                <?php endif; ?>
                <?php if($city_id): ?>
                <input type="hidden" name="address[city]" value="<?php echo e($city_id); ?>">
                <?php endif; ?>
                <?php if($area_id): ?>
                <input type="hidden" name="address[area]" value="<?php echo e($area_id); ?>">
                <?php endif; ?>
                <?php if($type == 'cottage' && $objectType): ?>
                <input type="hidden" name="filters[attributes][1][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php elseif($type == 'newbuild' && $objectType): ?>
                <input type="hidden" name="filters[product_attributes][newbuild_type][]" value="<?php echo e(str_replace('_', ' ', $objectType)); ?>">
                <?php endif; ?>
                <?php if($status): ?>
                <input type="hidden" name="filters[product_attributes][status][]" value="<?php echo e($status); ?>">
                <?php endif; ?>
                <button type="submit" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Смотреть все')); ?></span>
                    <span class="icon-arrow-more"></span>
                </button>
            </div>
        </form>
    </section>


    <!-- BEST COMPANIES -->
    <section class="best-company-info" v-if="companies.total">
        <div class="ts-lang-<?php echo e($lang); ?> ts-precatalog best-company-info__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Лучшие компании')); ?> <?php echo e($region_name_genitive); ?></h2>
                <p class="calc-product">
                    <a href="<?php echo e(route($lang . '_companies')); ?>">{{ companies.total }}</a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>

            <?php if(!$region && !$area && !$city && !$kyivdistrict && !$objectType && !$status): ?>
            <div class="best-company-info__text"><?php echo $page[$type . '_companies_text']; ?></div>
            <?php endif; ?>
            <ul class="best-company-info__list">
                <companycard v-for="(company, key) in companies.data" :key="key" :data-company="company" @add-to-favorites="addToFavorites" @add-to-notifications="addToNotifications"></companycard>
            </ul>
            <a href="<?php echo e(route($lang . '_companies')); ?>" class="main-button-more">
                <span class="text"><?php echo e(__('main.Смотреть все компании')); ?></span>
                <span class="icon-arrow-more"></span>
            </a>
        </div>
    </section>

    <!-- PROMOTIONS -->
    <?php if($promotions->count()): ?>
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Акции от застройщиков')); ?> <?php echo e($region_name_genitive); ?></h2>
                <p class="calc-product">
                    <a href="<?php echo e(route($lang . '_promotions')); ?>"><?php echo e($promotions_total); ?></a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="product__list product__list-sale product-slider__list js-infinity-slider-list">
                <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites"></promotioncard>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper <?php if($promotions->count() < 5): ?> hide <?php endif; ?>">
                    <button type="button" class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button type="button" class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="<?php echo e(route($lang . '_promotions')); ?>" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Смотреть все акции')); ?></span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- NEWS -->
    <section class="popular">
        <div class="popular__wrapper container">
            <div class="general-heading more">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Статьи о недвижимости')); ?> <?php echo e($article_region_name_genitive); ?></h2>
                <a :href="newsCategoryLink" class="read-more">
                    <span><?php echo e(__('main.Читать все статьи')); ?></span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
            <div class="popular__block">
                <div class="popular__block__header">
                    <div class="wrapper">
                        <p class="popular__category-name"><?php echo e(__('main.Новости')); ?></p>
                        <ul class="popular-sub-name__list">
                            <li class="popular-sub-name__item" :class="{active: articleTab == 0}" @click="articleTab = 0"><?php echo e(__('main.Недвижимость')); ?></li>
                        </ul>
                    </div>
                    <div class="wrapper">
                        <p class="popular__category-name"><?php echo e(__('main.Статьи')); ?></p>
                        <ul class="popular-sub-name__list">
                            <li class="popular-sub-name__item" :class="{active: articleTab == 1}" @click="articleTab = 1"><?php echo e(__('main.Строительство')); ?></li>
                            <li class="popular-sub-name__item" :class="{active: articleTab == 2}" @click="articleTab = 2"><?php echo e(__('main.Недвижимость')); ?></li>
                            <li class="popular-sub-name__item" :class="{active: articleTab == 3}" @click="articleTab = 3"><?php echo e(__('main.Аналитика')); ?></li>
                        </ul>
                    </div>
                </div>
                <div class="popular__block__body">
                    <ul class="popular__block__list popular__block__list-main">
                        <articlecard v-for="(article, key) in articles" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
                    </ul>
                </div>
            </div>
            <div class="subscribe-block">
                <h5 class="subscribe-block__text"><?php echo e(__('main.Нашли полезную информацию?')); ?><br> <?php echo e(__('main.Подписывайтесь на актуальные публикации')); ?>:</h5>
                <?php echo $__env->make('modules.subscription', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </section>

    <?php if($status != 'frozen'): ?>
    <section class="price-block" v-if="pricesProducts.length">
        <div class="price-block__wrapper container">
            <div class="price-block__header">
                <div class="price-block__main">
                    <?php
                        $title = $status? mb_strtolower(__('main.product_statuses_plural_prepositional.' . $status)) : '';
                        $title = $objectType? $title . ' ' . mb_strtolower(__('plural.prepositional.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural_prepositional'));
                        $title = $title . ' ' . $region_name_genitive;
                        $unit = $objectType == 'Земельный_участок'? 'сот' : 'кв.м';
                    ?>
                    <p class="main-caption-l main-caption-l--transform "><?php echo e(__('main.Средняя цена') . ' ' . __('main.в') . ' ' . $title); ?></p>
                    <p class="price-block__main__number" v-cloak>{{ prices.avg }}<span>грн/<?php echo e($unit); ?></span></p>
                </div>
                <div class="price-block__header__container">
                    <div class="price-block__header__item">
                        <p class="price-block__header__item-caption"><?php echo e(__('main.Минимальная цена')); ?></p>
                        <p class="price-block__header__item-sub"><?php echo e(__('main.в')); ?> <?php echo e($title); ?></p>
                        <p class="price-block__header__item-number" v-cloak>{{ prices.min }}<span>грн/<?php echo e($unit); ?></span></p>
                    </div>
                    <div class="price-block__header__item">
                        <p class="price-block__header__item-caption"><?php echo e(__('main.Максимальная цена')); ?></p>
                        <p class="price-block__header__item-sub"><?php echo e(__('main.в')); ?> <?php echo e($title); ?></p>
                        <p class="price-block__header__item-number" v-cloak>{{ prices.max }}<span>грн/<?php echo e($unit); ?></span></p>
                    </div>
                </div>
            </div>
            <div class="rating-block__table rating-block__table--price">
                <div class="rating-block__table__container">
                    <div class="rating-block__table__caption">
                        <p class="table-type"><?php echo e(__('main.Название')); ?></p>
                        <p class="table-description"><?php echo e(__('main.Застройщик')); ?></p>
                        <p class="table-price"><?php echo e(__('main.Цена')); ?>, грн/<?php echo e($unit); ?></p>
                    </div>
                    <div class="wrapper" v-cloak>
                        <div class="rating-block__table__item" v-for="item in pricesProducts">
                            <a :href="item.link" class="table-type">{{ item.name }}</a>
                            <p class="table-description">{{ item.brand_name? item.brand_name : '-' }}</p>
                            <?php if($objectType !== 'Земельный_участок'): ?>
                            <p class="table-price">{{ item.statistics_price }}</p>
                            <?php else: ?>
                            <p class="table-price">{{ item.statistics_price_plot }}</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- RATINGS/STATISTICS -->
    <?php if(!$region_id && !$status && !$objectType): ?>
    <section class="rating-block rating-block-info">
        <div class="rating-block__wrapper container">
            <div class="js-drop-item rating-drop__wrapper">
                <button class="rating__button-mobile js-drop-button">
                    <span class="rating-icon"></span>
                    <span><?php echo e(__('main.Рейтинги' )); ?> <?php echo e(mb_strtolower(__('main.type_' . $type . '_plural_genitive'))); ?> <?php echo e($region_name_genitive); ?></span>
                    <span class="icon-drop"></span>
                </button>
                <h2 class="main-caption-l rating-caption"><span class="rating-icon"></span><?php echo e(__('main.Рейтинги' )); ?> <?php echo e(mb_strtolower(__('main.type_' . $type . '_plural_genitive'))); ?> <?php echo e($region_name_genitive); ?> </h2>
                <ul class="rating-block__list">
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon"></span>
                            <h3 class="rating-block__item__caption"><span>ТОП-10</span><?php echo e(mb_strtolower(__('main.type_' . $type . '_plural_genitive'))); ?></h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name"><?php echo e(__('main.Название')); ?></p>
                                <p class="table-rating"><?php echo e(__('main.баллы')); ?></p>
                            </div>
                            <div class="wrapper">
                                <?php $__currentLoopData = $top_rating; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $item_type = $item->category_id == 1 || $item->category->id == 6? 'cottage' : 'complex';
                                ?>
                                <div class="rating-block__table__item">
                                    <p class="table-number"><?php echo e($key + 1); ?></p>
                                    <a href="<?php echo e($item->link . '/rating'); ?>" class="table-name"><?php echo e($item->name); ?></a>
                                    <p class="table-rating"><?php echo e($item->top_rating); ?></p>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item rating-block__item-assessment">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-man"></span>
                            <h3 class="rating-block__item__caption"><?php echo e(__('main.Народный рейтинг')); ?> <?php echo e(mb_strtolower(__('main.type_' . $type . '_plural_genitive'))); ?></h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name"><?php echo e(__('main.Название')); ?></p>
                                <p class="table-calc"><?php echo e(__('main.Кол-во')); ?></p>
                                <p class="table-rating"><?php echo e(__('main.Оценка')); ?></p>
                            </div>
                            <div class="wrapper">
                                <?php $__currentLoopData = $reviews_rating; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="rating-block__table__item">
                                    <p class="table-number"><?php echo e($key + 1); ?></p>
                                    <a href="<?php echo e($item->link); ?>" class="table-name"><?php echo e($item->name); ?></a>
                                    <p class="table-calc"><?php echo e($item->old_rating_count); ?></p>
                                    <p class="table-rating"><?php echo e(round($item->old_rating / $item->old_rating_count, 1)); ?></p>
                                </div>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="rating-drop__info">
                <div class="js-drop-item rating-drop__wrapper">
                    <button class="rating__button-mobile js-drop-button">
                        <span class="rating-icon"></span>
                        <span>
                            <span><?php echo e(__('main.Статистика')); ?> <?php echo e(mb_strtolower(__('main.type_' . $type . '_plural_genitive'))); ?></span>
                        </span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="rating-block__general-wrapper">
                        <h2 class="main-caption-l rating-caption">
                            <span class="rating-icon"></span>
                            <span><?php echo e(__('main.Статистика')); ?> <?php echo e(mb_strtolower(__('main.type_' . $type . '_plural_genitive'))); ?></span>
                        </h2>
                        <ul class="rating-block__list rating-block__list-diagram">
                            <li class="rating-block__item">
                                <div class="rating-block__item__header">
                                    <span class="rating-block-icon-diagram"></span>
                                    <h3 class="rating-block__item__caption"><?php echo e(__('main.Статистика')); ?> (грн) - <?php echo e(__('main.type_' . $type . '_plural')); ?></h3>
                                </div>
                                <div class="rating-block__table">
                                    <div class="wrapper">
                                        <div class="rating-block__general-info">
                                            <a href="<?php echo e(route($lang . '_precatalog', $category_slug)); ?>" class="name"><?php echo e(__('main.Украина')); ?></a>
                                            <p class="date"><?php echo e($statistics->first()->date); ?> - <span><?php echo e($statistics->first()->total); ?></span></p>
                                            <p class="date"><?php echo e($statistics->last()->date); ?> - <span><?php echo e($statistics->last()->total); ?></span></p>
                                        </div>
                                        <?php if($type == 'cottage'): ?>
                                        <div class="rating-block__general-info">
                                            <a href="<?php echo e(route($lang . '_precatalog', $category_slug) . '/region/' . \App\Region::where('region_id', 29)->first()->slug); ?>" class="name"><?php echo e(__('main.Киев')); ?></a>
                                            <p class="date"><?php echo e($statistics->first()->date); ?> - <span><?php echo e($statistics->first()->data['29']); ?></span></p>
                                            <p class="date"><?php echo e($statistics->last()->date); ?> - <span><?php echo e($statistics->last()->data['29']); ?></span></p>
                                        </div>
                                        <?php else: ?>
                                        <div class="rating-block__general-info">
                                            <a href="<?php echo e(route($lang . '_precatalog', $category_slug) . '/region/' . \App\Region::where('region_id', 11)->first()->slug); ?>" class="name"><?php echo e(__('main.Киевская')); ?></a>
                                            <p class="date"><?php echo e($statistics->first()->date); ?> - <span><?php echo e($statistics->first()->data['11']); ?></span></p>
                                            <p class="date"><?php echo e($statistics->last()->date); ?> - <span><?php echo e($statistics->last()->data['11']); ?></span></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rating-block__table__caption">
                                        <p class="table-number">№</p>
                                        <p class="table-area"><?php echo e(__('main.Область')); ?></p>
                                        <p class="table-date"><?php echo e($statistics->first()->date); ?></p>
                                        <p class="table-date"><?php echo e($statistics->last()->date); ?></p>
                                    </div>
                                    <div class="wrapper">
                                        <?php
                                        $i = 1;
                                        $data = $statistics->first()->data;
                                        arsort($data);
                                        ?>
                                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($key != 5 && $key != 13): ?> <!-- Луганская и Донецкая -->
                                        <?php if((($type == 'cottage' && $key != 29) || ($type == 'newbuild' && $key != 11)) && $item && $i <= 10): ?>
                                        <?php
                                        $reg = \App\Region::where('region_id', $key)->first();
                                        ?>
                                        <div class="rating-block__table__item">
                                            <p class="table-number"><?php echo e($i++); ?></p>
                                            <a href="<?php echo e(route($lang . '_precatalog', $category_slug) . '/region/' . $reg->slug); ?>" class="table-name"><?php echo e($reg->name); ?></a>
                                            <p class="table-rating"><?php echo e($item); ?></p>
                                            <p class="table-rating"><?php echo e($statistics->last()->data[$key]); ?></p>
                                        </div>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="js-drop-item rating-drop__wrapper">
                    <button class="rating__button-mobile js-drop-button">
                        <span class="rating-block-icon-info"></span>
                        <span><?php echo e(__('main.Земли Украины')); ?></span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="rating-block__general-wrapper">
                        <h2 class="main-caption-l rating-caption"><?php echo e(__('main.Земли Украины')); ?></h2>
                        <ul class="rating-block__list">
                            <li class="rating-block__item">
                                <div class="rating-block__item__header">
                                    <span class="rating-block-icon-info"></span>
                                    <h3 class="rating-block__item__caption"><?php echo e(__('main.Справочная информация')); ?></h3>
                                </div>
                                <div class="rating-block__table rating-block__table-info">
                                    <ul class="rating-block__table-list">
                                        <?php $__currentLoopData = $land_articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="rating-block__table-item">
                                            <a href="<?php echo e($item->link); ?>" class="rating-block__table-link">
                                                <span class="text"><?php echo e($item->title); ?></span>
                                                <span class="icon-arrow-more"></span>
                                            </a>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <a href="<?php echo e(route($lang . '_precatalog_statistics', $category_slug)); ?>" class="main-button-more"><?php echo e(__('main.Показать все области')); ?></a>
        </div>
    </section>
    <?php endif; ?>

    <!-- MAP -->
    <section class="info-block info-block_map container ts-precatalog">
        <?php
            $title = $status? __('main.product_statuses_plural.' . $status) : '';
            if($title) {
                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
            } else {
                $title = $objectType? $title . ' ' . __('plural.nominative.' . $objectType) : $title . ' ' . __('main.type_' . $type . '_plural');
            }
            $title = $title . ' ' . $region_name_genitive . ' ' . __('main.на карте');
        ?>
        <div class="general-heading">
            <h2 class="main-caption-l main-caption-l--transform"><?php echo e($title); ?></h2>
        </div>
        <div id="general__map" style="height:650px"></div>
    </section>
    <!-- REGION ARTICLE -->
    <?php if($region_article): ?>
    <section class="info-block">
        <div class="info-block__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($region_article->title); ?></h2>
            </div>
            <div class="info-block__container">
                <div class="info-block__inner info-block__inner_region">
                    <?php echo $region_article->content; ?>

                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- CLASSIFICATION ARTICLE -->
    <?php if($classification_article): ?>
    <section class="info-block">
        <div class="info-block__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($type == 'cottage'? $classification_article->name : $classification_article->title); ?></h2>
            </div>
            <div class="info-block__container">
                <div class="info-block__inner">
                    <?php echo $classification_article->content; ?>

                </div>
                <!-- <a href="#" class="read-more">
                    <span>Читать полностью</span>
                    <span class="icon-arrow-more"></span>
                </a> -->
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- SEO TEXT -->
    <?php if($seo_text): ?>
    <section class="info-block">
        <div class="info-block__wrapper container">
            <?php if($seo_title): ?>
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($seo_title); ?></h2>
            </div>
            <?php endif; ?>
            <div class="info-block__container">
                <div class="info-block__inner info-block__inner__classification">
                    <?php echo $seo_text; ?>

                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- FAQ -->
    <?php if($questions && $questions->count()): ?>
    <section class="info-block">
        <div class="info-block__wrapper container">
            <div class="general-heading more">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Частые вопросы и ответы')); ?> <?php echo e($type === 'cottage'? __('main.о коттеджных городках') : __('main.о новостройках')); ?></h2>
                <a href="<?php echo e(route($lang . '_faq')); ?>" class="read-more">
                    <span><?php echo e(__('main.Читать все вопросы')); ?></span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
            <div class="info-block__container">
                <div class="info-block__inner">
                    <ul class="info-block__spoiler__list">
                        <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="info-block__spoiler__item js-drop-item">
                            <button class="info-block__spoiler__button js-drop-button">
                                <span class="text"><?php echo e($question->question); ?></span>
                                <span class="icon-drop"></span>
                            </button>
                            <div class="info__wrapper">
                                <?php echo $question->answer; ?>

                            </div>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- START INTERLINKING -->
    <section class="category-links">
        <div class="category-links__wrapper container">
            <ul class="category-links__list">
                <!-- START KYIVDISTRICTS / CITIES / AREAS / REGIONS -->
                <li class="category-links__item js-drop-item js-catagory-links-item">
                    <button class="category-links__mobile-button js-drop-button">
                    <?php if($city): ?>
                        <span><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по городам')); ?></span>
                    <?php elseif($kyivdistrict || $area): ?>
                        <span><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по районам')); ?></span>
                    <?php else: ?>
                        <span><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по регионам')); ?></span>
                    <?php endif; ?>
                        <span class="icon-drop"></span>
                    </button>
                    <?php if($city): ?>
                        <h5 class="category-links__caption"><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по городам')); ?></h5>
                    <?php elseif($kyivdistrict || $area || ($region && $region->region_id == 29)): ?>
                        <h5 class="category-links__caption"><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по районам')); ?></h5>
                    <?php else: ?>
                        <h5 class="category-links__caption"><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по регионам')); ?></h5>
                    <?php endif; ?>
                    <ul class="category-links-sub__list">
                    <!-- START KYIVDISTRICTS -->
                    <?php if($kyivdistrict || ($region && $region->region_id == 29)): ?>
                    <?php $__currentLoopData = $kyivdistricts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $cache_key = 'count_kyivdistrict:' . $item->kyivdistrict_id . '.category:' . $category->id;

                        if($objectType)
                            $cache_key .= '.type:' . $objectType;

                        if($status)
                            $cache_key .= '.status:' . $status;

                        if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                            $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->kyivdistrict', $item->kyivdistrict_id);

                            if($objectType) {
                                $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                            }

                            if($status) {
                                switch ($status) {
                                        case 'frozen':
                                            $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                            break;

                                        case 'sold':
                                            $prodCount = $prodCount->where('products.is_sold', 1);
                                            break;

                                        default:
                                            $prodCount = $prodCount->where('products.extras->status', $status);
                                            break;
                                    }
                            }

                            $prodCount = $prodCount->count();
                            \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                        }

                        ?>
                        <?php if((!$kyivdistrict || $kyivdistrict->kyivdistrict_id != $item->kyivdistrict_id) && $prodCount): ?>
                        <?php
                            $link = route($lang . '_precatalog', $category->slug) . '/kyivdistrict/' . $item->slug;
                            $link = $status? $link . '/' . \Str::slug($status) : $link;
                            $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                            $title = $status? __('main.product_statuses_plural.' . $status) : '';
                            $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                            $title = $item->name_genitive? $title . ' ' . $item->name_genitive . ' ' . __('main.района') : $title . ' ' . $item->name . ' ' . __('main.район');
                        ?>
                        <li class="category-links-sub__item js-sub-link">
                            <a href="<?php echo e($link); ?>" class="category-links-sub__links" title="<?php echo e($title); ?>"><?php echo e($title); ?></a>
                        </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <!-- END KYIVDISTRICTS -->
                    <!-- START AREAS -->
                    <?php elseif($area): ?>
                        <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $cache_key = 'count_area:' . $item->area_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->area', $item->area_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            ?>
                            <?php if($area->area_id != $item->area_id && $prodCount): ?>
                            <?php
                                $link = route($lang . '_precatalog', $category->slug) . '/area/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $title . ' ' . $item->name_genitive;
                                $title = $item->is_center? $title : $title . ' ' . __('main.района');
                            ?>
                            <li class="category-links-sub__item js-sub-link">
                                <a href="<?php echo e($link); ?>" class="category-links-sub__links" title="<?php echo e($title); ?>"><?php echo e($title); ?></a>
                            </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <!-- END AREAS -->
                    <!-- START CITIES -->
                    <?php elseif($city): ?>
                        <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $cache_key = 'count_city:' . $item->city_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->city', $item->city_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            ?>
                            <?php if($city->city_id != $item->city_id && $prodCount): ?>
                            <?php
                                $link = route($lang . '_precatalog', $category->slug) . '/city/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $item->name_genitive? $title . ' ' . $item->name_genitive : $title . ' ' . $item->name;
                            ?>
                            <li class="category-links-sub__item js-sub-link">
                                <a href="<?php echo e($link); ?>" class="category-links-sub__links" title="<?php echo e($title); ?>"><?php echo e($title); ?></a>
                            </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <!-- END CITIES -->
                    <!-- START REGIONS -->
                    <?php else: ?>
                        <?php $__currentLoopData = $regions_collection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $cache_key = 'count_region:' . $item->region_id . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id)->where('address->region', $item->region_id);

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }

                                if($status) {
                                    switch ($status) {
                                            case 'frozen':
                                                $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                                break;

                                            case 'sold':
                                                $prodCount = $prodCount->where('products.is_sold', 1);
                                                break;

                                            default:
                                                $prodCount = $prodCount->where('products.extras->status', $status);
                                                break;
                                        }
                                }

                                $prodCount = $prodCount->count();
                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                            ?>
                            <?php if(($type === 'cottage' || $item->region_id != 29) && (!$region || $region->region_id != $item->region_id) && $prodCount): ?>
                            <?php
                                $link = route($lang . '_precatalog', $category->slug) . '/region/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $title . ' ' . $item->name_genitive;
                                $title = $item->region_id == 29? $title : $title . ' ' . __('main.области');
                            ?>
                            <li class="category-links-sub__item js-sub-link">
                                <a href="<?php echo e($link); ?>" class="category-links-sub__links" title="<?php echo e($title); ?>"><?php echo e($title); ?></a>
                            </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <!-- END REGIONS -->
                    </ul>
                    <button class="category__sub-button js-drop-button js-category-button">
                        <span class="icon-drop"></span>
                    </button>
                </li>
                <!-- END KYIVDISTRICTS / CITIES / AREAS / REGIONS -->
                <!-- START TYPES -->
                <li class="category-links__item js-drop-item js-catagory-links-item">
                    <button class="category-links__mobile-button js-drop-button">
                        <span><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по типу')); ?></span>
                        <span class="icon-drop"></span>
                    </button>
                    <h5 class="category-links__caption"><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по типу')); ?></h5>
                    <ul class="category-links-sub__list">
                        <?php $__currentLoopData = __('attributes.' . $type . '_types'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $cache_key = 'count_type:' . $key . '.category:' . $category->id;

                            if($kyivdistrict)
                                $cache_key .= '.kyivdistrict:' . $kyivdistrict->kyivdistrict_id;
                            elseif($city)
                                $cache_key .= '.city:' . $city->city_id;
                            elseif($area)
                                $cache_key .= '.area:' . $area->area_id;
                            elseif($region)
                                $cache_key .= '.region:' . $region->region_id;

                            if($status)
                                $cache_key .= '.status:' . $status;

                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id);
                                $prodCount = $region? $prodCount->where('address->region', $region->region_id) : $prodCount;
                                $prodCount = $area? $prodCount->where('address->area', $area->area_id) : $prodCount;
                                $prodCount = $city? $prodCount->where('address->city', $city->city_id) : $prodCount;
                                $prodCount = $kyivdistrict? $prodCount->where('address->kyivdistrict', $kyivdistrict->kyivdistrict_id) : $prodCount;

                                if($status) {
                                    switch ($status) {
                                        case 'frozen':
                                            $prodCount = $prodCount->where('products.extras->is_frozen', 1);
                                            break;

                                        case 'sold':
                                            $prodCount = $prodCount->where('products.is_sold', 1);
                                            break;

                                        default:
                                            $prodCount = $prodCount->where('products.extras->status', $status);
                                            break;
                                    }
                                }

                                $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $key))->select('products.*')->count() : $prodCount->whereJsonContains('extras->newbuild_type', $key)->count();

                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                        ?>
                        <?php if($key !== 'Эллинг' && $key !== $objectType && $prodCount): ?>
                        <?php
                            $link = route($lang . '_precatalog', $category->slug);

                            if($kyivdistrict)
                                $link = $link . '/kyivdistrict/' . $kyivdistrict->slug;
                            if($city)
                                $link = $link . '/city/' . $city->slug;
                            elseif($area)
                                $link = $link . '/area/' . $area->slug;
                            elseif($region)
                                $link = $link . '/region/' . $region->slug;

                            $link = $status? $link . '/' . \Str::slug($status) : $link;
                            $link = $link . '/' . \Str::slug($key);

                            $title = $status? __('main.product_statuses_plural.' . $status) : '';
                            $title = $title? $title . ' ' . mb_strtolower(__('plural.nominative.' . $key)) : mb_strtolower(__('plural.nominative.' . $key));
                            $title = $title . ' ' . $region_name_genitive;
                        ?>
                        <li class="category-links-sub__item js-sub-link">
                            <a href="<?php echo e($link); ?>" class="category-links-sub__links" title="<?php echo e($title); ?>"><?php echo e($title); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button class="category__sub-button js-drop-button js-category-button">
                        <span class="icon-drop"></span>
                    </button>
                </li>
                <!-- END TYPES -->
                <!-- START STATUSES -->
                <li class="category-links__item js-drop-item js-catagory-links-item">
                    <button class="category-links__mobile-button js-drop-button">
                        <span><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по статусу')); ?></span>
                        <span class="icon-drop"></span>
                    </button>
                    <h5 class="category-links__caption"><?php echo e(__('main.type_' . $type . '_plural')); ?> <?php echo e(__('main.по статусу')); ?></h5>
                    <ul class="category-links-sub__list">
                        <?php $__currentLoopData = ['done','building','project','frozen','sold']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $cache_key = 'count_status:' . $item . '.category:' . $category->id;

                            if($objectType)
                                $cache_key .= '.type:' . $objectType;

                            if($kyivdistrict)
                                $cache_key .= '.kyivdistrict:' . $kyivdistrict->kyivdistrict_id;
                            elseif($city)
                                $cache_key .= '.city:' . $city->city_id;
                            elseif($area)
                                $cache_key .= '.area:' . $area->area_id;
                            elseif($region)
                                $cache_key .= '.region:' . $region->region_id;


                            if(!request('caching') && !($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
                                $prodCount = \Aimix\Shop\app\Models\Product::active()->where('category_id', $category->id);
                                $prodCount = $region? $prodCount->where('address->region', $region->region_id) : $prodCount;
                                $prodCount = $area? $prodCount->where('address->area', $area->area_id) : $prodCount;
                                $prodCount = $city? $prodCount->where('address->city', $city->city_id) : $prodCount;
                                $prodCount = $kyivdistrict? $prodCount->where('address->kyivdistrict', $kyivdistrict->kyivdistrict_id) : $prodCount;

                                if($objectType) {
                                    $prodCount = $type == 'cottage'? $prodCount->distinct('products.id')->join('modifications', 'modifications.product_id', '=', 'products.id')->where('modifications.is_default', 0)->join('attribute_modification', 'attribute_modification.modification_id', '=', 'modifications.id')->where('attribute_modification.attribute_id', 1)->whereJsonContains('attribute_modification.value', str_replace('_', ' ', $objectType))->select('products.*') : $prodCount->whereJsonContains('extras->newbuild_type', $objectType);
                                }
                                if($item == 'frozen')
                                    $prodCount = $prodCount->where('products.extras->is_frozen', 1)->count();
                                elseif($item == 'sold')
                                    $prodCount = $prodCount->where('products.is_sold', 1)->count();
                                else
                                    $prodCount = $prodCount->where('products.extras->status', $item)->count();

                                \Illuminate\Support\Facades\Redis::set($cache_key, $prodCount, 'EX', 108000);
                            }

                        ?>
                        <?php if($status != $item && $prodCount): ?>
                        <?php
                            $link = route($lang . '_precatalog', $category->slug);

                            if($kyivdistrict)
                                $link = $link . '/kyivdistrict/' . $kyivdistrict->slug;
                            if($city)
                                $link = $link . '/city/' . $city->slug;
                            elseif($area)
                                $link = $link . '/area/' . $area->slug;
                            elseif($region)
                                $link = $link . '/region/' . $region->slug;

                            $link = $link  . '/' . $item;
                            $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                            $title = __('main.product_statuses_plural.' . $item);
                            $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                            $title = $title . ' ' . $region_name_genitive;
                        ?>
                        <li class="category-links-sub__item js-sub-link">
                            <a href="<?php echo e($link); ?>" class="category-links-sub__links" title="<?php echo e($title); ?>"><?php echo e($title); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button class="category__sub-button js-drop-button js-category-button">
                        <span class="icon-drop"></span>
                    </button>
                </li>
                <!-- END STATUSES -->
            </ul>
        </div>
    </section>
    <!-- END INTERLINKING -->
</main>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<!-- link href='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.css' rel='' data-style="mapbox" / -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />
<style>
.best-company__item img[lazy="loading"] {
    height: 1px;
    width: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.call-back[lazy="loading"] {
    background-image: none !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- <script src='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js'></script> -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>

<script>

    window.addEventListener("load", function(){
        setTimeout(() => {
            var scriptElement = document.createElement('script');
            scriptElement.type = 'text/javascript';
            scriptElement.src = 'https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js';
            document.head.appendChild(scriptElement);

            document.querySelector('link[data-style="mapbox"]').setAttribute('rel', 'stylesheet');

            setTimeout(() => {
                mapboxgl.accessToken = '<?php echo e(config('services.mapbox.token')); ?>';

                document.map = new mapboxgl.Map({
                    language: '<?php echo e($lang); ?>',
                    container: 'general__map',
                    style: 'mapbox://styles/mapbox/streets-v11',
                    center: [31.4827777778,49.0275],
                    zoom: 5,
                });

                document.map.on('load', function() {
                    document.map.getStyle().layers.forEach(function(thisLayer){
                        if(thisLayer.type == 'symbol'){
                            document.map.setLayoutProperty(thisLayer.id, 'text-field', ['get','name_ru'])
                        }
                    });
                });
            }, 500);
        }, 3000);
    });
</script>
<script>

    var promotions = <?php echo json_encode($promotions, 15, 512) ?>;
    var regions = <?php echo json_encode($regions, 15, 512) ?>;
    var address = {
        region: <?php echo json_encode($region_id, 15, 512) ?>,
        area: <?php echo json_encode($area_id, 15, 512) ?>,
        city: <?php echo json_encode($city_id, 15, 512) ?>,
        kyivdistrict: <?php echo json_encode($kyivdistrict_id, 15, 512) ?>,
    };
    var category_slug = <?php echo json_encode($category_slug, 15, 512) ?>;
    var other_category_slug = <?php echo json_encode($other_category->slug, 15, 512) ?>;
    var type = <?php echo json_encode($type, 15, 512) ?>;
    var status = '<?php echo e($status); ?>';
    var objectType = '<?php echo e($objectType); ?>';

</script>
<script src="<?php echo e(url('js/catalog/precatalog.js?v=' . $version)); ?>"></script>
<script>
    // Select block
    let slectedList = document.querySelector('.js-filter-selected');
    function hideMoreButton() {
        if(slectedList.offsetHeight <= 140) {
            document.querySelector('.js-filter-selected-more').classList.add("hide");
        }else {
            document.querySelector('.js-filter-selected-more').classList.remove("hide");
        }
    }
    hideMoreButton();
    window.addEventListener('resize', function() {
        hideMoreButton();
    });
    // Select block
</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', [
  'meta_title' => str_replace(' - Zagorodna.com', '', $meta_title),
  'meta_desc' => $meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/catalog/precatalog.blade.php ENDPATH**/ ?>