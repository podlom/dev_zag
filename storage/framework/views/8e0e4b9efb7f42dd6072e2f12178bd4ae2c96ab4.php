

<?php $__env->startSection('company_content'); ?>
<div class="company-page__content__info">
    <div class="company-page__full-info">
        <div class="company-page__full-info__text">
            <?php echo $company->description; ?>

        </div>
    </div>
    <?php if($company->activity): ?>
    <div class="company-page__actions">
        <h3 class="company-page__info-caption"><?php echo e(__('main.Деятельность компании')); ?></h3>
        <ul class="company-page__actions__list">
            <?php $__currentLoopData = json_decode($company->activity); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="company-page__actions__item"><?php echo e($item->text); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<?php if($company->achievements): ?>
<div class="company-page__slider slider-infinity">
    <h3 class="company-page__info-caption"><?php echo e(__('main.Достижения')); ?></h3>
    <ul class="company-page__slider__list js-infinity-slider-list">
        <?php $__currentLoopData = $company->achievements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="company-page__slider__item js-slider-item-infinity <?php if($key == 0): ?> show <?php endif; ?>">
            <div class="company-page__slider__img">
                <img src="<?php echo e(url($item['image'])); ?>" alt="<?php echo e($item['name']); ?>">
            </div>
            <p class="company-page__slider__description"><?php echo e($item['name']); ?></p>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <?php if(count($company->achievements) > 4): ?>
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
    <?php endif; ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('companies.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/companies/show.blade.php ENDPATH**/ ?>