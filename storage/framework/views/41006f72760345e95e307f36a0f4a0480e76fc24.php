

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render('page', $page->main_title)); ?>

        </div>
    </section>
    <section class="article-page policy">
        <div class="article-page__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l main-caption-l--transform"><?php echo e($page->main_title); ?></h1>
            </div>
            <?php if($lang != 'ru'): ?>
            <a href="<?php echo e(url( 'ru/'.substr(\Request::path(),3) )); ?>" class="translate-article">Читать на русском</a>
            <?php else: ?>
            <a href="<?php echo e(url( 'uk/'.substr(\Request::path(),3) )); ?>" class="translate-article">Читати українською</a>
            <?php endif; ?>
            <article class="article-main">
                <div class="article-main__body">
                <?php echo $page->content; ?>

                </div>
            </article>
        </div>
    </section>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(url('js/app.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'meta_title' => $page->meta_title,
  'meta_desc' => $page->meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/pages/about.blade.php ENDPATH**/ ?>