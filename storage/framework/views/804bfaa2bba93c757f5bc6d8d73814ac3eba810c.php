

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <div class="breadcrumbs__list">
                <a href="<?php echo e($lang === 'ru'? url('/') : url($lang)); ?>" class="breadcrumbs__link"><?php echo e(__('main.Главная')); ?></a>
                <a href="#" class="breadcrumbs__link" @click.prevent="query.type = null"><?php echo e($page->main_title); ?></a>
                <a href="#" class="breadcrumbs__link" v-cloak v-if="query.type">{{ title }}</a>
            </div>
        </div>
    </section>
    <section class="favorite">
        <div class="favorite__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l" v-cloak>{{ title }} <span v-if="query.page > 1"> ➨ <?php echo e(__('main.страница')); ?> {{ query.page }}</span></h1>
            </div>
            <div class="main-filtration__wrapper main-filtration__wrapper-offers">
                <ul class="general-filter__list">
                    <li class="general-filter__item">
                        <h4 class="general-filter__caption"><?php echo e(__('main.Область')); ?></h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="query.region? regions[query.region] : '<?php echo e(__('main.Все области')); ?>'" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item js-drop-contains" :class="{active: query.region == null}" @click="query.region = null"><?php echo e(__('main.Все области')); ?></li>
                                    <li class="general-drop__item js-drop-contains" v-for="(item, key) in regions" :class="{active: query.region == key}" @click="query.region = key">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item">
                        <h4 class="general-filter__caption"><?php echo e(__('main.Нас пункт')); ?></h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="query.city? cities[query.city] : '<?php echo e(__('main.Все нас пункты')); ?>'" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                    <li class="general-drop__item js-drop-contains" :class="{active: query.city == null}" @click="query.city = null"><?php echo e(__('main.Все нас пункты')); ?></li>
                                    <li class="general-drop__item js-drop-contains" v-for="(item, key) in cities" :class="{active: query.city == key}" @click="query.city = key">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item">
                        <h4 class="general-filter__caption"><?php echo e(__('main.Тип недвижимости')); ?></h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="query.type? types[query.type] : '<?php echo e(__('main.Все типы')); ?>'" readonly="" v-cloak>
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item js-drop-contains" :class="{active: query.type == null}" @click="query.type = null"><?php echo e(__('main.Все типы')); ?></li>
                                    <li class="general-drop__item js-drop-contains" v-for="(item, key) in types" :class="{active: query.type == key}" @click="query.type = key">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item">
                        <h4 class="general-filter__caption"><?php echo e(__('main.Застройщик')); ?></h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="query.company? companies[query.company] : '<?php echo e(__('main.Все застройщики')); ?>'" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item js-drop-contains" :class="{active: query.company == null}" @click="query.company = null"><?php echo e(__('main.Все застройщики')); ?></li>
                                    <li class="general-drop__item js-drop-contains" v-for="(item, id) in companies" :class="{active: query.company == id}" @click="query.company = id">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="product__wrapper-list-position">
                <div v-if="loading" class="product__list product__list-sale" style="height: 500px">
                    <img src="/img/preload-for-files.gif" style="height: 2px;margin: auto;" alt="">
                </div>
                <ul class="product__list product__list-sale" v-else>
                    <promotioncard v-for="(promotion, key) in promotions.data" :key="key" :data-promotion="promotion" @add-to-favorites="addToFavorites"></promotioncard>

                    <li v-if="!promotions.total" style="pointer-events:none">
                        <?php echo e(__('main.По Вашему запросу не найдено акций')); ?>.
                    </li>

                </ul>
            </div>
            <div class="pagination__wrapper" v-if="promotions.last_page != 1" v-cloak>
                <div class="pagination__container">
                    <button class="general-button" @click="query.page = 1" v-bind:class="{disabled: query.page == 1}">
                        <span class="icon-pagi-left"></span>
                    </button>
                    <button class="general-button" v-bind:class="{disabled: promotions.current_page == 1}" @click="query.page--">
                        <span class="icon-arrow-pagi-left"></span>
                    </button>
                    <ul class="pagination__list">
                        <li class="pagination__item" @click="query.page = 1" v-bind:class="{active: query.page == 1}">
                            <button>1</button>
                        </li>
                        <li class="pagination__item dots" v-if="promotions.last_page > 7 && query.page - 1 > 3">
                            <button>...</button>
                        </li>
                        <li class="pagination__item" v-for="page in (promotions.last_page - 1)" @click="query.page = page" v-bind:class="{active: page == query.page}" v-show="page != 1 && ((query.page == 1 && page <= 6) || (query.page == promotions.last_page && page >= promotions.last_page - 5) || (Math.abs(query.page - page) < 3) || (query.page <= 3 && page <= 6) || (query.page >= promotions.last_page - 3 && page >= promotions.last_page - 6))">
                            <button>{{ page }}</button>
                        </li>
                        <li class="pagination__item dots" v-if="promotions.last_page > 7 && promotions.last_page - query.page > 3">
                            <button>...</button>
                        </li>
                        <li class="pagination__item" @click="query.page = promotions.last_page" v-if="promotions.last_page != 1" v-bind:class="{active: promotions.last_page == query.page}">
                            <button>{{ promotions.last_page }}</button>
                        </li>
                    </ul>
                    <button class="general-button" v-bind:class="{disabled: promotions.current_page == promotions.last_page}" @click="query.page++">
                        <span class="icon-arrow-pagi-right"></span>
                    </button>
                    <button class="general-button" @click="query.page = promotions.last_page" v-if="promotions.last_page != 1" v-bind:class="{disabled: promotions.last_page == query.page}">
                        <span class="icon-pagi-right"></span>
                    </button>
                </div>
                <button @click="loadmore()" v-if="promotions.current_page != promotions.last_page" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Показать больше')); ?></span>
                </button>
            </div>
        </div>
    </section>
    
    <section class="info-block" v-if="seo_text && query.page == 1">
        <div class="info-block__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform" v-cloak>{{ seo_title }}</h2>
            </div>
            <div class="info-block__container">
                <div class="info-block__inner" v-html="seo_text">
                </div>
            </div>
        </div>
    </section>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    var promotions = <?php echo json_encode($promotions, 15, 512) ?>;
    var regions = <?php echo json_encode($regions, 15, 512) ?>;
    var cities = <?php echo json_encode($cities, 15, 512) ?>;
    var types = <?php echo json_encode($types, 15, 512) ?>;
    var companies = <?php echo json_encode($companies, 15, 512) ?>;
    var title = <?php echo json_encode($title, 15, 512) ?>;
    var seo_title = <?php echo json_encode($seo_title, 15, 512) ?>;
    var seo_text = <?php echo json_encode($seo_text, 15, 512) ?>;
    var types_slugs = <?php echo json_encode($types_slugs, 15, 512) ?>;
    var slug = <?php echo json_encode($slug, 15, 512) ?>;
    var lang = <?php echo json_encode($lang, 15, 512) ?>;
    var type = <?php echo json_encode($type, 15, 512) ?>;
    var page = <?php echo json_encode(request('page')? request('page') : 1, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/promotions/promotions.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'meta_title' => $meta_title,
  'meta_desc' => $meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/promotions/index.blade.php ENDPATH**/ ?>