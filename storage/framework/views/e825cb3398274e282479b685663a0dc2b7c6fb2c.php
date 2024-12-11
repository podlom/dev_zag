<div class="<?php echo e($widget['wrapperClass'] ?? ''); ?>">
	<div class="jumbotron mb-2">

	  <?php if(isset($widget['heading'])): ?>
	  <h1 class="display-3"><?php echo $widget['heading']; ?></h1>
	  <?php endif; ?>

	  <?php if(isset($widget['content'])): ?>
	  <p><?php echo $widget['content']; ?></p>
	  <?php endif; ?>

	  <?php if(isset($widget['button_link'])): ?>
	  <p class="lead">
	    <a class="btn btn-primary" href="<?php echo e($widget['button_link']); ?>" role="button"><?php echo e($widget['button_text']); ?></a>
	  </p>
	  <?php endif; ?>
	</div>
</div><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/base/widgets/jumbotron.blade.php ENDPATH**/ ?>