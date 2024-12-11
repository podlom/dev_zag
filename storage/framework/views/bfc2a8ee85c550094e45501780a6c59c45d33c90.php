
<?php
    $type = ($product->category_id === 1 || $product->category->original_id === 1)? 'cottage' : 'newbuild';
?>
<?php $__env->startSection('product_content'); ?>
<div class="product-page__main product-page__main-tabs">
    <div class="product-page__reviews">
        <div class="product-page__reviews-header">
            <h4 class="product-page__caption-l"><?php echo e(__('main.Отзывы о')); ?> <?php echo e($product->name); ?></h4>
            <button class="main-button js-review-button"></button>
        </div>
        <div class="add-reviews js-review-drop">
            <h4 class="product-page__caption-l"><?php echo e(__('main.Оцените объект')); ?></h4>
            <div class="star__wrapper star__wrapper-set">
                <ul class="star__list">
                    <li class="start__item js-stars-item" data-index="2">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item js-stars-item" data-index="4">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item js-stars-item" data-index="6">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item js-stars-item" data-index="8">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item js-stars-item" data-index="10">
                        <span class="icon-star"></span>
                    </li>
                </ul>
            </div>
            <form action="<?php echo e(url('reviews/create/' . $type)); ?>" method="post" class="reviews__form">
            <?php echo csrf_field(); ?>
                <input type="hidden" name="reviewable_type" value="Aimix\Shop\app\Models\Product">
                <input type="hidden" name="reviewable_id" value="<?php echo e($product->id); ?>" class="ts-removed-original_id-here-ln-36">
                <input type="hidden" name="language_abbr" value="<?php echo e($product->language_abbr); ?>" class="ts-lang-review-create-ln-37">
                <input type="hidden" class="js-input-stars" name="<?php echo e($type); ?>_review_rating" value="">
                <label class="textarea__wrapper">
                    <span class="input__caption"><?php echo e(__('main.Текст отзыва')); ?></span>
                    <textarea class="main-textarea" name="<?php echo e($type); ?>_review_text" placeholder="<?php echo e(__('forms.placeholders.Напишите отзыв')); ?>"></textarea>
                </label>
                <label class="input__wrapper">
                    <span class="input__caption"><?php echo e(__('main.Имя')); ?></span>
                    <input type="text" class="main-input" name="<?php echo e($type); ?>_review_name" placeholder="<?php echo e(__('forms.placeholders.Как к вам обращаться?')); ?>">
                </label>
                <label class="input__wrapper">
                    <span class="input__caption">Email*</span>
                    <input type="email" class="main-input" name="<?php echo e($type); ?>_review_email" placeholder="<?php echo e(__('forms.placeholders.Ваш электронный адрес')); ?>">
                </label>
                <button class="main-button"><?php echo e(__('main.Опубликовать')); ?></button>
            </form>
        </div>
        <ul class="product-page__reviews-list" v-if="reviews.total">
            <reviewCard v-for="(review, key) in reviews.data" :data-review="review" data-type="product" :key="key"></reviewCard>
            <div class="pagination__wrapper">
                <a href="#" class="main-button-more" @click.prevent="loadmore()" v-if="reviews.to != reviews.total">
                    <span class="text"><?php echo e(__('main.Показать больше')); ?></span>
                </a>
            </div>
        </ul>
        <div class="product-page__reviews-list" v-else><?php echo e(__('main.К этому объекту нет отзывов')); ?></div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        // Stars
        document.addEventListener("click", function(e){
            let item = e.target;

            if(item.closest('.js-stars-item')) {
                let value = item.closest('.js-stars-item').getAttribute('data-index');
                let allStars = document.querySelectorAll(".js-stars-item");

                allStars.forEach(function(item){
                    item.classList.remove('active');
                });
                item.closest('.js-stars-item').classList.add('active');
                item.closest('.star__list').classList.add('active');
                document.querySelector(".js-input-stars").value = value;
            }
        });

        // //Stars

        // Review

        document.addEventListener('click', function(e){
            let item = e.target;

            if(item.closest('.js-review-button')) {
                let reviewContainer = document.querySelector('.js-review-drop');
                item.closest('.js-review-button').classList.toggle('active');
                reviewContainer.classList.toggle('active');
            }
        });
    });
</script>
<script>
    var product = <?php echo json_encode($product, 15, 512) ?>;
    var reviews = <?php echo json_encode($reviews, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/product/reviews.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('product.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/product/reviews.blade.php ENDPATH**/ ?>