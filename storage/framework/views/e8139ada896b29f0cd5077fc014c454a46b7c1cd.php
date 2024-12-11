


<?php $__env->startSection('company_content'); ?>
<div class="company-page__content-tabs">
    <div class="product-page__reviews-header">
        <h4 class="product-page__caption-l"><?php echo e(__('main.Отзывы')); ?></h4>
        <button class="main-button js-review-button"></button>
    </div>
    <div class="add-reviews js-review-drop">
        <h4 class="product-page__caption-l"><?php echo e(__('main.Оцените компанию')); ?></h4>
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
        <form action="<?php echo e(url('reviews/create/brand')); ?>" method="post" class="reviews__form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="brand_review_rating" class="js-input-stars" value="">
            <input type="hidden" name="reviewable_type" value="Aimix\Shop\app\Models\Brand">
            <input type="hidden" name="reviewable_id" value="<?php echo e($company->original_id? $company->original_id : $company->id); ?>">
            <label class="textarea__wrapper">
                <span class="input__caption"><?php echo e(__('main.Отзыв')); ?></span>
                <textarea class="main-textarea" name="brand_review_text" placeholder="<?php echo e(__('forms.placeholders.Напишите отзыв')); ?>"></textarea>
            </label>
            <label class="input__wrapper">
                <span class="input__caption"><?php echo e(__('main.Имя')); ?></span>
                <input type="text" class="main-input" name="brand_review_name" placeholder="<?php echo e(__('forms.placeholders.Как к вам обращаться?')); ?>">
            </label>
            <label class="input__wrapper">
                <span class="input__caption">Email</span>
                <input type="email" class="main-input" name="brand_review_email" placeholder="<?php echo e(__('forms.placeholders.Ваш электронный адрес')); ?>">
            </label>
            <button class="main-button"><?php echo e(__('main.Опубликовать')); ?></button>
        </form>
    </div>
    <ul class="product-page__reviews-list" v-if="reviews.total">
        <reviewCard v-for="(review, key) in reviews.data" :data-review="review" data-type="product" :key="key"></reviewCard>
        <div class="pagination__wrapper">
            <a href="#" class="main-button-more" @click.prevent="loadmore()" v-if="reviews.total && reviews.to != reviews.total">
                <span class="text"><?php echo e(__('main.Показать больше')); ?></span>
            </a>
        </div>
    </ul>
    <div class="product-page__reviews-list" v-else><?php echo e(__('main.Отзывы отсутствуют')); ?></div>
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('companies.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/companies/reviews.blade.php ENDPATH**/ ?>