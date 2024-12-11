

<?php $__env->startSection('product_content'); ?>
<div class="product-page__main product-page__main-tabs ts-promotions-tab-ln-4">
    <div class="product-page__video">
        <h4 class="ts-product__video-title product-page__caption product-page__caption-l"><?php echo e(__('main.Акции о')); ?> <?php echo e($product->name); ?></h4>

        <div class="product-page__video-list">
            <?php if(isset($product->promotions) && $product->promotions->count()): ?>
                <ul class="product__list product__list-sale">
                    <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" @add-to-favorites="addToFavorites"></promotioncard>
                </ul>
            <?php else: ?>
                <span class="product-page__promotions-item"><?php echo e(__('main.У этого объекта нет пока акций')); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    var product = <?php echo json_encode($product, 15, 512) ?>;
    var promotions = <?php echo json_encode($promotions, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/product/promotions.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('product.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/product/promotions.blade.php ENDPATH**/ ?>