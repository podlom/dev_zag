

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(./img/background-img-2.png)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
                <?php echo e(Breadcrumbs::render('page', $page->main_title)); ?>

        </div>
    </section>
    <section class="dictionary">
        <div class="dictionary__wrapper container">
            <h1 class="main-caption-l main-caption-l--transform"><?php echo e($page->main_title); ?></h1>
            <div class="dictionaty__text"><?php echo $page->main_text; ?></div>
            <div class="dictionary__letter__header js-dictionary-header">
                <div class="dictionary__letter__header__wrapper js-dictionary-wrapper">
                    <ul class="dictionary__letter-list">
                        <?php $__currentLoopData = $letters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter => $terms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="dictionary__letter-item">
                            <a class="dictionary__letter-link" href="#<?php echo e($letter); ?>"><?php echo e($letter); ?></a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
            <ul class="dictionary__letter-content-list js-dictionary-body">
                <?php $__currentLoopData = $letters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter => $terms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="dictionary__letter-content-item" id="<?php echo e($letter); ?>">
                    <div class="caption">
                        <h3><?php echo e($letter); ?></h3>
                    </div>
                    <ul class="dictionary__word-list">
                        <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="dictionary__word-item">
                            <p class="name"><?php echo e($term->name); ?></p>
                            <p class="description"><?php echo e($term->definition); ?></p>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <div class="article-page__more-news">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform"><?php echo e($page->news_title); ?></h2>
                </div>
                <ul class="popular__block__list popular__block__list-more">
                    <articlecard v-for="(article, key) in articles" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
                </ul>
            </div>
            <div class="subscribe-block subscribe-block-alone">
                <h5 class="subscribe-block__text"><?php echo e(__('main.Нашли полезную информацию?')); ?><br><?php echo e(__('main.Подписывайтесь на актуальные публикации')); ?>:</h5>
                <?php echo $__env->make('modules.subscription', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </section>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function(){
        function showDictionaryList() {
            let stikyBlockHeight = document.querySelector('.js-dictionary-wrapper').offsetHeight;
            let stikyBlock = document.querySelector('.js-dictionary-wrapper');
            let hideBlock = document.querySelector('.js-dictionary-header');
            let dictionaryBodyBottom = document.querySelector('.js-dictionary-body').getBoundingClientRect().bottom;
            
            if(dictionaryBodyBottom < stikyBlockHeight) {
                stikyBlock.setAttribute('style', 'position: absolute');
                return;
            }else {
                stikyBlock.setAttribute('style', 'position: fixed');
            }
        
            if(hideBlock.getBoundingClientRect().top < 10) {
                hideBlock.style.height = stikyBlockHeight + "px";
                stikyBlock.setAttribute('style', 'position: fixed');
                stikyBlock.classList.add("fixed");
            }else {
                hideBlock.style.height = stikyBlockHeight + "px";
                stikyBlock.setAttribute('style', 'position: absolute');
                stikyBlock.classList.remove("fixed");
            }
        }
        
        window.addEventListener("resize",function(){
            showDictionaryList();
        });
        
        showDictionaryList()
        
        document.addEventListener("scroll",function(){
            showDictionaryList()
        });
    
});
</script>
<script>
  var articles = <?php echo json_encode($articles, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/dictionary/dictionary.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'meta_title' => $page->meta_title,
  'meta_desc' => $page->meta_desc,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/pages/dictionary.blade.php ENDPATH**/ ?>