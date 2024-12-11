<div class="social-banners__wrapper">
	<h5 class="social-banners__text">
    	<?php echo __('main.Хотите знать', ['del' => '<div class="mobile-hide"></div>']); ?>

	</h5>
	<div class="social-banners__block">
    	<a href="https://t.me/zagorodnacom" target="_blank" class="social-banners__item social-banners__item-telegram">
        	<div  class="social-banners__item_img">
            	<img src="<?php echo e(url('/img/telegram_icon.png')); ?>" alt="">
        	</div>
        	<p><?php echo e(__('main.Присоединяйтесь к нам в')); ?> <b>Telegram</b>!</p>
    	</a>
    	<a href="<?php echo e($google_news_link); ?>" target="_blank" class="social-banners__item social-banners__item-google">
        	<div class="social-banners__item_img">
            	<img src="<?php echo e(url('/img/google_news_icon.png')); ?>" alt="">
        	</div>
        	<p><?php echo e(__('main.Присоединяйтесь к нам в')); ?> <b>Google News</b>!</p>
    	</a>
	</div>
</div><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/includes/socialBanner.blade.php ENDPATH**/ ?>