

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
          <?php echo e(Breadcrumbs::render('page', $page->main_title)); ?>

        </div>
    </section>
    <section class="favorite">
        <div class="favorite__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l"><?php echo e($page->main_title); ?></h1>
            </div>
            <div class="general-tabs">
                <ul class="general-tabs__list">
                    <li class="general-tabs__item" @click="query.tab = 'cottages'" :class="{active: query.tab == 'cottages'}"><?php echo e(__('main.Коттеджи')); ?></li>
                    <li class="general-tabs__item" @click="query.tab = 'newbuilds'" :class="{active: query.tab == 'newbuilds'}"><?php echo e(__('main.Новостройки')); ?></li>
                    <li class="general-tabs__item" @click="query.tab = 'promotions'" :class="{active: query.tab == 'promotions'}"><?php echo e(__('main.Акции')); ?></li>
                    <li class="general-tabs__item" @click="query.tab = 'companies'" :class="{active: query.tab == 'companies'}"><?php echo e(__('main.Компании')); ?></li>
                    <li class="general-tabs__item" @click="query.tab = 'articles'" :class="{active: query.tab == 'articles'}"><?php echo e(__('main.Статьи')); ?></li>
                    <li class="general-tabs__item general-tabs__item-empty"></li>
                </ul>
            </div>
            <div class="main-filtration__wrapper main-filtration__wrapper-favorite">
                <ul class="general-filter__list">
                    <li class="general-filter__item">
                        <h4 class="general-filter__caption">{{ filter_name_1 }}</h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="query.filter_1 != null? filters_1[query.filter_1] : '<?php echo e(__('main.Не выбрано')); ?>'" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                  <li class="general-drop__item js-drop-contains" @click="query.filter_1 = null" :class="{active: query.filter_1 == null}"><?php echo e(__('main.Не выбрано')); ?></li>
                                  <li class="general-drop__item js-drop-contains" v-for="(item, key) in filters_1" @click="query.filter_1 = key" :class="{active: query.filter_1 == key}">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item">
                        <h4 class="general-filter__caption">{{ filter_name_2 }}</h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="query.filter_2 != null? filters_2[query.filter_2] : '<?php echo e(__('main.Не выбрано')); ?>'" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                  <li class="general-drop__item js-drop-contains" @click="query.filter_2 = null" :class="{active: query.filter_2 == null}"><?php echo e(__('main.Не выбрано')); ?></li>
                                  <li class="general-drop__item js-drop-contains" v-for="(item, key) in filters_2" @click="query.filter_2 = key" :class="{active: query.filter_2 == key}">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item" v-if="Object.keys(filters_3).length">
                        <h4 class="general-filter__caption">{{ filter_name_3 }}</h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="filters_3[query.filter_3]" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                  <li class="general-drop__item js-drop-contains" v-for="(item, key) in filters_3" @click="query.filter_3 = key" :class="{active: query.filter_3 == key}">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item general-filter__item-sort" v-if="tab != 'promotions' && tab != 'companies'">
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="sorts[query.sort]" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <li class="general-drop__item js-drop-contains" v-for="(sort, key) in sorts" @click="query.sort = key" :class="{active: query.sort == key}">{{ sort }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="product__wrapper-list-position" :class="{'news__wrapper': tab == 'articles'}">
                <ul :class="{'popular__block__list': tab == 'articles', 'product__list': tab != 'articles', 'product__list-tabs': query.filter_3 === 1}" :style="{height: preload? '350px' : 'auto'}">
                    <template v-if="!preload">
                        <template v-for="(item, key) in items.data">
                            <productcard :key="key" :data-product="item" @add-to-favorites="addToFavorites" v-if="query.filter_3 === 0 && (tab == 'cottages' || tab == 'newbuilds')"></productcard>
                            <projectcard :key="key" :data-project="item" @add-to-favorites="addToFavorites" v-if="query.filter_3 === 1 && (tab == 'cottages' || tab == 'newbuilds')"></projectcard>
                            <articlecard :key="key" :data-article="item" @add-to-favorites="addToFavorites" v-if="tab == 'articles'"></articlecard>
                            <promotioncard :key="key" :data-promotion="item" @add-to-favorites="addToFavorites" v-if="tab == 'promotions'"></promotioncard>
                            <companycard :key="key" :data-company="item" @add-to-favorites="addToFavorites" v-if="tab == 'companies'"></companycard>
                        </template>
                    </template>
                    <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" v-else>
                </ul>
            </div>
            <div class="pagination__wrapper" v-if="!preload && items && items.last_page != 1">
                <div class="pagination__container">
                    <button class="general-button" @click="query.page = 1" v-bind:class="{disabled: query.page == 1}">
                        <span class="icon-pagi-left"></span>
                    </button>
                    <button class="general-button" v-bind:class="{disabled: items.current_page == 1}" @click="query.page--">
                        <span class="icon-arrow-pagi-left"></span>
                    </button>
                  <ul class="pagination__list">
                        <li class="pagination__item" @click="query.page = 1" v-bind:class="{active: query.page == 1}">
                          <button>1</button>
                        </li>
                      <li class="pagination__item dots" v-if="items.last_page > 7 && query.page - 1 > 3">
                          <button>...</button>
                        </li>
                      <li class="pagination__item" v-for="page in (items.last_page - 1)" @click="query.page = page" v-bind:class="{active: page == query.page}" v-show="page != 1 && ((query.page == 1 && page <= 6) || (query.page == items.last_page && page >= items.last_page - 5) || (Math.abs(query.page - page) < 3) || (query.page <= 3 && page <= 6) || (query.page >= items.last_page - 3 && page >= items.last_page - 6))">
                          <button>{{ page }}</button>
                      </li>
                      <li class="pagination__item dots" v-if="items.last_page > 7 && items.last_page - query.page > 3">
                          <button>...</button>
                        </li>
                        <li class="pagination__item" @click="query.page = items.last_page" v-if="items.last_page != 1" v-bind:class="{active: items.last_page == query.page}">
                          <button>{{ items.last_page }}</button>
                      </li>
                  </ul>
                  <button class="general-button" v-bind:class="{disabled: items.current_page == items.last_page}" @click="query.page++">
                        <span class="icon-arrow-pagi-right"></span>
                    </button>
                    <button class="general-button" @click="query.page = items.last_page" v-if="items.last_page != 1" v-bind:class="{disabled: items.last_page == query.page}">
                        <span class="icon-pagi-right"></span>
                    </button>
                </div>
                <button @click="loadmore()" v-if="items.current_page != items.last_page" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Показать больше')); ?></span>
                </button>
            </div>
        </div>
    </section>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(url('js/favorite/favorite.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
'meta_title' => $page->meta_title,
'meta_desc' => $page->meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/favorite/index.blade.php ENDPATH**/ ?>