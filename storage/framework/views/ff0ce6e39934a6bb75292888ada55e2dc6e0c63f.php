

<?php $__env->startSection('product_content'); ?>
<div class="product-page__main product-page__main-tabs">
    <div class="product-page__map">
        <h4 class="product-page__caption product-page__caption-l"><?php echo e(__('main.Местоположение')); ?></h4>
        <p class="product-page__map-address">
            <span class="icon-map-marker-outline"></span>
            <span><?php echo e($product->address_string); ?></span>
        </p>
        <div class="product-page__map-container ts-lat__<?php echo e($product->getLatAttribute()); ?>__ts ts-lng__<?php echo e($product->getLngAttribute()); ?>__ts" id="general__map">
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <!-- link href='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.css' rel='stylesheet' / -->

    <!-- link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/ -->
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <!-- script src='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js'></script -->

    <!-- script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script -->
<script>
    var tsLang = '<?php echo e($lang); ?>';

    /* var map = L.map('general__map').setView([<?php echo e($product->getLatAttribute()); ?>, <?php echo e($product->getLngAttribute()); ?>], 16); */

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('product.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/product/map.blade.php ENDPATH**/ ?>