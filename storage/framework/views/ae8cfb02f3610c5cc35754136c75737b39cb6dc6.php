<?php $__env->startSection('content'); ?>
<main>
		<?php if($banners->count()): ?>
    <section class="main-section">
        <div class="main-slider__wrapper container js-slider">
            <ul class="main-slider__list">
                <?php
                    $width = 1115;
                    if(Browser::isTablet())
                        $width = 950;
                    elseif(Browser::isMobile())
                        $width = 335;

                    if(count($banners) === 1) {
                        $banners[] = $banners[0];
                        $banners[] = $banners[0];
                    } elseif(count($banners) === 2) {
                        $banners[] = $banners[0];
                        $banners[] = $banners[1];
                    }
                ?>

                <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="main-slider__item js-main-slider <?php if($key == 0): ?> show <?php elseif($key == 1): ?> next <?php elseif($key == count($banners) - 1): ?> prev <?php endif; ?>" data-index="<?php echo e($key + 1); ?>" v-lazy:background-image="'<?php echo e(url(str_replace('files', 'glide', $banner->image) . '?w=' . $width . '&fm=pjpg&q=75')); ?>'">
                    <div class="main-slider__item__info">
                        <h2 class="main-slider__name"><?php echo e($banner->title); ?></h2>
                        <?php if($banner->short_desc): ?>
                        	<p class="main-slider__info"><span><?php echo e(__('main.от')); ?></span><?php echo e($banner->short_desc); ?> <span>грн/кв.м</span></p>
                        <?php endif; ?>
                        <?php if($banner->link): ?>
                        	<a rel="nofollow" href="<?php echo e(url($banner->link)); ?>" target="_blank" class="main-button"><?php echo e($banner->button_text); ?></a>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <div class="main-slider__dots">
              <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button class="main-slider__dots-button js-dot <?php if($key == 0): ?> active <?php endif; ?>" data-index="<?php echo e($key + 1); ?>"></button>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="general-button__wrapper general-button__wrapper-main js-arrows-main">
                <button class="general-button prev">
                    <span class="icon-main-arrow-left"></span>
                </button>
                <button class="general-button next">
                    <span class="icon-main-arrow-right"></span>
                </button>
            </div>
        </div>

        <div class="general-filter">
            <div class="general-filter__wrapper container">
                <form :action="searchFormAction" class="general-filter__form">
                <input type="hidden" name="address[region]" v-model="address.region" v-if="address.region">
                <input type="hidden" name="address[area]" v-model="address.area" v-if="address.area">
                <input type="hidden" name="address[city]" v-model="address.city" v-if="address.city">
                <input type="hidden" name="price[0]" v-model="price.min" v-if="price.min">
                <input type="hidden" name="price[1]" v-model="price.max" v-if="price.max">
                    <div class="filter-check">
                        <label class="filter-check__wrapper">
                            <input type="checkbox" class='filter-check__input' name="new-houses" v-model="main_search_type" disabled>
                            <h4 class="general-filter__caption"></h4>
                            <span class="filter-check__decor-wrapper">
                                <span class="filter-check__decor"></span>
                            </span>
                        </label>
                    </div>
                    <ul class="general-filter__list">
                        <li class="general-filter__item ">
                            <h4 class="general-filter__caption"><?php echo e(__('main.Область')); ?></h4>
                            <div class="general-drop js-drop-item">
                                <button type="button" class="general-filter__button js-drop-button">
                                    <input type="text" class="general-drop-input js-drop-input" :value="address.region? regions[address.region] : '<?php echo e(__('main.Все области')); ?>'" readonly>
                                    <span class="icon-drop"></span>
                                </button>
                                <div class="general-drop__container">
                                    <div class="general-drop__wrapper">
                                        <ul class="general-drop__list">
                                            <li class="general-drop__item js-drop-contains" v-for="(region, key) in regions" :class="{active: address.region == key}" @click="address.region = key">{{ region }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="general-filter__item">
                            <h4 class="general-filter__caption"><?php echo e(__('main.Район')); ?></h4>
                            <div class="general-drop js-drop-item">
                                <button type="button" class="general-filter__button js-drop-button">
                                    <input type="text" class="general-drop-input js-drop-input" :value="address.area? areas[address.area] : '<?php echo e(__('main.Все районы')); ?>'" readonly>
                                    <span class="icon-drop"></span>
                                </button>
                                <div class="general-drop__container">
                                    <div class="general-drop__wrapper" v-if="Object.keys(areas).length">
                                        <ul class="general-drop__list">
                                            <li class="general-drop__item js-drop-contains" v-for="(area, key) in areas" :class="{active: address.area == key}" @click="address.area = key">{{ area }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="general-filter__item">
                            <h4 class="general-filter__caption"><?php echo e(__('main.Населенный пункт')); ?></h4>
                            <div class="general-drop js-drop-item">
                                <button type="button" class="general-filter__button js-drop-button">
                                    <input type="text" class="general-drop-input js-drop-input" :value="address.city? cities[address.city] : '<?php echo e(__('main.Все нас пункты')); ?>'" readonly>
                                    <span class="icon-drop"></span>
                                </button>
                                <div class="general-drop__container">
                                    <div class="general-drop__wrapper" v-if="Object.keys(cities).length">
                                        <ul class="general-drop__list">
                                            <li class="general-drop__item js-drop-contains" v-for="(city, key) in cities" :class="{active: address.city == key}" @click="address.city = key">{{ city }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <label class="input__wrapper">
                        <input type="number" :min="main_search_type? range_options.min_2 : range_options.min_1" :max="main_search_type? range_options.max_2 : range_options.max_1" class="main-input" placeholder="<?php echo e(__('main.Ср. цена')); ?>, грн/м2" v-model="average_price" @change="checkAveragePrice()">
                    </label>
                    <button class="filter-button">
                        <span class="filter-button__decor"></span>
                        <span class="filter-button__text"><?php echo e(__('main.Подобрать')); ?></span>
                    </button>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <div class="general-text container">
        <h1 class="main-caption-xl"><?php echo e($page->entry_title); ?></h1>
        <?php echo $page->entry_text; ?>

    </div>
    <?php if($hits_count): ?>
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l"><?php echo e($page->hot_title); ?></h2>
                <p class="calc-product">{{ hits.total }} <span><?php echo e(__('main.Всего')); ?></span></p>
            </div>
            <div class="general-text container">
            <?php echo $page->hot_text; ?>

            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="hits.total">
                    <productcard v-for="(product, key) in hits.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" v-else>
            </ul>

            <div class="general-button__wrapper js-arrow-infinity general-button--hide container">
                <div class="wrapper <?php if($hits_count < 5): ?> hide <?php endif; ?>">
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
    <?php endif; ?>
    <?php if($cottages_count): ?>
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l"><?php echo e($page->cottage_title); ?></h2>
                <p class="calc-product">
                    <a href="<?php echo e(route($lang . '_precatalog', $cottage_slug)); ?>">{{ cottages.total }}</a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <div class="general-text container">
            <?php echo $page->cottage_text; ?>

            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="cottages.total">
                    <productcard v-for="(product, key) in cottages.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper <?php if($cottages_count < 5): ?> hide <?php endif; ?>">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="<?php echo e(route($lang . '_precatalog', $cottage_slug)); ?>" class="main-button-more">
                    <span class="text"><?php echo e($page->cottage_button_text); ?></span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <?php if($newbuilds_count): ?>
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l"><?php echo e($page->newbuild_title); ?></h2>
                <p class="calc-product">
                    <a href="<?php echo e(route($lang . '_precatalog', $newbuild_slug)); ?>">{{ newbuilds.total }}</a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <div class="general-text container">
            <?php echo $page->newbuild_text; ?>

            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="newbuilds.total">
                    <productcard v-for="(product, key) in newbuilds.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper <?php if($newbuilds_count < 5): ?> hide <?php endif; ?>">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="<?php echo e(route($lang . '_precatalog', $newbuild_slug)); ?>" class="main-button-more">
                    <span class="text"><?php echo e($page->newbuild_button_text); ?></span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <?php if($promotions_count): ?>
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l"><?php echo e($page->promotions_title); ?></h2>
                <p class="calc-product">
                    <a href="<?php echo e(route($lang . '_promotions')); ?>"><?php echo e($promotions_count); ?></a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <div class="general-text container">
            <?php echo $page->promotions_text; ?>

            </div>
            <ul class="product__list product__list-sale product-slider__list js-infinity-slider-list">
                <template v-if="promotions.length">
                    <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites"></promotioncard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper <?php if($promotions_count < 5): ?> hide <?php endif; ?>">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="<?php echo e(route($lang . '_promotions')); ?>" class="main-button-more">
                    <span class="text"><?php echo e($page->promotions_button_text); ?></span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <section class="popular">
        <div class="popular__wrapper container">
            <div class="general-heading more">
                <h2 class="main-caption-l"><?php echo e($page->news_title); ?></h2>
                <a :href="newsCategoryLink" class="read-more">
                    <span><?php echo e($page->news_button_text); ?></span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
            <div class="popular__text"><?php echo $page->news_text; ?></div>
            <div class="popular__block">
                <div class="popular__block__header">
                    <div class="wrapper">
                        <p class="popular__category-name"><?php echo e(__('main.Новости')); ?></p>
                        <ul class="popular-sub-name__list">
                            <li class="popular-sub-name__item" :class="{active: articleTab == 0}" @click="articleTab = 0"><?php echo e(__('main.Недвижимость')); ?></li>
                        </ul>
                    </div>
                    <div class="wrapper">
                        <p class="ts-articles-ln-298 popular__category-name"><?php echo e(__('main.Статьи')); ?></p>
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
        <!-- <div class="general-text container">
        </div> -->
    </section>

    <section class="best-company">
        <div class="best-company__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l"><?php echo e($page->companies_title); ?></h2>
                <p class="calc-product">
                    <a href="<?php echo e(route($lang . '_companies')); ?>"><?php echo e($brands_count); ?></a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="best-company__list">
                <?php $__currentLoopData = $company_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="best-company__item">
                    <a href="<?php echo e($category->link); ?>">
                        <img src="<?php echo e($category->image? url($category->image) : url('image/company-cover.jpg?w=350&q=75')); ?>" alt="Фото: <?php echo e($category->name); ?>" alt="Картинка: <?php echo e($category->name); ?>">
                    </a>
                    <div class="best-company__name">
                        <a href="<?php echo e($category->link); ?>">
                            <h5><?php echo e($category->name); ?></h5>
                            <span><?php echo e($category->brands->count()); ?></span>
                        </a>
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <div class="best-company__text"><?php echo $page->companies_text; ?></div>
            <a href="<?php echo e(route($lang . '_companies')); ?>" class="main-button-more">
                <span class="text"><?php echo e($page->companies_button_text); ?></span>
                <span class="icon-arrow-more"></span>
            </a>
        </div>
    </section>
    <?php if($reviews->count()): ?>
    <section class="reviews">
        <div class="reviews__wrapper slider-infinity container">
            <div class="general-heading">
                <h2 class="main-caption-l"><?php echo e($page->reviews_title); ?></h2>
                <p class="calc-product">
                    <a href="<?php echo e(route($lang . '_reviews')); ?>"><?php echo e($reviews_count); ?></a>
                    <span><?php echo e(__('main.Всего')); ?></span>
                </p>
            </div>
            <ul class="reviews__list js-infinity-slider-list reviews-slider__list">
                <reviewCard v-for="(review, key) in reviews" :data-review="review" data-type="zagorodna" :key="key" :data-classes="key == 0? 'reviews-slider__item js-slider-item-infinity show' : 'reviews-slider__item js-slider-item-infinity'"></reviewCard>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity">
                <div class="wrapper <?php if($reviews->count() < 3): ?> hide <?php endif; ?>">
                    <button class="general-button prev">
                        <span class="icon-arrow-left"></span>
                    </button>
                    <button class="general-button next">
                        <span class="icon-arrow-right"></span>
                    </button>
                </div>
                <a href="<?php echo e(route($lang . '_reviews')); ?>" class="main-button-more">
                    <span class="text"><?php echo e($page->reviews_button_text); ?></span>
                    <span class="icon-arrow-more"></span>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- <section class="rating-block">
        <div class="rating-block__wrapper container">
            <div class="js-drop-item rating-drop__wrapper">
                <button class="rating__button-mobile js-drop-button">
                    <span class="rating-icon"></span>
                    <span>Рейтинги недвижимости</span>
                    <span class="icon-drop"></span>
                </button>
                <h2 class="main-caption-l rating-caption"><span class="rating-icon"></span>Рейтинги недвижимости</h2>
                <ul class="rating-block__list">
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon"></span>
                            <h3 class="rating-block__item__caption"><span>ТОП-10</span>новостроек</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">Название</p>
                                <p class="table-rating">баллы</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">2503</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Новый Коралловый Риф</p>
                                    <p class="table-rating">2203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Морская жемчужина</p>
                                    <p class="table-rating">2100</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">2003</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">1503</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">1203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">253</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">250</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">03</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon"></span>
                            <h3 class="rating-block__item__caption"><span>ТОП-10</span>новостроек</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">Название</p>
                                <p class="table-rating">баллы</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">2503</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Новый Коралловый Риф</p>
                                    <p class="table-rating">2203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Морская жемчужина</p>
                                    <p class="table-rating">2100</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">2003</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">1503</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">1203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">253</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">250</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">203</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-rating">03</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item rating-block__item-assessment">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-man"></span>
                            <h3 class="rating-block__item__caption">Народный рейтинг коттеджных городков</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">Название</p>
                                <p class="table-calc">Кол-во</p>
                                <p class="table-rating">Оценка</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Новый Коралловый Риф</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.2</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Морская жемчужина</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">100</p>
                                    <p class="table-rating">4.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">3.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item rating-block__item-assessment">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-man"></span>
                            <h3 class="rating-block__item__caption">Народный рейтинг новостроек</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-name">Название</p>
                                <p class="table-calc">Кол-во</p>
                                <p class="table-rating">Оценка</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Новый Коралловый Риф</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.2</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Морская жемчужина</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">100</p>
                                    <p class="table-rating">4.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">3.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Городок “Мечта бизнесмена”</p>
                                    <p class="table-calc">10</p>
                                    <p class="table-rating">9.3</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="js-drop-item rating-drop__wrapper">
                <button class="rating__button-mobile js-drop-button">
                    <span class="rating-icon"></span>
                    <span>Статистика недвижимости</span>
                    <span class="icon-drop"></span>
                </button>
                <h2 class="main-caption-l rating-caption"><span class="rating-icon"></span>Статистика недвижимости</h2>
                <ul class="rating-block__list rating-block__list-diagram">
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-diagram"></span>
                            <h3 class="rating-block__item__caption">Статистика (грн) - Новостройка</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="wrapper">
                                <div class="rating-block__general-info">
                                    <p class="name">Украина</p>
                                    <p class="date">01.2020 - <span>15925</span></p>
                                    <p class="date">02.2020 - <span>v 15915</span></p>
                                </div>
                                <div class="rating-block__general-info">
                                    <p class="name">Киев</p>
                                    <p class="date">01.2020 - <span>15925</span></p>
                                    <p class="date">02.2020 - <span>v 15915</span></p>
                                </div>
                            </div>
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-area">Район</p>
                                <p class="table-date">01.2020</p>
                                <p class="table-date">02.2020</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Одесская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Харьковская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Запорожская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Киевская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Черновицкая</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Херсонская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Ивано-Франковская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Закарпатская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Львовская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Николаевская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="rating-block__item">
                        <div class="rating-block__item__header">
                            <span class="rating-block-icon-diagram"></span>
                            <h3 class="rating-block__item__caption">Статистика (грн) - Коттеджи</h3>
                        </div>
                        <div class="rating-block__table">
                            <div class="wrapper">
                                <div class="rating-block__general-info">
                                    <p class="name">Украина</p>
                                    <p class="date">01.2020 - <span>15925</span></p>
                                    <p class="date">02.2020 - <span>v 15915</span></p>
                                </div>
                                <div class="rating-block__general-info">
                                    <p class="name">Киев</p>
                                    <p class="date">01.2020 - <span>15925</span></p>
                                    <p class="date">02.2020 - <span>v 15915</span></p>
                                </div>
                            </div>
                            <div class="rating-block__table__caption">
                                <p class="table-number">№</p>
                                <p class="table-area">Район</p>
                                <p class="table-date">01.2020</p>
                                <p class="table-date">02.2020</p>
                            </div>
                            <div class="wrapper">
                                <div class="rating-block__table__item">
                                    <p class="table-number">1</p>
                                    <p class="table-name">Одесская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">2</p>
                                    <p class="table-name">Харьковская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">3</p>
                                    <p class="table-name">Запорожская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">4</p>
                                    <p class="table-name">Киевская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">5</p>
                                    <p class="table-name">Черновицкая</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">6</p>
                                    <p class="table-name">Херсонская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">7</p>
                                    <p class="table-name">Ивано-Франковская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">8</p>
                                    <p class="table-name">Закарпатская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">9</p>
                                    <p class="table-name">Львовская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                                <div class="rating-block__table__item">
                                    <p class="table-number">10</p>
                                    <p class="table-name">Николаевская</p>
                                    <p class="table-rating">25003</p>
                                    <p class="table-rating">25030</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <button class="main-button-more">Показать все области</button>
        </div>
    </section> -->
    <section class="statistic" v-lazy:background-image="'<?php echo e(url('/image/bg-zagorodna-in-numbers.jpg?w=1425&h=501&fm=pjpg&q=75')); ?>'">
        <div class="statistic__wrapper container">
            <h2 class="main-caption-l"><?php echo e($page->numbers_title); ?></h2>
            <ul class="statistic__list">
                <?php $__currentLoopData = json_decode($page->numbers_content); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="statistic__item">
                    <p class="statistic__item__number"><?php echo e($numbers[$key]); ?></p>
                    <p class="statistic__item__text"><?php echo e($item->text); ?></p>
                    <div class="statistic__item__img" style="background-image: url(<?php echo e(url('img/zagorodna-number-img-' . ($key + 1) . '.svg')); ?>);"></div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </section>
    <section class="seo-block">
        <div class="seo-block__wrapper container">
            <h2 class="main-caption-l"><?php echo e($page->seo_title); ?></h2>
            <div class="seo-block__content">
                <div class="wrapper">
                  <?php echo $page->seo_text; ?>

                </div>

            </div>
        </div>

    </section>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .main-slider__item[lazy="loading"] {
        background-image: none !important;
    }
    .statistic[lazy="loading"] {
        background-image: none !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    var cottages_slug = <?php echo json_encode($cottages_slug, 15, 512) ?>;
    var newbuilds_slug = <?php echo json_encode($newbuilds_slug, 15, 512) ?>;
    var reviews = <?php echo json_encode($reviews, 15, 512) ?>;
    var regions = <?php echo json_encode($regions, 15, 512) ?>;
    var range_options = <?php echo json_encode($range_options, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/index/index.js?v=' . $version)); ?>"></script>

<script>
	//console.log(lang)
	//let newsCategoryLinkWithLang = newsCategoryLink;
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', [
  'meta_title' => $page->meta_title,
  'meta_desc' => $page->meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/index.blade.php ENDPATH**/ ?>