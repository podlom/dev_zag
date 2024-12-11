

<?php $__env->startSection('product_content'); ?>

<div class="product-page__main product-page__main-tabs">
    <div class="product__wrapper product__wrapper-list-position">
        <ul class="product__list product__list-tabs ts-ln-7">
            <projectcard v-for="(project, key) in projects" :key="key" :data-project="project" @add-to-favorites="addToFavorites"></projectcard>
        </ul>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
    var product = <?php echo json_encode($product, 15, 512) ?>;
    var projects = <?php echo json_encode($projects, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/product/projects.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('product.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/product/projects.blade.php ENDPATH**/ ?>