

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render('product_tab', $product, 'Рейтинг', 'rating')); ?>

        </div>
    </section>
    <section class="product-page-tabs product-page-tabs--rating">
        <div class="product-page-tabs__wrapper container">
            <div class="product-page-tabs__header">
                <div class="product-page-tabs__header-img">
                    <img src="<?php echo e($product->image? url($product->image) : url('files/47/net-fot500x500.jpg')); ?>" alt="<?php echo e(__('main.Фото')); ?>: <?php echo e($product->name); ?>" title="<?php echo e(__('main.Картинка')); ?>: <?php echo e($product->name); ?>">
                </div>
                <div class="product-page-tabs__header-info">
                    <div class="wrapper">
                        <h1 class="main-caption-l main-caption-l--transform">Рейтинг <?php echo e(mb_strtolower(__('main.type_' . $type . '_genitive'))); ?> <?php echo e($product->name); ?></h1>
                        <div class="general-noty__buttons-container">
                            <?php if($product->category_id == 2 || $product->category_id == 7): ?>
                            <button class="general-noty__button general-noty__button-compare" @click="addToComparison(<?php echo e($product); ?>)" :class="{active: comparison.includes(<?php echo e($product->id); ?>) || comparison.includes(<?php echo e($product->original_id); ?>)}" title="<?php echo e(__('main.Добавить в сравнение')); ?>">
                                <span class="icon-compare"></span>
                            </button>
                            <?php endif; ?>
                            <button class="general-noty__button general-noty__button-favorite" @click="addToFavorites(<?php echo e($product); ?>, 'products')" :class="{active: favorites['products'].includes(<?php echo e($product->id); ?>) || favorites['products'].includes(<?php echo e($product->original_id); ?>)}" title="<?php echo e(__('main.Добавить в избранное')); ?>">
                                <span class="icon-heart-outline"></span>
                            </button>
                            <button class="general-noty__button general-noty__button-sing-up" @click="addToNotifications(<?php echo e($product); ?>, 'products')" :class="{active: notifications['products'].includes(<?php echo e($product->id); ?>) || notifications['products'].includes(<?php echo e($product->original_id); ?>)}" title="<?php echo e(__('main.Добавить в уведомления')); ?>">
                                <span class="icon-bell-outline"></span>
                            </button>
                        </div>
                    </div>
                    <div class="info">
                        <div class="rating-info">
                            <span class="rating-icon"></span>
                            <p class="rating-info__name"><strong><?php echo e($rating_position); ?> <?php echo e(__('main.место в общем рейтинге')); ?></strong></p>
                        </div>
                        <?php if($product->brand): ?>
                        <div class="project">
                            <span><?php echo e(__('main.Проект от')); ?>:</span>
                            <h5><?php echo e($product->brand->name); ?></h5>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="rating-block__table rating-block__table--project">
                <div class="rating-block__table__container">
                    <div class="rating-block__table__caption">
                        <p class="table-type"><strong>Характеристика</strong></p>
                        <p class="table-description"><strong><?php echo e(__('main.Описание')); ?></strong></p>
                        <p class="table-rating"><strong><?php echo e(__('main.Оценка')); ?></strong></p>
                    </div>
                    <div class="wrapper">
                        <?php $__currentLoopData = $table; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rating-block__table__item">
                            <p class="table-type"><?php echo e($attr); ?></p>
                            <p class="table-description"><?php echo e($row['value']); ?></p>
                            <p class="table-rating table-rating--active"><?php echo e($row['rating']); ?></p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="rating-block__table__item">
                            <p class="table-type"><?php echo e(__('main.Общая сумма баллов')); ?></p>
                            <p class="table-description"></p>
                            <p class="table-rating table-rating--active"><?php echo e($product->top_rating); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Рядом в рейтинге')); ?></h2>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <template v-if="products.data.length">
                    <productcard v-for="(product, key) in products.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
                </template>
                <img src="<?php echo e(url('img/preload-for-files.gif')); ?>" style="margin:auto" v-else>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper">
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
    <?php if(false): ?>
    <section class="info-block">
        <div class="info-block__wrapper container">
            <!-- <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"></h2>
            </div> -->
            <div class="info-block__container">
                <div class="info-block__inner" >
                
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(url('js/product/rating.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'meta_title' => 'Рейтинг '  . mb_strtolower(__('main.type_' . $type . '_genitive')) . ' ' . $product->name,
  'meta_desc' => 'Рейтинг ' . mb_strtolower(__('main.type_' . $type . '_plural_genitive_alt')) . ' ➨ Рейтинг '  . mb_strtolower(__('main.type_' . $type . '_genitive')) . ' ' . $product->name,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/product/rating.blade.php ENDPATH**/ ?>