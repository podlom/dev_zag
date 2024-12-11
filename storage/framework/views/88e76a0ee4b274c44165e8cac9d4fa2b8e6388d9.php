<section class="free-search container">
    <div class="free-search__wrapper" v-lazy:background-image="'<?php echo e(url('img/free-search-bg.png')); ?>'">
        <h3 class="free-search__caption"><?php echo e(__('main.Бесплатный подбор недвижимости')); ?></h3>
        <div class="free-search__body">
            <p><span><?php echo e(__('main.подберем')); ?></span>за</p>
            <h4 class="free-search__number">5</h4>
            <p><span><?php echo e(__('main.вариантов')); ?></span><?php echo e(__('main.шагов')); ?></p>
        </div>
        <button class="call-back__button js-button" data-target="free_selection"><?php echo e(__('main.Подобрать бесплатно')); ?></button>
    </div>
</section><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/includes/freeSearch.blade.php ENDPATH**/ ?>