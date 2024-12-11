<?php if(count($breadcrumbs)): ?>
    <div class="breadcrumbs__list">
    <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php if($breadcrumb->url && !$loop->last): ?>
            <a href="<?php echo e($breadcrumb->url); ?>" class="breadcrumbs__link"><?php echo e($breadcrumb->title); ?></a>
        <?php else: ?>
            <a href="#" class="breadcrumbs__link"><?php echo e($breadcrumb->title); ?></a>
        <?php endif; ?>
    
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>
<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/davejamesmiller/laravel-breadcrumbs/src/../views//bootstrap4.blade.php ENDPATH**/ ?>