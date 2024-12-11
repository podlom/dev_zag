

<?php $__env->startSection('company_content'); ?>
<div class="company-page__content-tabs">
    <h4 class="product-page__caption product-page__caption-l"><?php echo e(__('main.Местоположение')); ?></h4>
    <p class="product-page__map-address">
        <span class="icon-map-marker-outline"></span>
        <span><?php echo e($company->address_string); ?></span>
    </p>
    <?php if(isset($company->address['latlng'])): ?>
    <div class="product-page__map-container" id="general__map">
    </div>
    <?php else: ?>
    <div class="product-page__map-empty"><p><?php echo e(__('main.Карта отсутствует')); ?></p></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('companies.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/companies/map.blade.php ENDPATH**/ ?>