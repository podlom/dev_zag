

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(./img/background-img-2.png)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render('page', $page->title)); ?>

        </div>
    </section>
    <section class="article-page policy">
        <div class="article-page__wrapper container">
            <div class="general-heading">
                <h1 class="ts-common-blade-pages-ln-17 main-caption-l main-caption-l--transform"><?php echo e($page->title); ?></h1>
            </div>
            <?php if($page->content): ?>
            <article class="article-main">
                <div class="article-main__body">
                <?php echo $page->content; ?>

                </div>
            </article>
            <?php endif; ?>
        </div>
    </section>

    <?php if($page->seo_text): ?>
    <section class="info-block">
        <div class="info-block__wrapper container">
            <!-- <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"></h2>
            </div> -->
            <div class="info-block__container">
                <div class="info-block__inner">
                <?php echo $page->seo_text; ?>

                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(url('js/app.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', [
  'meta_title' => $page->meta_title,
  'meta_desc' => $page->meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/pages/common.blade.php ENDPATH**/ ?>