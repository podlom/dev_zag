

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <div class="breadcrumbs__list">
                <a href="<?php echo e(route($lang . '_home')); ?>" class="breadcrumbs__link"><?php echo e(__('main.Главная')); ?></a> 
                <a href="#" class="breadcrumbs__link" v-cloak><?php echo e(__('main.Часто задаваемые вопросы по теме')); ?> "{{ categories[query.category] }}"</a>
            </div>
        </div>
    </section>
    <section class="faq">
        <div class="faq__wrapper container">
            <h1 class="main-caption-l main-caption-l--transform" v-cloak><?php echo e(__('main.Часто задаваемые вопросы по теме')); ?> "{{ categories[query.category] }}"</h1>
            <div class="faq__tabs-adaptation">
                <h4 class="faq__tabs-caption"><?php echo e(__('main.Категории вопросов')); ?></h4>
                <div class="general-drop general-top__drop js-drop-item">
                    <button type="button" class="general-top__drop__button js-drop-button general-drop__text"> 
                        <span class="text" v-cloak>{{ categories[query.category] }}</span>
                        <span class="icon-drop"></span>
                    </button>
                    <div class="general-drop__wrapper">
                        <ul class="general-drop__list">
                            <li class="general-drop__item js-drop-contains" v-for="(item, slug) in categories" @click="query.category = slug" :class="{active: query.category == slug}" v-cloak>{{ item }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="faq__container">
                <div class="faq__info">
                    <div v-if="loading" class="info-block__spoiler__list" style="height: 400px;display:flex;">
                        <img src="/img/preload-for-files.gif" style="height: 2px;margin: auto;">
                    </div>
                    <ul class="info-block__spoiler__list" v-else>
                        <li class="info-block__spoiler__item js-drop-item" v-for="(question, key) in questions" v-cloak>
                            <button class="info-block__spoiler__button js-drop-button">
                                <span class="text">{{ question.question }}</span>
                                <span class="icon-drop"></span>
                            </button>
                            <div class="info__wrapper" v-html="question.answer"></div>
                        </li>
                    </ul>
                </div>
                <div class="faq__tabs">
                    <h4 class="faq__tabs-caption"><?php echo e(__('main.Категории вопросов')); ?></h4>
                    <ul class="faq__tabs__list">
                        <li class="faq__tabs__item" v-for="(item, slug) in categories" @click="query.category = slug" :class="{active: query.category == slug}" v-cloak>{{ item }}</li>
                    </ul>
                </div>
            </div>
            <div class="faq__form-wrapper">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e($page->form_title); ?></h2>
                <form action="<?php echo e(url('feedback/create/question')); ?>" method="post" class="faq__form">
                    <?php echo csrf_field(); ?>
                    <div class="wrapper">
                        <div class="container-form">
                            <label class="input__wrapper">
                                <h5 class="input__caption"><?php echo e(__('main.Имя')); ?></h5>
                                <input type="text" class="main-input main-input-faq" name="question_name" placeholder="<?php echo e(__('forms.placeholders.Как к вам обращаться?')); ?>" value="<?php echo e(old('question_name')); ?>">
                            </label>
                            <label class="input__wrapper <?php $__errorArgs = ['question_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <h5 class="input__caption">Email</h5>
                                <input type="email" class="main-input main-input-faq" name="question_email" placeholder="<?php echo e(__('forms.placeholders.Ваш электронный адрес')); ?>" value="<?php echo e(old('question_email')); ?>">
                                <?php $__errorArgs = ['question_email'];
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
                        </div>
                        <label class="textarea__wrapper">
                            <h5 class="input__caption"><?php echo e(__('main.Вопрос')); ?></h5>
                            <textarea class="main-textarea main-textarea-faq" name="question_text" placeholder="Текст"><?php echo e(old('question_text')); ?></textarea>
                        </label>
                    </div>
                    <button class="main-button"><?php echo e(__('main.Отправить')); ?></button>
                </form>
            </div>
            <div class="article-page__more-news">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform"><?php echo e($page->news_title); ?></h2>
                </div>
                <ul class="popular__block__list popular__block__list-more">
                    <articlecard v-for="(article, key) in articles" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
                </ul>
            </div>
            <div class="subscribe-block subscribe-block-alone">
                <h5 class="subscribe-block__text"><?php echo e(__('main.Нашли полезную информацию?')); ?><br><?php echo e(__('main.Подписывайтесь на актуальные публикации')); ?>:</h5>
                <?php echo $__env->make('modules.subscription', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </section>

    <section class="info-block" v-if="seo_text">
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
  var questions = <?php echo json_encode($questions, 15, 512) ?>;
  var categories = <?php echo json_encode($categories, 15, 512) ?>;
  var currentCategorySlug = <?php echo json_encode($category->slug, 15, 512) ?>;
  var articles = <?php echo json_encode($articles, 15, 512) ?>;
  var seo_text = <?php echo json_encode($seo_text, 15, 512) ?>;
</script>
</script>
<script src="<?php echo e(url('js/faq/faq.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'meta_title' => $meta_title,
  'meta_desc' => $meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/pages/faq.blade.php ENDPATH**/ ?>