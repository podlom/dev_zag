<section class="social-banners">
	<div class="container">
		<?php echo $__env->make('includes.socialBanner', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</div>
</section>
<footer class="footer">
    <div class="footer__header">
        <div class="footer__header__wrapper container">
            <div class="footer__write-us">
                <h5 class="footer__caption"><?php echo e(__('main.пишите нам')); ?>:</h5>
                <ul class="footer__socila-list footer__socila-list-write-us">
                    <li class="footer__social-item">
                        <a href="viber://chat?number=<?php echo e(config('settings.vb')); ?>" class="footer__social-link social-viber" title="Viber" rel="nofollow">
                            <span class="icon-viber"></span>
                        </a>
                    </li>
                    <li class="footer__social-item">
                        <a href="whatsapp://send?phone=<?php echo e(config('settings.wa')); ?>" class="footer__social-link social-whatsapp" title="Whatsapp" rel="nofollow">
                            <span class="icon-whatsapp"></span>
                        </a>
                    </li>
                    <li class="footer__social-item">
                        <a href="tg://resolve?domain=<?php echo e(config('settings.tg')); ?>" class="footer__social-link social-telegram" title="Telegram" rel="nofollow">
                            <span class="icon-telegram"></span>
                        </a>
                    </li>
                </ul>
            </div>
            <ul class="footer__socila-list">
                <li class="footer__social-item">
                    <a href="<?php echo e(config('settings.fb')); ?>" target="_blank" class="footer__social-link social-facebook" title="Facebook" rel="nofollow">
                        <span class="icon-facebook"></span>
                    </a>
                </li>
                <li class="footer__social-item">
                    <a href="<?php echo e(config('settings.tw')); ?>" target="_blank" class="footer__social-link social-twitter" title="Twitter" rel="nofollow">
                        <span class="icon-twitter"></span>
                    </a>
                </li>
                <li class="footer__social-item">
                    <a href="<?php echo e(config('settings.inst')); ?>" target="_blank" class="footer__social-link social-instagram" title="Instagram" rel="nofollow">
                        <span class="icon-instagram"></span>
                    </a>
                </li>
                <li class="footer__social-item">
                    <a href="<?php echo e(config('settings.yt')); ?>" target="_blank" class="footer__social-link social-youtube" title="Youtube" rel="nofollow">
                        <span class="icon-youtube"></span>
                    </a>
                </li>
                <li class="footer__social-item">
                    <button class="footer__social-link js-button social-message" data-target="call-back" title="<?php echo e(__('main.Форма обратной связи')); ?>">
                        <span class="icon-message"></span>
                    </button>
                </li>
                <li class="footer__social-item">
                    <a href="<?php echo e(config('settings.in')); ?>" target="_blank" class="footer__social-link js-button linkedin-button" title="Linkedin" rel="nofollow">
                        <span class="icon-linkedin"></span>
                    </a>
                </li>
            </ul>
            <div class="footer__call-back" id="callback">
                <a href="tel:<?php echo e(explode(',', config('settings.phone'))[0]); ?>" class="footer__call-back__link" rel="nofollow"><?php echo e(explode(',', config('settings.phone'))[0]); ?></a>
                <button class="footer__call-back__button js-button" data-target="footer-callback"><?php echo e(__('main.Обратный звонок')); ?></button>
                <div class="popup popup-footer <?php $__errorArgs = ['callback_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> active <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" data-target="footer-callback">
                    <button class="close-popup js-close">
                        <span class="decor"></span>
                    </button>
                    <form action="<?php echo e(url('feedback/create/callback')); ?>" method="post" class="footer-callback__form">
                        <?php echo csrf_field(); ?>
                        <label class="input__wrapper <?php $__errorArgs = ['callback_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <span class="input__caption"><?php echo e(__('main.Имя')); ?></span>
                            <input type="text" class="main-input" name="callback_name" value="<?php echo e(old('callback_name')); ?>" placeholder="<?php echo e(__('forms.placeholders.Как к вам обращаться?')); ?>">
                            <?php $__errorArgs = ['callback_name'];
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
                        <label class="input__wrapper <?php $__errorArgs = ['callback_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <span class="input__caption"><?php echo e(__('main.Контактный телефон')); ?>*</span>
                            <input type="tel" class="main-input" name="callback_phone" value="<?php echo e(old('callback_phone')); ?>" placeholder="<?php echo e(__('forms.placeholders.Номер телефона')); ?>">
                            <?php $__errorArgs = ['callback_phone'];
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
                        <button class="main-button main-button-else"><?php echo e(__('main.Отправить')); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__menu">
        <div class="footer__menu__wrapper container js-drop-item">
            <button class="footer__mobile-button js-drop-button">
                <span><?php echo e(__('main.Полезные ссылки')); ?></span>
                <span class="icon-drop"></span>
            </button>
            <ul class="footer__menu__list">
                <?php $__currentLoopData = $footerMenu->children->sortBy('lft'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="footer__menu__item">
                    <a href="<?php echo e($menuItem->url()); ?>" class="footer__menu__link"><?php echo e($menuItem->name); ?></a>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
    <div class="footer__category">
        <div class="footer__category__wrapper container">
            <ul class="footer__category__list">
                <?php $__currentLoopData = $footerSubMenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="footer__category__item js-drop-item js-catagory-links-item">
                    <button class="footer__mobile-button js-drop-button">
                        <span><?php echo e(str_replace(['(подвал)', '(підвал)'], '', $menu->name)); ?></span>
                        <span class="icon-drop"></span>
                    </button>
                    <h5 class="footer__caption"><?php echo e(str_replace(['(подвал)', '(підвал)'], '', $menu->name)); ?></h5>
                    <ul class="footer__sub-category__list">
                        <?php $__currentLoopData = $menu->children->sortBy('lft'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="footer__sub-category__item js-sub-link">
                            <a href="<?php echo e($menuItem->url()); ?>" class="footer__sub-category__link"><?php echo e($menuItem->name); ?></a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button class="footer__sub-button js-drop-button js-category-button">
                        <span class="icon-drop"></span>
                    </button>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <div id="SinoptikInformer" style="width:350px;" class="SinoptikInformer type5c1"><div class="siHeader"><div class="siLh"><div class="siMh"><a onmousedown="siClickCount();" class="siLogo" href="https://sinoptik.ua/" target="_blank" rel="nofollow" title="Погода"> </a>Погода <span id="siHeader"></span></div></div></div><div class="siBody"><a onmousedown="siClickCount();" href="https://sinoptik.ua/погода-киев" title="Погода в Киеве" target="_blank"><div class="siCity"><div class="siCityName"><span>Киев</span></div><div id="siCont0" class="siBodyContent"><div class="siLeft"><div class="siTerm"></div><div class="siT" id="siT0"></div><div id="weatherIco0"></div></div><div class="siInf"><p>влажность: <span id="vl0"></span></p><p>давление: <span id="dav0"></span></p><p>ветер: <span id="wind0"></span></p></div></div></div></a><div class="siLinks">Погода на 10 дней от <a href="https://sinoptik.ua/погода-киев/10-дней" title="Погода на 10 дней" target="_blank" onmousedown="siClickCount();" rel="nofollow">sinoptik.ua</a></div></div><div class="siFooter"><div class="siLf"><div class="siMf"></div></div></div></div>
            </div>
    </div>
    <div class="footer__copyright">
        <div class="footer__copyright__wrapper container">
	        <div>
	            <span>© Copyright 2010-<?php echo e(now()->format('Y')); ?> - Zagorodna.com </span> | 
	            <a href="<?php echo e($policyLink); ?>" class="footer__copyright__link"><?php echo e(__('main.Политика конфиденциальности')); ?></a> | 
	            <a href="<?php echo e(url('sitemap.xml')); ?>" target="_blank" class="footer__copyright__link"><?php echo e(__('main.Карта сайта')); ?></a>
	        </div>
	        
<!-- 	        include('includes.bigmir') -->
      

        </div>
    </div>
</footer><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/layouts/footer.blade.php ENDPATH**/ ?>