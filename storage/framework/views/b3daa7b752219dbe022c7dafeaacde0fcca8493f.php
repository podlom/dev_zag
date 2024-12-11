

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <div class="breadcrumbs__list">
                <a href="<?php echo e($lang === 'ru'? url('/') : url($lang)); ?>" class="breadcrumbs__link"><?php echo e(__('main.Главная')); ?></a>
                <a href="#" class="breadcrumbs__link" v-if="query.parent_category" @click.prevent="query.category = null" v-cloak>{{ parent_categories[query.parent_category] }}</a>
                <a href="#" class="breadcrumbs__link" v-if="query.category" v-cloak>{{ categories[query.category] }}</a>
            </div>
        </div>
    </section>
    <section class="news">
        <div class="news__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l" v-cloak>{{ query.category? parent_categories[query.parent_category] + ' / тема ' + categories[query.category] : parent_categories[query.parent_category] }} <span v-if="query.page > 1"> ➨ <?php echo e(__('main.страница')); ?> {{ query.page }}</span></h1>
                <div class="general-drop general-top__drop js-drop-item">
                    <button type="button" class="general-top__drop__button js-drop-button general-drop__text"> 
                        <span class="text parent_category_text" v-if="query.parent_category" v-cloak>{{ parent_categories[query.parent_category] }}</span>
                        <span class="text parent_category_text" v-else><?php echo e(__('main.Не выбрано')); ?></span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="general-drop__wrapper">
                        <ul class="general-drop__list">
                          <li class="general-drop__item js-drop-contains" v-for="(item, slug) in parent_categories" @click="query.parent_category = slug" :class="{active: query.parent_category == slug}">{{ item }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="main-filtration__wrapper main-filtration__wrapper-news">
                <ul class="general-filter__list">
                    <li class="general-filter__item">
                        <h4 class="general-filter__caption">Тема</h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="query.category? categories[query.category] : '<?php echo e(__('main.Не выбрано')); ?>'" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                  <li class="general-drop__item js-drop-contains" @click="query.category = null" :class="{active: query.category == null}"><?php echo e(__('main.Не выбрано')); ?></li>
                                  <li class="general-drop__item js-drop-contains" v-for="(item, slug) in categories" @click="query.category = slug" :class="{active: query.category == slug}">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item general-filter__item-area">
                        <h4 class="general-filter__caption"><?php echo e(__('main.Область')); ?></h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="query.region? regions[query.region] : '<?php echo e(__('main.Не выбрано')); ?>'" readonly="">
                                <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                  <li class="general-drop__item js-drop-contains" @click="query.region = null" :class="{active: query.region == null}"><?php echo e(__('main.Не выбрано')); ?></li>
                                  <li class="general-drop__item js-drop-contains" v-for="(item, key) in regions" @click="query.region = key" :class="{active: query.region == key}">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item general-filter__item-years">
                        <h4 class="general-filter__caption"><?php echo e(__('main.Год')); ?></h4>
                        <div class="general-drop js-drop-item">
                            <button type="button" class="general-filter__button js-drop-button">
                                <input type="text" class="general-drop-input js-drop-input" :value="query.year? years[query.year] : '<?php echo e(__('main.Не выбрано')); ?>'" readonly="">
                              <span class="icon-drop"></span>
                            </button>
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                  <li class="general-drop__item js-drop-contains" @click="query.year = null" :class="{active: query.year == null}"><?php echo e(__('main.Не выбрано')); ?></li>
                                  <li class="general-drop__item js-drop-contains" v-for="(item, key) in years" @click="query.year = key" :class="{active: query.year == key}">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="general-filter__item general-filter__item-sort">
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
            <div v-if="loading" class="popular__block__list" style="height: 250px;display: flex;">
                <img src="/img/preload-for-files.gif" style="height: 2px;margin: auto;" alt="">
            </div>
            <ul class="popular__block__list" v-else>
              <articlecard v-for="(article, key) in articles.data" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
            </ul>
            <div v-if="!articles.total"><?php echo e(__('main.По вашему запросу не найдено записей')); ?>.</div>
            <div class="pagination__wrapper" v-if="articles.last_page != 1" v-cloak>
                <div class="pagination__container">
                    <button class="general-button" @click="query.page = 1" v-bind:class="{disabled: query.page == 1}">
                        <span class="icon-pagi-left"></span>
                    </button>
                    <button class="general-button" v-bind:class="{disabled: articles.current_page == 1}" @click="query.page--">
                        <span class="icon-arrow-pagi-left"></span>
                    </button>
                    <ul class="pagination__list">
                        <li class="pagination__item" @click="query.page = 1" v-bind:class="{active: query.page == 1}">
                            <button>1</button>
                        </li>
                        <li class="pagination__item dots" v-if="articles.last_page > 7 && query.page - 1 > 3">
                            <button>...</button>
                        </li>
                        <li class="pagination__item" v-for="page in (articles.last_page - 1)" @click="query.page = page" v-bind:class="{active: page == query.page}" v-show="page != 1 && ((query.page == 1 && page <= 6) || (query.page == articles.last_page && page >= articles.last_page - 5) || (Math.abs(query.page - page) < 3) || (query.page <= 3 && page <= 6) || (query.page >= articles.last_page - 3 && page >= articles.last_page - 6))">
                            <button>{{ page }}</button>
                        </li>
                        <li class="pagination__item dots" v-if="articles.last_page > 7 && articles.last_page - query.page > 3">
                            <button>...</button>
                        </li>
                        <li class="pagination__item" @click="query.page = articles.last_page" v-if="articles.last_page != 1" v-bind:class="{active: articles.last_page == query.page}">
                            <button>{{ articles.last_page }}</button>
                        </li>
                    </ul>
                    <button class="general-button" v-bind:class="{disabled: articles.current_page == articles.last_page}" @click="query.page++">
                        <span class="icon-arrow-pagi-right"></span>
                    </button>
                    <button class="general-button" @click="query.page = articles.last_page" v-if="articles.last_page != 1" v-bind:class="{disabled: articles.last_page == query.page}">
                        <span class="icon-pagi-right"></span>
                    </button>
                </div>
                <button @click="loadmore()" v-if="articles.current_page != articles.last_page" class="main-button-more">
                    <span class="text"><?php echo e(__('main.Показать больше')); ?></span>
                </button>
            </div>
            <div class="subscribe-block subscribe-block-alone">
                <h5 class="subscribe-block__text"><?php echo e(__('main.Нашли полезную информацию?')); ?><br><?php echo e(__('main.Подписывайтесь на актуальные публикации')); ?>:</h5>
                <?php echo $__env->make('modules.subscription', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </section>

    <section class="info-block" v-if="seo_text && query.page == 1">
        <div class="info-block__wrapper container">
            <!-- <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"></h2>
            </div> -->
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
  var articles = <?php echo json_encode($articles, 15, 512) ?>;
  var parent_categories = <?php echo json_encode($parent_categories, 15, 512) ?>;
  var categories = <?php echo json_encode($categories, 15, 512) ?>;
  var regions = <?php echo json_encode($regions, 15, 512) ?>;
  var years = <?php echo json_encode($years, 15, 512) ?>;
  var currentCategorySlug = <?php echo json_encode($category? $category->slug : null, 15, 512) ?>;
  var currentThemeSlug = <?php echo json_encode($currentThemeSlug, 15, 512) ?>;
  var sorts = <?php echo json_encode(__('sorts.articles'), 15, 512) ?>;
  var seo_text = <?php echo json_encode($seo_text, 15, 512) ?>;
  var page = <?php echo json_encode(request('page')? request('page') : 1, 15, 512) ?>;
  var year = <?php echo json_encode($year, 15, 512) ?>;
  var region = <?php echo json_encode($region, 15, 512) ?>;
  var sort = <?php echo json_encode($sort? $sort : 'date_desc', 15, 512) ?>;
</script>
</script>
<script src="<?php echo e(url('js/news/news.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'meta_title' => $meta_title? $meta_title : __('main.Журнал'),
  'meta_desc' => $meta_desc? $meta_desc : __('main.Журнал')
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/news/index.blade.php ENDPATH**/ ?>