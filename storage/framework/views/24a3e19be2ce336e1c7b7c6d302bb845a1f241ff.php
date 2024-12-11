<?php $__env->startSection('product_content'); ?>

<?php if(count($types)): ?>
<div class="product-page__same">
    <h4 class="ts-typical-project-price-ln-7 product-page__caption"><?php echo e(__('main.Цены на типовые проекты')); ?></h4>
    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $projects): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="product-page__container">
        <?php if($type): ?>
        <h5 class="product-page__same__caption"><?php echo e(__('plural.nominative.' . $type)); ?></h5>
        <?php endif; ?>
        <ul class="product-page__same__list">
          <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="product-page__same__item">
                <div class="img" v-lazy:background-image="'<?php echo e($item->images? url('common/' . $item->images[0]) : url('common/' . $product->image)); ?>'" <?php if($product->category_id === 2 || $product->category_id === 7): ?> style="background-size: contain;" <?php endif; ?>></div>
                <p class="name">
                    <a href="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
                </p>
                <?php if($item->price && $item->area != 0): ?>
                <p class="price"><?php echo e(__('main.от')); ?> <strong><?php echo e($item->price * $item->area); ?></strong> грн</p>
                <?php else: ?>
                    <?php if($item->status): ?>
                        <p class="ts-item-status item__status"> <?php echo e(__('main.' . $item->status)); ?></p>
                    <?php endif; ?>

                    <?php if($item->building): ?>
                        <p class="ts-item-status-building item__status-building"> <?php echo e($item->building); ?></p>
                    <?php endif; ?>

                    <?php if($item->status_done): ?>
                        <p class="ts-item-status-done item__status-done"> <?php echo e($item->status_done); ?></p>
                    <?php endif; ?>

                    <?php if($item->status_project): ?>
                        <p class="ts-item-status-project item__status-project"> <?php echo e($item->status_project); ?></p>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if($item->area != 0): ?>
                    <p class="area"><?php echo e($item->area); ?> <?php echo e($item->area_unit); ?></p>
                <?php endif; ?>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<?php if(!empty($product->updated_at->format('d.m.Y'))): ?>
<div class="product-page__status-wrapper">
    <h4 class="ts-home-build-status-ln-36 product-page__caption"><?php echo e(__('main.Статус строительства домов')); ?></h4>

    <div class="product-status__info ts-2024-08-05-ln-41">
        <p class="product-page__status-info"><?php echo e(__('main.Информация предоставлена отделом продаж состоянием на')); ?> <?php echo e($product->updated_at->format('d.m.Y')); ?></p>
    </div>


    <?php if(isset($statuses_array['Земельный участок']) && !empty($statuses_array)): ?>
        <!-- @ts  $statuses_array: <?php echo e(var_export($statuses_array, true)); ?> -->
    <?php else: ?>
        <!-- @ts  $statuses_array is not set or empty -->
    <?php endif; ?>

    
    <?php if(isset($type) && ($type != 2)): ?>
    <?php if(count($statuses_array) > 1 || (count($statuses_array) === 1 && !isset($statuses_array['Земельный участок']))): ?>
    <div class="product-page__status-table">
        <div class="product-page__status-table__header">
            <p class="type"><?php echo e(__('main.Тип')); ?></p>
            <p class="in-project"><?php echo e(__('main.В проекте')); ?></p>
            <p class="build"><?php echo e(__('main.Строятся')); ?></p>
            <p class="status"><?php echo e(__('main.Завершенные')); ?></p>
        </div>
        <ul class="product-page__status-table__list ts-status-type--<?php echo e($type); ?>}">
          <?php $__currentLoopData = $statuses_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $statuses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if($type !== 'Земельный участок'): ?>
            <li class="product-page__status-table__item">
                <p class="type"><?php echo e(__('plural.nominative.' . $type)); ?></p>
                <p class="in-project"><?php echo e($statuses['project'] === null? '0' : $statuses['project']); ?></p>
                <p class="build"><?php echo e($statuses['building'] === null? '0' : $statuses['building']); ?></p>
                <p class="status"><?php echo e($statuses['done'] === null? '0' : $statuses['done']); ?></p>
            </li>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    <?php if(isset($statuses_array['Земельный участок'])): ?>
    <div class="product-page__status-table">
        <div class="product-page__status-table__header">
            <p class="type"><?php echo e(__('main.Тип')); ?></p>
            <p class="in-project"><?php echo e(__('main.Свободные')); ?></p>
            <p class="build"><?php echo e(__('main.Застраиваются')); ?></p>
            <p class="status"><?php echo e(__('main.Застроенные', ['type' => ''])); ?></p>
        </div>
        <ul class="product-page__status-table__list">
            <?php $__currentLoopData = $statuses_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $statuses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($type === 'Земельный участок'): ?>
            <li class="product-page__status-table__item">
                <p class="type"><?php echo e(__('plural.nominative.' . $type)); ?></p>
                <p class="in-project"><?php echo e($statuses['project'] === null? 'н.д' : $statuses['project']); ?></p>
                <p class="build"><?php echo e($statuses['building'] === null? 'н.д' : $statuses['building']); ?></p>
                <p class="status"><?php echo e($statuses['done'] === null? 'н.д' : $statuses['done']); ?></p>
            </li>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="product-page__characteristic-wrapper js-drop-item">
    <h4 class="product-page__caption"><?php echo e(__('main.Характеристики')); ?></h4>
    <ul class="product-page__characteristic-list">
        <?php if($product->category_id === 1 || $product->category->original_id === 1): ?>
            <?php if($houses_area['min'] || $houses_area['max']): ?>
            <li class="product-page__characteristic-item">
                <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-1.png')); ?>)"></div>
                <div class="info">
                    <h5 class="caption"><?php echo e(__('main.Площадь домовладения')); ?></h5>
                    <p><?php echo e($houses_area['min']? $houses_area['min'] : ''); ?> <?php echo e($houses_area['min'] && $houses_area['max'] && $houses_area['min'] != $houses_area['max']? ' - ' : ''); ?> <?php echo e($houses_area['min'] != $houses_area['max'] ? $houses_area['max'] : ''); ?> <?php echo e(!$houses_area['min'] && !$houses_area['max'] ? 'н.д.' : ''); ?> (<?php echo e($product->area_unit); ?>)</p>
                </div>
            </li>
            <?php endif; ?>
        <?php elseif($product->category_id === 2 || $product->category->original_id === 2): ?>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-1.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Площадь квартиры')); ?></h5>
                <p><?php echo e($houses_area['min']); ?> <?php echo e($houses_area['min'] != $houses_area['max'] ? '- ' . $houses_area['max'] : ''); ?> (м<sup>2</sup>)</p>
            </div>
        </li>
        <?php endif; ?>
        <?php if($plot_area): ?>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-2.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Площадь участка')); ?></h5>
                <p><?php echo e($plot_area['min']); ?> <?php echo e($plot_area['min'] != $plot_area['max'] ? '- ' . $plot_area['max'] : ''); ?> (сот.)</p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->extras['area'])): ?>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-3.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Площадь застройки')); ?></h5>
                <p><?php echo e($product->area_m2); ?> (га)</p>
            </div>
        </li>
        <?php endif; ?>
        <!-- <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(./img/characteristic-img-4.png)"></div>
            <div class="info">
                <h5 class="caption">Количество участков</h5>
                <p>5</p>
            </div>
        </li>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(./img/characteristic-img-5.png)"></div>
            <div class="info">
                <h5 class="caption">Количество таунхаусов</h5>
                <p>5</p>
            </div>
        </li>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(./img/characteristic-img-6.png)"></div>
            <div class="info">
                <h5 class="caption">Количество дуплексов</h5>
                <p>4</p>
            </div>
        </li> -->
        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $projects): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($projects->sum('total')): ?>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-4.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Количество')); ?> <?php echo e(__('plural.genitive.' . $type)); ?></h5>
                <p><?php echo e($projects->sum('total')); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($product->category_id === 1 || $product->category->original_id === 1): ?>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-7.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Количество домовладений')); ?></h5>
                <p><?php echo e($houses_amount? $houses_amount : 0); ?></p>
            </div>
        </li>
        <?php elseif($product->category_id === 2 || $product->category->original_id === 2): ?>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-7.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Количество квартир')); ?></h5>
                <?php if($product->flats_count): ?>
                <p><?php echo e($product->flats_count); ?></p>
                <?php else: ?>
                <p><?php echo e($houses_amount? $houses_amount : 0); ?></p>
                <?php endif; ?>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->wall_material)): ?>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-8.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Материал стен')); ?></h5>
                <p><?php echo e($product->wall_material? __('attributes.wall_materials.' . $product->wall_material) : 'н.д.'); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->roof_material)): ?>
        <li class="product-page__characteristic-item">
            <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Материал крыши')); ?></h5>
                <p><?php echo e($product->roof_material? __('attributes.roof_materials.' . $product->roof_material) : 'н.д.'); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(($product->category_id === 2 || $product->category_id === 7) && isset($product->newbuild_type)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption ts-2024-07-29-ln-200"><?php echo e(__('main.Тип')); ?></h5>
                <p><?php echo e(__('main.' . $product->newbuild_type)); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->floors)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Этажность')); ?></h5>
                <p><?php echo e($product->floors); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->technology)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Технология строительства')); ?></h5>
                <p><?php echo e($product->technology); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->class)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Класс')); ?></h5>
                <p><?php echo e($product->class); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->insulation)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Утепление')); ?></h5>
                <p><?php echo e($product->insulation); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->ceilings)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Высота потолков')); ?></h5>
                <p><?php echo e($product->ceilings); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->condition)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Состояние квартиры')); ?></h5>
                <p><?php echo e($product->condition); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->closed_area)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Закрытая территория')); ?></h5>
                <p><?php echo e($product->closed_area); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if(isset($product->parking)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Паркинг')); ?></h5>
                <p><?php echo e($product->parking); ?></p>
            </div>
        </li>
        <?php endif; ?>

        <?php if(isset($product->area_cottage)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Размер участка под коттедж')); ?></h5>
                <p><?php echo e($product->area_cottage); ?></p>
            </div>
        </li>
        <?php endif; ?>

        <?php if(isset($product->area_townhouse)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Придомовой участок таунхауса')); ?></h5>
                <p><?php echo e($product->area_townhouse); ?></p>
            </div>
        </li>
        <?php endif; ?>

        <?php if(isset($product->area_duplex)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Придомовой участок дуплекса')); ?></h5>
                <p><?php echo e($product->area_duplex); ?></p>
            </div>
        </li>
        <?php endif; ?>

        <?php if(isset($product->area_quadrex)): ?>
        <li class="product-page__characteristic-item">
            <!-- <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-9.png')); ?>)"></div> -->
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Придомовой участок квадрекса')); ?></h5>
                <p><?php echo e($product->area_quadrex); ?></p>
            </div>
        </li>
        <?php endif; ?>

        <?php if($product->infrastructure): ?>
        <li class="product-page__characteristic-item product-page__characteristic-item-big">
            <div class="img" style="background-image:url(<?php echo e(url('img/characteristic-img-10.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Инфраструктура')); ?></h5>
                <p><?php echo e($product->infrastructure); ?></p>
            </div>
        </li>
        <?php endif; ?>
        <?php if($product->communications_string): ?>
        <li class="product-page__characteristic-item product-page__characteristic-item-big">
            <div class="img communication" style="background-image:url(<?php echo e(url('img/characteristic-img-11.png')); ?>)"></div>
            <div class="info">
                <h5 class="caption"><?php echo e(__('main.Коммуникации')); ?></h5>
                <p><?php echo e($product->communications_string); ?></p>
            </div>
        </li>
        <?php endif; ?>
    </ul>
    <?php if($product->description): ?>
    <div class="product-page__characteristic-description">
        <h5><?php echo e(__('main.Описание')); ?></h5>
        <?php echo $product->description; ?>

    </div>
    <?php endif; ?>
    <button class="main-button-more js-drop-button"></button>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('product_after_content'); ?>
<section class="product" v-if="other_products.total">
    <div class="product__wrapper slider-infinity">
        <div class="general-heading container">
            <h2 class="main-caption-l main-caption-l--transform"><?php echo e($product->category_id == 1 || $product->category_id == 6? __('main.Рядом с городком') : __('main.Рядом с комплексом')); ?></h2>
        </div>
        <ul class="product__list product-slider__list js-infinity-slider-list">
            <productcard v-for="(product, key) in other_products.data" :key="key" :data-product="product" @add-to-favorites="addToFavorites" @add-to-comparison="addToComparison" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'"></productcard>
        </ul>
        <div class="general-button__wrapper js-arrow-infinity general-button--hide container">
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
<?php if($companies_count): ?>
<section class="best-company-info">
    <div class="best-company-info__wrapper container">
        <div class="general-heading">
            <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Лучшие застройщики')); ?></h2>
            <p class="calc-product"><?php echo e($companies_count); ?><span><?php echo e(__('main.Всего')); ?></span></p>
        </div>
        <ul class="best-company-info__list">
            <companycard v-for="(company, key) in companies" :key="key" :data-company="company" @add-to-favorites="addToFavorites" @add-to-notifications="addToNotifications"></companycard>
        </ul>
        <a href="<?php echo e(route($lang . '_companies')); ?>" class="main-button-more">
            <span class="text"><?php echo e(__('main.Смотреть все компании')); ?></span>
            <span class="icon-arrow-more"></span>
        </a>
    </div>
</section>
<?php endif; ?>
<?php if($promotions_count): ?>
<section class="product">
    <div class="product__wrapper slider-infinity">
        <div class="general-heading container">
            <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Акции от застройщиков')); ?></h2>
            <p class="calc-product"><?php echo e($promotions_count); ?> <span><?php echo e(__('main.Всего')); ?></span></p>
        </div>
        <ul class="product__list product__list-sale product-slider__list js-infinity-slider-list">
            <promotioncard v-for="(promotion, key) in promotions" :key="key" :data-promotion="promotion" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites"></promotioncard>
        </ul>
        <div class="general-button__wrapper js-arrow-infinity container">
            <div class="wrapper <?php if($promotions->count() < 5): ?> hide <?php endif; ?>">
                <button class="general-button prev">
                    <span class="icon-arrow-left"></span>
                </button>
                <button class="general-button next">
                    <span class="icon-arrow-right"></span>
                </button>
            </div>
            <a href="<?php echo e(route($lang . '_promotions')); ?>" class="main-button-more">
                <span class="text"><?php echo e(__('main.Смотреть все акции')); ?></span>
                <span class="icon-arrow-more"></span>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>
<section class="popup popup-full-screen slider-infinity" data-target="full-screen">
      <button class="close-popup js-close">
        <span class="decor"></span>
    </button>
    <div class="popup__wrapper popup-full-screen__wrapper">
        <ul class="popup-full-screen__list js-infinity-slider-list">
            <li class="popup-full-screen__item js-slider-item-infinity show" v-lazy:background-image="'<?php echo e($product->image? url('common/' . $product->image . '?w=1000&q=90') : url('files/47/net-fot500x500.jpg')); ?>'"></li>
            <?php if(isset($product->images) && (is_array($product->images) || $product->images instanceof Countable)): ?>
                <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <code class="ts-key">$key: <?php echo e($key); ?></code>
                  <code class="ts-image">$image: <?php echo e($image); ?></code>

                <!-- li class="popup-full-screen__item js-slider-item-infinity" v-lazy:background-image="'<?php echo e(url('common/' . $image . '?w=1000&q=90')); ?>'"></li -->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </ul>
    </div>
    <div class="popup-buttons js-arrow-infinity">
        <button class="popup-button prev">
            <span class="icon-arrow-left"></span>
        </button>
        <button class="popup-button next">
            <span class="icon-arrow-right"></span>
        </button>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .product-page__same__item .img[lazy="loading"],
    .popup-full-screen__item[lazy="loading"] {
        background-size: auto 1px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    var product = <?php echo json_encode($product, 15, 512) ?>;
    var companies = <?php echo json_encode($companies, 15, 512) ?>;
    var promotions = <?php echo json_encode($promotions, 15, 512) ?>;
    var product_id = <?php echo json_encode($product->id, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/product/show.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('product.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/product/show.blade.php ENDPATH**/ ?>