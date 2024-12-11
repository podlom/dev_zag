<?php if($crud->buttons()->where('stack', $stack)->count()): ?>
	<?php $__currentLoopData = $crud->buttons()->where('stack', $stack); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	  <?php if($button->type == 'model_function'): ?>
		<?php if($stack == 'line'): ?>
	  		  <?php echo $entry->{$button->content}($crud);; ?>

		<?php else: ?>
			  <?php echo $crud->model->{$button->content}($crud);; ?>

		<?php endif; ?>
	  <?php else: ?>
		<?php echo $__env->make($button->content, ['button' => $button], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	  <?php endif; ?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/inc/button_stack.blade.php ENDPATH**/ ?>