<?php $__env->startSection('product_content'); ?>

<div class="product-page__main product-page__main-tabs" itemscope itemtype="http://schema.org/Apartment">
    <meta itemprop="accommodationCategory" content="Residential">
    <meta itemprop="address" content="<?php echo e($product->city); ?><?php echo e($product->address_string? ', ' . $product->address_string : ''); ?>">
    <meta itemprop="latitude" content="<?php echo e($product->lat); ?>">
    <meta itemprop="longitude" content="<?php echo e($product->lng); ?>">
    <div class="product-page__general-wrapper slider">
        <div class="product-page__general__caption-wrapper">
            <meta itemprop="name" content="<?php echo e($product->name); ?> - <?php echo e($project->name); ?>">
            <h3 class="name-project"><?php echo e($project->name); ?></h3>
            <h5 class="ts-type-ln-14 type-project"><?php echo e(__('main.' . $project->type)); ?></h5>
            <p class="product__status build product__status-tabs"><?php echo e($project->status_string); ?></p>
        </div>
        <div class="product-page__general-img js-general-image" itemprop="photo" itemscope itemtype="http://schema.org/ImageObject">
            <img src="<?php echo e(count($project->images)? url($project->images[0]) : url($project->product->image)); ?>" title="<?php echo e(__('main.Картинка')); ?>: <?php echo e($product->name); ?> - <?php echo e($project->name); ?>" alt="<?php echo e(__('main.Фото')); ?>: <?php echo e($product->name); ?> - <?php echo e($project->name); ?>" itemprop="url">
            <div class="product-page__img-header">
                <?php if(count($project->images) > 1): ?>
                <div class="product-page__slider-number js-slider-number">
                    <span class="current">1</span>
                    <span>/</span>
                    <span class="all"><?php echo e(count($project->images)); ?></span>
                </div>
                <?php endif; ?>
                <button class="general__open js-button" data-target="full-screen" title="<?php echo e(__('main.На весь экран')); ?>">
                    <span class="icon-full"></span>
                </button>
            </div>
        </div>
        <?php if(count($project->images) > 1): ?>
        <div class="product-page__img-navigation">
            <button class="general-button js-image-button-prev disabled">
                <span class="icon-arrow-left"></span>
            </button>
            <ul class="product-page__img-list">
              <?php $__currentLoopData = $project->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="product-page__img-item js-image <?php if($key == 0): ?> active <?php endif; ?>" data-index="<?php echo e($key + 1); ?>">
                    <img src="<?php echo e(url($image)); ?>" alt="<?php echo e($project->name); ?>">
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button class="general-button js-image-button-next">
                <span class="icon-arrow-right"></span>
            </button>
        </div>
        <?php endif; ?>
        <?php if($project->layouts && count($project->layouts)): ?>
        <div class="product-page__plan" itemprop="accommodationFloorPlan" itemscope itemtype="http://schema.org/FloorPlan">
            <h4 class="product-page__caption"><?php echo e(__('main.Планировки')); ?></h4>
            <ul class="product-page__plan-list">
                <?php $__currentLoopData = $project->layouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $layout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="product-page__plan-item js-button js-button-plan" data-target="full-screen-plan" data-index="<?php echo e($key + 1); ?>">
                    <p class="name" itemprop="name"><?php echo e($layout['name']); ?></p>
                    <div class="product-page__plan-img" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
                        <img src="<?php echo e(url($layout['image'])); ?>" alt="Фото: <?php echo e($layout['name']); ?>" title="Картинка: <?php echo e($layout['name']); ?>" itemprop="url">
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <div class="ts-project-blade product-page__info-wrapper product-page__info-wrapper-tabs">
        <div class="product-page__status">
            <p class="product__status build"><?php echo e($project->status_string); ?></p>
        </div>
        <div class="product-page__info-body ts-project-blade">
            <div class="product-page__buttons product-page__buttons-tabs ts-project-status__<?php echo e($product->status); ?> ts-product-is_sold__<?php echo e($product->is_sold); ?> ts-product-is_frozen__<?php echo e($product->is_frozen); ?>">
                <?php if($product->is_sold == 0 && $product->status !== 'project' && $product->is_frozen == 0): ?>
                    <?php if($product->phone): ?>
                    <a href="tel:<?php echo e(explode(',', $product->phone)[0]); ?>" class="product-page__button general-button-color product-page__button-phone">
                        <span class="icon-phone"></span>
                        <span><?php echo e(explode(',', $product->phone)[0]); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if($product->site): ?>
                    <a rel="nofollow" href="<?php echo e(strpos($product->site, 'http') !== false? $product->site.'?utm_source=zagorodna&utm_medium=referral&utm_campaign' : '//' . $product->site .'?utm_source=zagorodna&utm_medium=referral&utm_campaign'); ?>" target="_blank" class="product-page__button general-button-color product-page__button-question js-button" data-target="help"><?php echo e(__('main.Перейти на сайт')); ?></a>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- button class="product-page__button product-page__button-help filter-button js-button" data-target="questions"><?php echo e(__('main.Задать вопрос')); ?></button>
                <button class="product-page__button catalog__filter-button product-page__button-meeting js-button" data-target="meeting"><?php echo e(__('main.Назначить визит')); ?></button -->
            </div>
            <div class="wrapper">
                <div class="product-page__info-about-house">
                    <?php if($project->area): ?>
                    <div class="area about-house-wrapper" itemprop="floorSize" itemscope itemtype="http://schema.org/QuantitativeValue">
                        <meta itemprop="value" content="<?php echo e($project->area); ?>">
                        <meta itemprop="unitCode" content="MTK">
                        <div class="area-img img" style="background-image:url(<?php echo e(url('img/area-icon.png')); ?>)"></div>
                        <p><?php echo e($project->area); ?> <?php echo e($project->area_unit); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($project->floors): ?>
                    <div class="floor about-house-wrapper">
                        <div class="floor-img img" style="background-image:url(<?php echo e(url('img/floor-icon.png')); ?>)"></div>
                        <p><?php echo e($project->floors); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($project->bedrooms): ?>
                    <div class="rooms about-house-wrapper">
                        <div class="rooms-img img" style="background-image:url(<?php echo e(url('img/rooms-icon.png')); ?>)"></div>
                        <p itemprop="numberOfBedrooms"><?php echo e($project->bedrooms); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="product-page__price">
                    <p><?php echo e($project->price); ?></p>
                    <span>грн/<?php echo e($project->area_unit); ?></span>
                </div>
            </div>
        </div>
        <?php if($product->notBaseModifications->count() > 1): ?>
        <div class="product-page__info-footer">
            <div class="product-page__same product-page__same-tabs">
                <h4 class="product-page__caption"><?php echo e(__('main.Другие типовые проекты')); ?></h4>
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $projects): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="product-page__container">
                    <h5 class="product-page__same__caption"><?php echo e(__('plural.nominative.' . $type)); ?></h5>
                    <ul class="product-page__same__list">
                      <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="product-page__same__item">
                            <div class="img" style="background-image:url(<?php echo e($item->images? url($item->images[0]) : url($product->image)); ?>); <?php if($product->category_id === 2 || $product->category_id === 7): ?> background-size: contain; <?php endif; ?>" ></div>
                            <p class="name">
                                <a href="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
                            </p>
                            <?php if($item->price * $item->area): ?>
                            <p class="price">от <strong><?php echo e($item->price * $item->area); ?></strong> грн</p>
                            <?php endif; ?>
                            <?php if($item->area): ?>
                            <p class="area"><?php echo e($item->area); ?> <?php echo e($item->area_unit); ?></p>
                            <?php endif; ?>
                        </li>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if($project->layouts && count($project->layouts)): ?>
        <div class="product-page__plan product-page__plan-tablet">
            <h4 class="product-page__caption"><?php echo e(__('main.Планировки')); ?></h4>
            <ul class="product-page__plan-list">
                <?php $__currentLoopData = $project->layouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $layout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="product-page__plan-item js-button js-button-plan" data-target="full-screen-plan" data-index="<?php echo e($key + 1); ?>">
                    <p class="name"><?php echo e($layout['name']); ?></p>
                    <div class="product-page__plan-img">
                        <img src="<?php echo e(url($layout['image'])); ?>" alt="Фото: <?php echo e($layout['name']); ?>" title="Картинка: <?php echo e($layout['name']); ?>">
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('product_after_content'); ?>
<section class="popup popup-full-screen slider-infinity" data-target="full-screen">
      <button class="close-popup js-close">
        <span class="decor"></span>
    </button>
    <div class="popup__wrapper popup-full-screen__wrapper">
        <ul class="popup-full-screen__list js-infinity-slider-list">
          <?php $__currentLoopData = $project->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="popup-full-screen__item js-slider-item-infinity <?php if($key == 0): ?> show <?php endif; ?>" style="background-image:url(<?php echo e(url($image)); ?>)"></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php if(!count($project->images)): ?>
            <li class="popup-full-screen__item js-slider-item-infinity show" style="background-image:url(<?php echo e(url($project->product->image)); ?>)"></li>
          <?php endif; ?>
        </ul>
    </div>
    <?php if(count($project->images) > 1): ?>
    <div class="popup-buttons js-arrow-infinity">
        <button class="popup-button prev">
            <span class="icon-arrow-left"></span>
        </button>
        <button class="popup-button next">
            <span class="icon-arrow-right"></span>
        </button>
    </div>
    <?php endif; ?>
</section>
<?php if($project->layouts && count($project->layouts)): ?>
<section class="popup popup-full-screen slider" data-target="full-screen-plan">
    <button class="close-popup js-close">
        <span class="decor"></span>
    </button>
    <div class="popup__wrapper popup-full-screen-plan__wrapper">
        <ul class="product-page__plan-list">
            <?php $__currentLoopData = $project->layouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $layout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="product-page__plan-item js-slider-item show" data-index="<?php echo e($key + 1); ?>">
                <p class="name"><?php echo e($layout['name']); ?></p>
                <div class="product-page__plan-img">
                    <img src="<?php echo e(url($layout['image'])); ?>" alt="Фото: <?php echo e($layout['name']); ?>" title="Картинка: <?php echo e($layout['name']); ?>">
                </div>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <div class="popup-buttons js-arrows">
        <button class="popup-button prev">
            <span class="icon-arrow-left"></span>
        </button>
        <button class="popup-button next">
            <span class="icon-arrow-right"></span>
        </button>
    </div>
</section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>

</script>
<script src="<?php echo e(url('js/product/default.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('product.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/product/project.blade.php ENDPATH**/ ?>