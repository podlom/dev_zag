

<?php $__env->startPush('header_scripts'); ?>
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php echo e($company->name); ?>",
        "image": [
            "<?php echo e($company->image? url($company->image) : ''); ?>"
        ],
        "logo": "<?php echo e($company->logo? url($company->logo) : ''); ?>",
        "address": "<?php echo e($company->address_string); ?>",
        "telephone": "<?php echo e(explode(', ', $company->phone)[0]); ?>",
        "url": "<?php echo e($company->link); ?>"
    }
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs breadcrumbs-company">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render('company', $company->category, $h1)); ?>

        </div>
    </section>
    <section class="company-page">
        <div class="company-page__bg" >
            <img src="" v-lazy="'<?php echo e($company->image? url($company->image) : url('/image/company-cover.jpg?q=70&f=pjpg')); ?>'" alt="<?php echo e($company->name); ?> фото" title="<?php echo e($company->name); ?> картинка">
        <?php if($company->image && isset($company->color) && $company->color): ?>
            <div class="layer" style="background-color:<?php echo e($company->color); ?>"></div>
        <?php endif; ?>
        </div>
        <div class="company-page__wrapper container">
            <div class="company-page__main">
                <h1 class="company-page__caption"><?php echo e($h1); ?></h1>
                <div class="general-noty__buttons-container">
                    <button class="general-noty__button general-noty__button-favorite" @click="addToFavorites(<?php echo e($company); ?>, 'companies')" :class="{active: favorites['companies'].includes(<?php echo e($company->id); ?>) || favorites['companies'].includes(<?php echo e($company->original_id); ?>)}" title="<?php echo e(__('main.Добавить в избранное')); ?>">
                        <span class="icon-heart-outline"></span>
                    </button>
                    <?php if($company->category_id == 1 || $company->category_id == 18): ?>
                    <button class="general-noty__button general-noty__button-sing-up" @click="addToNotifications(<?php echo e($company); ?>, 'companies')" :class="{active: notifications['companies'].includes(<?php echo e($company->id); ?>) || notifications['products'].includes(<?php echo e($company->original_id); ?>)}">
                        <span class="icon-bell-outline"></span>
                    </button>
                    <?php endif; ?>
                </div>
                <div class="company-page__info">
                    <div class="company-page__info__header">
                        <div class="company-page__info__logo">
                            <?php
                                if(!$company->logo)
                                    $logo = url('/img/fireplace-circle.svg');
                                elseif(strpos($company->logo, '.svg') !== false)
                                    $logo = url($company->logo);
                                else
                                    $logo = url('common/' . $company->logo . '?w=100');
                            ?>
                            <img src="<?php echo e($logo); ?>" alt="<?php echo e($company->name); ?> логотип фото" title="<?php echo e($company->name); ?> логотип картинка">
                        </div>
                        <ul class="general__socila-list">
                            <?php if($company->fb): ?>
                            <li class="general__social-item">
                                <a href="<?php echo e($company->fb); ?>" class="general__social-link social-facebook">
                                    <span class="icon-facebook"></span>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if($company->inst): ?>
                            <li class="general__social-item">
                                <a href="<?php echo e($company->inst); ?>" class="general__social-link social-instagram">
                                    <span class="icon-instagram"></span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="company-page__info__body">
                        <ul class="company-page__links-list">
                            <li class="company-page__links-item">
                                <span class="icon-place-big"></span>
                                <?php if($company->city): ?>
                                <a href="<?php echo e($company->link . '/map#content'); ?>" class="company-page__links-link"><?php echo e($company->city); ?></a>
                                <?php else: ?>
                                <div class="company-page__links-link" style="pointer-events:none">н.д.</div>
                                <?php endif; ?>
                            </li>
                            <li class="company-page__links-item">
                                <span class="icon-phone-outline"></span>
                                <a href="tel:<?php echo e(explode(', ', $company->phone)[0]); ?>" class="company-page__links-link" <?php if(!$company->phone): ?> style="pointer-events:none" <?php endif; ?>><?php echo e($company->phone? explode(', ', $company->phone)[0] : 'н.д.'); ?></a>
                            </li>
                            <li class="company-page__links-item">
                                <span class="icon-globe"></span>
                                <a rel="nofollow" href="<?php echo e(Str::startsWith($company->site, ['http://', 'https://']) ? $company->site : 'https://' . $company->site); ?>" class="company-page__links-link ts--ln-98" <?php if(!$company->site): ?> style="pointer-events:none" <?php endif; ?>>
                                    <?php echo e($company->site ? $company->site : 'н.д.'); ?>

                                </a>

                            </li>
                        </ul>
                    </div>
                    <?php if($company->statistics): ?>
                    <div class="company-page__info__footer">
                        <ul class="company-page__info__statistic-list">
                            <?php $__currentLoopData = json_decode($company->statistics); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(count((array) $item)): ?>
                            <li class="company-page__info__statistic-item">
                                <p class="number"><?php echo e($item->number); ?></p>
                                <p class="text"><?php echo e($item->text); ?></p>
                            </li>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="company-page__content" id="content">
                <div class="general-tabs">
                    <ul class="general-tabs__list">
                        <li class="general-tabs__item <?php if(!$tab): ?> active <?php endif; ?>"><a href="<?php echo e($company->link . '#content'); ?>"><?php echo e(__('main.О компании')); ?></a></li>
                        <li class="general-tabs__item <?php if($tab == 'map'): ?> active <?php endif; ?> <?php if(!isset($company->address['latlng'])): ?> disabled <?php endif; ?>"><a href="<?php echo e($company->link . '/map#content'); ?>"><?php echo e(__('main.Карта')); ?></a></li>
                        <li class="general-tabs__item <?php if($tab == 'video'): ?> active <?php endif; ?> <?php if(!$company->videos): ?> disabled <?php endif; ?>"><a href="<?php echo e($company->link . '/video#content'); ?>"><?php echo e(__('main.Видео')); ?></a></li>
                        <li class="general-tabs__item <?php if($tab == 'reviews'): ?> active <?php endif; ?>"><a href="<?php echo e($company->link . '/reviews#content'); ?>"><?php echo e(__('main.Отзывы')); ?></a></li>
                        <li class="general-tabs__item <?php if($tab == 'promotions'): ?> active <?php endif; ?> <?php if(!count($company->promotions)): ?> disabled <?php endif; ?>"><a href="<?php echo e($company->link . '/promotions#content'); ?>"><?php echo e(__('main.Акции')); ?></a></li>
                        <li class="general-tabs__item general-tabs__item-empty"></li>
                    </ul>
                </div>
<?php echo $__env->yieldContent('company_content'); ?>
            </div>
        </div>
    </section>
    <?php if($products_count): ?>
    <section class="product">
        <div class="product__wrapper slider-infinity">
            <div class="general-heading container">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Новостройки застройщика')); ?> "<?php echo e($company->name); ?>"</h2>
                <p class="calc-product"><?php echo e($products_count); ?> <span><?php echo e(__('main.Всего')); ?></span></p>
            </div>
            <ul class="product__list product-slider__list js-infinity-slider-list">
                <productcard v-for="(product, key) in products.data" :key="key" :data-product="product" :data-classes="key == 0? 'js-slider-item-infinity product-slider__item show' : 'js-slider-item-infinity product-slider__item'" @add-to-favorites="addToFavorites"></productcard>
            </ul>
            <div class="general-button__wrapper js-arrow-infinity container">
                <div class="wrapper <?php if($products->count() < 5): ?> hide <?php endif; ?>">
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
    <?php endif; ?>
    <?php if($companies->count()): ?>
    <section class="best-company-info best-company-info-company">
        <div class="best-company-info__wrapper container">
            <div class="general-heading">
                <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Другие компании')); ?></h2>
                <p class="calc-product"><?php echo e($companies_count); ?> <span><?php echo e(__('main.Всего')); ?></span></p>
            </div>
            <ul class="best-company-info__list">
                <companycard v-for="(company, key) in companies.data" :key="key" :data-company="company" @add-to-favorites="addToFavorites" @add-to-notifications="addToNotifications"></companycard>
            </ul>
            <a href="<?php echo e(route($lang . '_companies')); ?>" class="main-button-more">
                <span class="text"><?php echo e(__('main.Смотреть все компании')); ?></span>
                <span class="icon-arrow-more"></span>
            </a>
        </div>
    </section>
    <?php endif; ?>
    <?php if($company->seo_desc): ?>
    <section class="seo-block">
        <div class="seo-block__wrapper container">
            <h2 class="main-caption-l"><?php echo e($company->seo_title); ?></h2>
            <div class="seo-block__content">
                <div class="wrapper">
                    <?php echo $company->seo_desc; ?>

                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  var company = <?php echo json_encode($company, 15, 512) ?>;
  var reviews = <?php echo json_encode($reviews, 15, 512) ?>;
  var products = <?php echo json_encode($products, 15, 512) ?>;
  var companies = <?php echo json_encode($companies, 15, 512) ?>;
  var promotions = <?php echo json_encode($promotions, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/companies/company.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>

<?php if(isset($company->address['latlng']) && $tab === 'map'): ?>
<?php $__env->startPush('styles'); ?>
<!-- link href='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.css' rel='stylesheet' / -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- script src='https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js'></script -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>

<script>
    mapboxgl.accessToken = '<?php echo e(config('services.mapbox.token')); ?>';

document.map = new mapboxgl.Map({
  language: '<?php echo e($lang); ?>',
  container: 'general__map',
  style: 'mapbox://styles/mapbox/streets-v11',
  center: [<?php echo e($company->address['latlng']['lng']); ?>, <?php echo e($company->address['latlng']['lat']); ?>],
  zoom: 11,
  minZoom: 3.7
});

var marker = new mapboxgl.Marker()
.setLngLat([<?php echo e($company->address['latlng']['lng']); ?>, <?php echo e($company->address['latlng']['lat']); ?>])
.setPopup(new mapboxgl.Popup().setText('<?php echo e($company->name); ?>'))
.addTo(document.map);

currentMarkers.push(marker);

document.map.on('load', function() {
    document.map.getStyle().layers.forEach(function(thisLayer){
        if(thisLayer.type == 'symbol'){
            document.map.setLayoutProperty(thisLayer.id, 'text-field', ['get','name_ru'])
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.app', [
  'meta_title' => $meta_title,
  'meta_desc' => $meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/companies/layouts/app.blade.php ENDPATH**/ ?>