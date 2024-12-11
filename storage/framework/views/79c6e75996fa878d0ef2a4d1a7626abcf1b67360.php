<?php $__env->startSection('content'); ?>
  <div class="page-404">
    <div class="container">
      <h1 class="page-404_title"><?php echo e(__('main.Похоже, то что вы искали')); ?> <br> <?php echo e(__('main.где-то потерялось, днем с огнем не сыщешь')); ?></h1>
      <a href="<?php echo e(url('/')); ?>" class="main-button-more page-404_button"><?php echo e(__('main.На главную')); ?></a>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .page-404 {
    height: 50vw;
    width: 100%;
    background: 50% 0% / cover no-repeat;
    background-image: url("<?php echo e(url('img/404.png')); ?>");
    padding-top: 90px;
  }
  .page-404_title {
    font-size: 24px;
    line-height: 1.5;
    font-weight: 800;
    text-transform: uppercase;
    color: #fff;
    text-align: center;
    margin-bottom: 27vw;
  }
  .page-404_button {
    margin: auto;
    width: 255px;
  }
  @media  screen and (max-width: 1480px) {
    .page-404 {
      padding-top: 60px;
    }
    .page-404_title {
      margin-bottom: calc(29vw - 50px);
    }
  }
  @media  screen and (max-width: 1169px) {
    .page-404_title {
      font-size: 20px;
      margin-bottom: calc(29vw - 75px);
    }
  }
  @media  screen and (max-width: 767px) {
    .page-404 {
      padding-top: 30px;
      height: 300px;
      background-size: calc(40vw + 500px);
    }
    .page-404_title {
      font-size: 11px;
      margin-bottom: 150px;
    }
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(url('js/app.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'translation_link' => $lang === 'ru'? url('uk') : url('ru')
  ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/errors/404.blade.php ENDPATH**/ ?>