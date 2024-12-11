<?php
    $kyivdistrict_id = $kyivdistrict? $kyivdistrict->kyivdistrict_id : null;
    $city_id = $city? $city->city_id : null;
    $area_id = $area? $area->area_id : ($city? $city->area_id : null);
    $region_id = $region? $region->region_id : ($area? $area->region_id : ($city? $city->area->region_id : ($kyivdistrict? 29 : null)));
?>

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background decor-background--pre-catalog" style="background-image:url(<?php echo e(url('img/background-img-1.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render('map',
            $category->slug,
            str_replace(' - Zagorodna.com', '', $h1))); ?>

        </div>
    </section>
    <!-- MAP -->
    <section class="info-block info-block_map container">
        <div class="general-heading">
            <h1 class="main-caption-l main-caption-l--transform"><?php echo e($h1); ?></h1>
        </div>
        <div id="general__map" class="ts-map-ln-28 ts-catalog-map-blade" style="height:650px"></div>
    </section>

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

                        if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                            $link = route($lang . '_map', $category->slug) . '/kyivdistrict/' . $item->slug;
                            $link = $status? $link . '/' . \Str::slug($status) : $link;
                            $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                            $title = $status? __('main.product_statuses_plural.' . $status) : '';
                            $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                            $title = $item->name_genitive? $title . ' ' . $item->name_genitive . ' ' . __('main.района') : $title . ' ' . $item->name . ' ' . __('main.район');
                            $title .= ' ' . __('main.на карте');
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

                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                                $link = route($lang . '_map', $category->slug) . '/area/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $title . ' ' . $item->name_genitive;
                                $title = $item->is_center? $title : $title . ' ' . __('main.района');
                                $title .= ' ' . __('main.на карте');
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

                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                                $link = route($lang . '_map', $category->slug) . '/city/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $item->name_genitive? $title . ' ' . $item->name_genitive : $title . ' ' . $item->name;
                                $title .= ' ' . __('main.на карте');
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

                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                                $link = route($lang . '_map', $category->slug) . '/region/' . $item->slug;
                                $link = $status? $link . '/' . \Str::slug($status) : $link;
                                $link = $objectType? $link . '/' . \Str::slug($objectType) : $link;

                                $title = $status? __('main.product_statuses_plural.' . $status) : '';
                                $title = $objectType? $title . ' ' . mb_strtolower(__('plural.nominative.' . $objectType)) : $title . ' ' . mb_strtolower(__('main.type_' . $type . '_plural'));
                                $title = $title . ' ' . $item->name_genitive;
                                $title = $item->region_id == 29? $title : $title . ' ' . __('main.области');
                                $title .= ' ' . __('main.на карте');
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

                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                            $link = route($lang . '_map', $category->slug);

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
                            $title .= ' ' . __('main.на карте');
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


                            if(!($prodCount = \Illuminate\Support\Facades\Redis::get($cache_key))){
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
                            $link = route($lang . '_map', $category->slug);

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
                            $title .= ' ' . __('main.на карте');
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
</main>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <!-- link href='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.css' rel='stylesheet' / -->

    <link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />

    <!-- link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/ -->
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <!-- script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script -->

    <!-- script src='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js'></script -->

    <script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>

<script>
	mapboxgl.accessToken = '<?php echo e(config('services.mapbox.token')); ?>';

    document.addEventListener("DOMContentLoaded", function(){
        document.map = new mapboxgl.Map({
            container: 'general__map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [31.4827777778,49.0275],
            zoom: 5,
            minZoom: 5
        });

        document.map.on('load', function() {
            document.map.getStyle().layers.forEach(function(thisLayer){
                if(thisLayer.type == 'symbol'){
                    document.map.setLayoutProperty(thisLayer.id, 'text-field', ['get','name_ru'])
                }
            });
        });

    });
</script>
<script>
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
    var tsLang = '<?php echo e($lang); ?>';

</script>
<script src="<?php echo e(url('js/catalog/map.js?v=' . $version . '&lang=' . $lang)); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', [
  'meta_title' => str_replace(' - Zagorodna.com', '', $meta_title),
  'meta_desc' => $meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/catalog/map.blade.php ENDPATH**/ ?>