

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render('precatalog_statistics', $category)); ?>

        </div>
    </section>
    <section class="rating-block rating-block-info" style="padding-top:30px">
        <div class="general-heading container">
            <h1 class="main-caption-l main-caption-l--transform"><?php echo e($page[$type . '_title']); ?></h1>
        </div>
        <div class="rating-block__wrapper container">
            <div class="rating-drop__info">
                <div class="js-drop-item rating-drop__wrapper active">
                    <div class="rating-block__general-wrapper">
                        <ul class="rating-block__list rating-block__list-diagram">
                            <li class="rating-block__item">
                                <div class="rating-block__item__header">
                                    <span class="rating-block-icon-diagram"></span>
                                    <h3 class="rating-block__item__caption"><?php echo e(__('main.Статистика')); ?> (грн) - <?php echo e(__('main.type_' . $type . '_plural')); ?></h3>
                                </div>
                                <div class="rating-block__table">
                                    <div class="wrapper">
                                        <div class="rating-block__general-info">
                                            <a href="<?php echo e(route($lang . '_precatalog', $category->slug)); ?>" class="name"><?php echo e(__('main.Украина')); ?></a>
                                            <p class="date"><?php echo e($statistics->first()->date); ?> - <span><?php echo e($statistics->first()->total); ?></span></p>
                                            <p class="date"><?php echo e($statistics->last()->date); ?> - <span><?php echo e($statistics->last()->total); ?></span></p>
                                        </div>
                                        <?php if($type == 'cottage'): ?>
                                        <div class="rating-block__general-info">
                                            <a href="<?php echo e(route($lang . '_precatalog', $category->slug) . '/region/' . \App\Region::where('region_id', 29)->first()->slug); ?>" class="name"><?php echo e(__('main.Киев')); ?></a>
                                            <p class="date"><?php echo e($statistics->first()->date); ?> - <span><?php echo e($statistics->first()->data['29']); ?></span></p>
                                            <p class="date"><?php echo e($statistics->last()->date); ?> - <span><?php echo e($statistics->last()->data['29']); ?></span></p>
                                        </div>
                                        <?php else: ?>
                                        <div class="rating-block__general-info">
                                            <a href="<?php echo e(route($lang . '_precatalog', $category->slug) . '/region/' . \App\Region::where('region_id', 11)->first()->slug); ?>" class="name"><?php echo e(__('main.Киевская')); ?></a>
                                            <p class="date"><?php echo e($statistics->first()->date); ?> - <span><?php echo e($statistics->first()->data['11']); ?></span></p>
                                            <p class="date"><?php echo e($statistics->last()->date); ?> - <span><?php echo e($statistics->last()->data['11']); ?></span></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rating-block__table__caption">
                                        <p class="table-number">№</p>
                                        <p class="table-area"><?php echo e(__('main.Область')); ?></p>
                                        <p class="table-date"><?php echo e($statistics->first()->date); ?></p>
                                        <p class="table-date"><?php echo e($statistics->last()->date); ?></p>
                                    </div>
                                    <div class="wrapper">
                                        <?php
                                        $i = 1;
                                        $data = $statistics->first()->data;
                                        arsort($data);
                                        ?>
                                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($key != 5 && $key != 13): ?> <!-- Луганская и Донецкая -->
                                        <?php if((($type == 'cottage' && $key != 29) || ($type == 'newbuild' && $key != 11)) && $item): ?>
                                        <?php
                                        $reg = \App\Region::where('region_id', $key)->first();
                                        ?>
                                        <div class="rating-block__table__item">
                                            <p class="table-number"><?php echo e($i++); ?></p>
                                            <a href="<?php echo e(route($lang . '_precatalog', $category->slug) . '/region/' . $reg->slug); ?>" class="table-name"><?php echo e($reg->name); ?></a>
                                            <p class="table-rating"><?php echo e($item); ?></p>
                                            <p class="table-rating"><?php echo e($statistics->last()->data[$key]); ?></p>
                                        </div>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php if($page[$type . '_seo_text']): ?>
    <section class="info-block">
        <div class="info-block__wrapper container">
            <!-- <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"></h2>
            </div> -->
            <div class="info-block__container">
                <div class="info-block__inner">
                <?php echo $page[$type . '_seo_text']; ?>

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
    'meta_title' => $page[$type . '_meta_title'],
    'meta_desc' => $page[$type . '_meta_desc'],
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/catalog/statistics.blade.php ENDPATH**/ ?>