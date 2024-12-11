

<?php $__env->startSection('product_content'); ?>
<div class="product-page__main product-page__main-tabs">
  <div class="product-page__video">
      <h4 class="ts-product__video-title product-page__caption product-page__caption-l"><?php echo e(__('main.Видео о')); ?> <?php echo e($product->name); ?></h4>
      <ul class="product-page__video-list">
        <?php if($product->youtube_video): ?>
        <li class="product-page__video-item">
            <?php echo $product->youtube_video; ?>

        </li>
        <?php else: ?>
              <p class="text-center"><?php echo e(__('main.У этого объекта пока нет видео')); ?></p>
        <?php endif; ?>
        <?php if($product->videos): ?>
        <?php $__currentLoopData = $product->videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li class="product-page__video-item">
            <video src="<?php echo e(url($video)); ?>" controls="controls"></video>
          </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
      </ul>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(url('js/product/default.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('product.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/product/video.blade.php ENDPATH**/ ?>