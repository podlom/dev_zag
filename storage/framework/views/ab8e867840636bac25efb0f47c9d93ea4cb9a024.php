

<?php $__env->startSection('content'); ?>
<main>
        <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
        <section class="breadcrumbs">
            <div class="breadcrumbs__wrapper">
                <div class="breadcrumbs__list">
                    <a href="<?php echo e($lang === 'ru'? url('/') : url($lang)); ?>" class="breadcrumbs__link"><?php echo e(__('main.Главная')); ?></a> 
                    <a href="#" class="breadcrumbs__link">{{ h1 }}</a>
                </div>
            </div>
        </section>
        <section class="reviews-page">
            <div class="reviews-page__wrapper container">
                <div class="general-heading">
                    <h1 class="main-caption-l main-caption-l--transform" v-cloak>{{ h1 }} <span v-if="query.page > 1"> ➨ <?php echo e(__('main.страница')); ?> {{ query.page }}</span></h1>
                    <div class="general-drop general-top__drop js-drop-item" v-show="query.type != 'zagorodna' && query.type != 'realexpo'">
                        <button type="button" class="general-top__drop__button js-drop-button general-drop__text"> 
                            <span class="text" v-cloak>{{ sorts[query.sort] }}</span>
                            <span class="icon-drop"></span>
                        </button>
                        <div class="general-drop__wrapper">
                            <ul class="general-drop__list">
                                <li class="general-drop__item js-drop-contains" v-for="(sort, key) in sorts" @click="query.sort = key" :class="{active: query.sort == key}">{{ sort }}</li>
                            </ul>
                        </div>
                    </div>
                    <button class="main-button js-review-button" v-show="query.type == 'zagorodna' || query.type == 'realexpo'"></button>
                </div>
                <div class="general-tabs">
                    <ul class="general-tabs__list">
                        <li class="general-tabs__item" @click.prevent="query.type = 'zagorodna'" :class="{active: query.type == 'zagorodna'}"><?php echo e(__('main.о Zagorodna com')); ?></li>
                        <li class="general-tabs__item" @click.prevent="query.type = 'realexpo'" :class="{active: query.type == 'realexpo'}"><?php echo e(__('main.о “Реал Экспо”')); ?></li>
                        <!-- <li class="general-tabs__item" @click.prevent="query.type = 'brand'" :class="{active: query.type == 'brand'}"><?php echo e(__('main.о застройщиках')); ?></li>
                        <li class="general-tabs__item" @click.prevent="query.type = 'cottage'" :class="{active: query.type == 'cottage'}"><?php echo e(__('main.о коттеджных городках')); ?></li>
                        <li class="general-tabs__item" @click.prevent="query.type = 'newbuild'" :class="{active: query.type == 'newbuild'}"><?php echo e(__('main.о новостройках')); ?></li>
                        <li class="general-tabs__item general-tabs__item-empty"></li> -->
                    </ul>
                </div>
                <div class="add-reviews js-review-drop" v-show="query.type == 'zagorodna' || query.type == 'realexpo'">
                    <h4 class="product-page__caption-l"><?php echo e(__('main.Оцените объект')); ?></h4>
                    <div class="star__wrapper star__wrapper-set">
                        <ul class="star__list">
                            <li class="start__item js-stars-item" data-index="2">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item js-stars-item" data-index="4">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item js-stars-item" data-index="6">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item js-stars-item" data-index="8">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item js-stars-item" data-index="10">
                                <span class="icon-star"></span>
                            </li>
                        </ul>
                    </div>
                    <form :action="'/reviews/create/' + query.type" method="post" class="reviews__form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" :name="query.type + '_review_rating'" class="js-input-stars" value="">
                        <label class="textarea__wrapper">
                            <span class="input__caption"><?php echo e(__('main.Отзыв')); ?></span>
                            <textarea class="main-textarea" :name="query.type + '_review_text'" placeholder="<?php echo e(__('forms.placeholders.Напишите отзыв')); ?>"></textarea>
                        </label>
                        <label class="input__wrapper">
                            <span class="input__caption"><?php echo e(__('main.Имя')); ?></span>
                            <input type="text" class="main-input" :name="query.type + '_review_name'" placeholder="<?php echo e(__('forms.placeholders.Как к вам обращаться?')); ?>">
                        </label>
                        <label class="input__wrapper">
                            <span class="input__caption">Email*</span>
                            <input type="email" class="main-input" :name="query.type + '_review_email'" placeholder="<?php echo e(__('forms.placeholders.Ваш электронный адрес')); ?>">
                        </label>
                        <button class="main-button"><?php echo e(__('main.Опубликовать')); ?></button>
                    </form>
                </div>
                <div v-if="loading" class="reviews__list" style="height: 500px">
                    <img src="/img/preload-for-files.gif" style="height: 2px;margin: 150px auto;" alt="">
                </div>
                <ul :class="{reviews__list: query.type == 'zagorodna' || query.type == 'realexpo', 'product-page__reviews-list reviews__list-buildings': query.type != 'zagorodna' && query.type != 'realexpo'}" v-else-if="reviews.total">
                  <reviewCard v-for="(review, key) in reviews.data" :data-review="review" :data-type="(query.type != 'zagorodna' && query.type != 'realexpo')? 'product_image' : 'zagorodna'" :key="key"></reviewCard>
                </ul>
                <div class="product-page__reviews-list reviews__list-buildings" v-else><?php echo e(__('main.Отзывы отсутствуют')); ?></div>
                <div class="pagination__wrapper" v-if="reviews.last_page != 1">
                    <div class="pagination__container">
                        <button class="general-button" @click="query.page = 1" v-bind:class="{disabled: query.page == 1}">
                            <span class="icon-pagi-left"></span>
                        </button>
                        <button class="general-button" v-bind:class="{disabled: reviews.current_page == 1}" @click="query.page--">
                            <span class="icon-arrow-pagi-left"></span>
                        </button>
	                    <ul class="pagination__list">
                            <li class="pagination__item" @click="query.page = 1" v-bind:class="{active: query.page == 1}">
	                            <button>1</button>
                            </li>
	                        <li class="pagination__item dots" v-if="reviews.last_page > 7 && query.page - 1 > 3">
	                            <button>...</button>
                            </li>
	                        <li class="pagination__item" v-for="page in (reviews.last_page - 1)" @click="query.page = page" v-bind:class="{active: page == query.page}" v-show="page != 1 && ((query.page == 1 && page <= 6) || (query.page == reviews.last_page && page >= reviews.last_page - 5) || (Math.abs(query.page - page) < 3) || (query.page <= 3 && page <= 6) || (query.page >= reviews.last_page - 3 && page >= reviews.last_page - 6))">
	                            <button>{{ page }}</button>
	                        </li>
	                        <li class="pagination__item dots" v-if="reviews.last_page > 7 && reviews.last_page - query.page > 3">
	                            <button>...</button>
                            </li>
                            <li class="pagination__item" @click="query.page = reviews.last_page" v-if="reviews.last_page != 1" v-bind:class="{active: reviews.last_page == query.page}">
	                            <button>{{ reviews.last_page }}</button>
	                        </li>
	                    </ul>
	                    <button class="general-button" v-bind:class="{disabled: reviews.current_page == reviews.last_page}" @click="query.page++">
                            <span class="icon-arrow-pagi-right"></span>
                        </button>
                        <button class="general-button" @click="query.page = reviews.last_page" v-if="reviews.last_page != 1" v-bind:class="{disabled: reviews.last_page == query.page}">
                            <span class="icon-pagi-right"></span>
                        </button>
                    </div>
                    <button @click="loadmore()" v-if="reviews.current_page != reviews.last_page" class="main-button-more">
                        <span class="text"><?php echo e(__('main.Показать больше')); ?></span>
                    </button>
                </div>
            </div>
        </section>
        <section class="seo-block" v-if="seo_text && query.page == 1">
            <div class="seo-block__wrapper container">
                <h2 class="main-caption-l">{{ seo_title }}</h2>
                <div class="seo-block__content">
                    <div class="wrapper" v-html="seo_text">
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        // Stars 
        document.addEventListener("click", function(e){
            let item = e.target;
            
            if(item.closest('.js-stars-item')) {
                let value = item.closest('.js-stars-item').getAttribute('data-index');
                let allStars = document.querySelectorAll(".js-stars-item");
                
                allStars.forEach(function(item){
                    item.classList.remove('active');
                });
                item.closest('.js-stars-item').classList.add('active');
                item.closest('.star__list').classList.add('active');
                document.querySelector(".js-input-stars").value = value;
            }
        });
        
        // //Stars 
        
        // Review
        
        document.addEventListener('click', function(e){
            let item = e.target;
            
            if(item.closest('.js-review-button')) {
                let reviewContainer = document.querySelector('.js-review-drop');
                item.closest('.js-review-button').classList.toggle('active');
                reviewContainer.classList.toggle('active');
            }
        });
    });
</script>
<script>
  var reviews = <?php echo json_encode($reviews, 15, 512) ?>;
  var type = <?php echo json_encode($type? $type : 'zagorodna', 15, 512) ?>;
  var h1 = <?php echo json_encode($h1, 15, 512) ?>;
  var meta_title = <?php echo json_encode($meta_title, 15, 512) ?>;
  var seo_title = <?php echo json_encode($seo_title, 15, 512) ?>;
  var seo_text = <?php echo json_encode($seo_text, 15, 512) ?>;
  var page = <?php echo json_encode(request('page')? request('page') : 1, 15, 512) ?>;
  var slug = <?php echo json_encode($slug, 15, 512) ?>;
  var types = <?php echo json_encode($types, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/reviews/reviews.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'meta_title' => $meta_title,
  'meta_desc' => $meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/reviews/index.blade.php ENDPATH**/ ?>