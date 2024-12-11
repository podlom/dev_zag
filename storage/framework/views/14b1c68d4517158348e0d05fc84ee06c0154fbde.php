

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render($slug? 'research' : 'researches', $slug? $page[$slug . '_title'] : '')); ?>

        </div>
    </section>
    <section class="researches">
        <div class="researches__wrapper container">
            <?php if(!$slug): ?>
            <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Маркетинговые исследования')); ?></h2>
            <ul class="researches__list">
                <li class="researches__item">
                    <?php if($page->all_image): ?>
                    <div class="img" style="background-image:url(<?php echo e(url($page->all_image)); ?>)"></div>
                    <?php endif; ?>
                    <a href="<?php echo e(url($page_slug . '/all')); ?>" class="researches__link">
                        <span class="text"><?php echo e($page->all_title); ?></span>
                        <span class="icon-arrow-more"></span>
                    </a>
                </li>
                <li class="researches__item">
                    <?php if($page->cottage_image): ?>
                    <div class="img" style="background-image:url(<?php echo e(url($page->cottage_image)); ?>)"></div>
                    <?php endif; ?>
                    <a href="<?php echo e(url($page_slug . '/cottage')); ?>" class="researches__link">
                        <span class="text"><?php echo e($page->cottage_title); ?></span>
                        <span class="icon-arrow-more"></span>
                    </a>
                </li>
                <li class="researches__item">
                    <?php if($page->realexpo_image): ?>
                    <div class="img" style="background-image:url(<?php echo e(url($page->realexpo_image)); ?>)"></div>
                    <?php endif; ?>
                    <a href="<?php echo e(url($page_slug . '/realexpo')); ?>" class="researches__link">
                        <span class="text"><?php echo e($page->realexpo_title); ?></span>
                        <span class="icon-arrow-more"></span>
                    </a>
                </li>
            </ul>
            <h1 class="main-caption-l main-caption-l--transform"><?php echo e($page->main_title); ?></h1>
            <div class="researches__text"><?php echo $page->main_description; ?></div>
            <?php else: ?>
            <h1 class="main-caption-l main-caption-l--transform"><?php echo e($page[$slug . '_title']); ?></h1>
            <div class="researches__text"><?php echo $page[$slug . '_description']; ?></div>
            <?php endif; ?>
            <?php if(!$slug): ?>
            <div class="researches__content">
                <form action="<?php echo e(url('research/create')); ?>" method="post" class="researches__form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="type" value="<?php echo e($slug? $slug : 'individual'); ?>">
                    <label class="textarea__wrapper <?php $__errorArgs = ['theme'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <span class="input__caption">1. <?php echo e(__('main.Тематика исследования (цели, задачи, проблемы и т д )')); ?>*</span>
                        <textarea class="main-textarea" name="theme" placeholder="<?php echo e(__('forms.placeholders.Опишите ключевые пункты')); ?>"><?php echo e(old('theme')); ?></textarea>
                        <?php $__errorArgs = ['theme'];
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
                    <label class="input__wrapper <?php $__errorArgs = ['region'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <span class="input__caption">2. <?php echo e(__('main.Регион исследования')); ?>*</span>
                        <input type="text" class="main-input" name="region" value="<?php echo e(old('region')); ?>" placeholder="<?php echo e(__('forms.placeholders.Укажите регион')); ?>">
                        <?php $__errorArgs = ['region'];
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
                    <div class="input__wrapper">
                        <span class="input__caption">3. <?php echo e(__('main.Методика исследования')); ?></span>
                        <div class="general-drop general-top__drop js-drop-item">
                            <button type="button" class="general-top__drop__button js-drop-button general-drop__text"> 
                                <span class="text"><?php echo e(old('method')? old('method') : __('forms.placeholders.Выберите методику')); ?></span>
                                <span class="icon-drop"></span>
                            </button>
                            <input type="hidden" name="method" class="js-drop-input" value="<?php echo e(old('method')); ?>">
                            <div class="general-drop__wrapper">
                                <ul class="general-drop__list">
                                    <?php $__currentLoopData = json_decode($page->methods); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="general-drop__item js-drop-contains <?php if(old('method') == $method->name): ?> active <?php endif; ?>"><?php echo e($method->name); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <label class="input__wrapper">
                        <span class="input__caption">4. <?php echo e(__('main.Структура и объем выборки')); ?></span>
                        <input type="text" class="main-input" name="structure" value="<?php echo e(old('structure')); ?>" placeholder="<?php echo e(__('forms.placeholders.Укажите структуру и объем')); ?>">
                    </label>
                    <label class="textarea__wrapper">
                        <span class="input__caption">5. <?php echo e(__('main.Дополнительная информация')); ?></span>
                        <textarea class="main-textarea" name="info" placeholder="<?php echo e(__('forms.placeholders.Напишите дополнительно')); ?>"><?php echo e(old('info')); ?></textarea>
                    </label>
                    <label class="input__wrapper <?php $__errorArgs = ['organization'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <span class="input__caption"><?php echo e(__('main.Организация')); ?>*</span>
                        <input type="text" class="main-input" name="organization" placeholder="<?php echo e(__('forms.placeholders.Название организации')); ?>" value="<?php echo e(old('organization')); ?>">
                        <?php $__errorArgs = ['organization'];
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
                    <label class="input__wrapper <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <span class="input__caption"><?php echo e(__('main.ФИО')); ?>*</span>
                        <input type="text" class="main-input" name="name" placeholder="<?php echo e(__('forms.placeholders.Как к вам обращаться?')); ?>" value="<?php echo e(old('name')); ?>">
                        <?php $__errorArgs = ['name'];
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
                    <label class="input__wrapper <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <span class="input__caption"><?php echo e(__('main.Контактный телефон')); ?>*</span>
                        <input type="tel" class="main-input" name="phone" placeholder="<?php echo e(__('forms.placeholders.Номер телефона')); ?>" value="<?php echo e(old('phone')); ?>">
                        <?php $__errorArgs = ['phone'];
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
                    <label class="input__wrapper <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <span class="input__caption">Email*</span>
                        <input type="email" class="main-input" name="email" placeholder="<?php echo e(__('forms.placeholders.Ваш электронный адрес')); ?>" value="<?php echo e(old('email')); ?>">
                        <?php $__errorArgs = ['email'];
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
                    <button class="main-button"><?php echo e(__('main.Отправить')); ?></button>
                </form>
            </div>
            <?php endif; ?>
            <div class="article-page__more-news">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Читайте также')); ?></h2>
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
    
    <?php if($slug? $page[$slug . '_seo_text']: $page->main_seo_text): ?>
    <section class="info-block">
        <div class="info-block__wrapper container">
            <div class="info-block__container">
                <div class="info-block__inner">
                <?php echo $slug? $page[$slug . '_seo_text']: $page->main_seo_text; ?>

                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  var articles = <?php echo json_encode($articles, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/researches/researches.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'meta_title' => $slug? $page[$slug . '_meta_title']: $page->main_meta_title,
  'meta_desc' => $slug? $page[$slug . '_meta_desc']: $page->main_meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/pages/researches.blade.php ENDPATH**/ ?>