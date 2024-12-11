<!-- This file is used to store topbar (left) items -->

<li class="nav-item px-3"><a class="nav-link" href="<?php echo e(backpack_url('feedback')); ?>"><i class='nav-icon las la-phone-volume'></i> Обратная связь 
		<span class="badge badge-<?php echo e($new_feedbacks? 'warning': 'light'); ?>" style="position:initial"><?php echo e($new_feedbacks); ?></span></a>
</li>
<li class="nav-item px-3">
	<a class="nav-link" href="<?php echo e(backpack_url('review')); ?>">
		<i class='nav-icon las la-comment-dots'></i> Отзывы 
		<span class="badge badge-<?php echo e($new_reviews? 'warning': 'light'); ?>" style="position:initial"><?php echo e($new_reviews); ?></span>
	</a>
</li>
<li class="nav-item px-3"><a class="nav-link" href="<?php echo e(backpack_url('subscription')); ?>"><i class='nav-icon la la-mail-bulk'></i> Подписки</a></li>

<li class='nav-item px-3'><a class='nav-link' href='<?php echo e(backpack_url('application')); ?>'><i class='nav-icon la la-file-alt'></i> Заявки</a></li><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/vendor/backpack/base/inc/topbar_left_content.blade.php ENDPATH**/ ?>