<form action="<?php echo e(route('subscribe')); ?>" method="post" class="subscribe-block__form" id="subscription">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="subscription_news" value="1">
    <label class="input__wrapper <?php $__errorArgs = ['subscription_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        <span class="subscribe-block__decor"></span>
        <input type="email" name="subscription_email" class='main-input' placeholder="<?php echo e(__('forms.placeholders.Ваш электронный адрес')); ?>">
        <?php $__errorArgs = ['subscription_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="error-text" role="alert">
                <?php echo e($message); ?>

            </span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </label>
    <button class="subscribe-block__button"><?php echo e(__('main.Подписаться')); ?></button>
</form><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/modules/subscription.blade.php ENDPATH**/ ?>