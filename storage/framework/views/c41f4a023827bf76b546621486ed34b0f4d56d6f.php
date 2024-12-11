<!-- browse server input -->

<div <?php echo $__env->make('crud::inc.field_wrapper_attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> >

    <label><?php echo $field['label']; ?></label>
    <?php echo $__env->make('crud::inc.field_translatable_icon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="controls">
	    <div class="input-group">
			<input
				type="text"
				id="<?php echo e($field['name']); ?>-filemanager"
				name="<?php echo e($field['name']); ?>"
		        value="<?php echo e(old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? ''); ?>"
		        data-init-function="bpFieldInitBrowseElement"
		        data-elfinder-trigger-url="<?php echo e(url(config('elfinder.route.prefix').'/popup')); ?>"
		        <?php echo $__env->make('crud::inc.field_attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				<?php if(!isset($field['readonly']) || $field['readonly']): ?> readonly <?php endif; ?>
			>

			<span class="input-group-append">
			  	<button type="button" data-inputid="<?php echo e($field['name']); ?>-filemanager" class="btn btn-light btn-sm popup_selector"><i class="fa fa-cloud-upload"></i> <?php echo e(trans('backpack::crud.browse_uploads')); ?></button>
				<button type="button" data-inputid="<?php echo e($field['name']); ?>-filemanager" class="btn btn-light btn-sm clear_elfinder_picker"><i class="fa fa-eraser"></i> <?php echo e(trans('backpack::crud.clear')); ?></button>
			</span>
		</div>
	</div>

	<?php if(isset($field['hint'])): ?>
        <p class="help-block"><?php echo $field['hint']; ?></p>
    <?php endif; ?>

</div>




<?php if($crud->fieldTypeNotLoaded($field)): ?>
	<?php
		$crud->markFieldTypeAsLoaded($field);
	?>

	
	<?php $__env->startPush('crud_fields_styles'); ?>
		<!-- include browse server css -->
		<link href="<?php echo e(asset('packages/jquery-colorbox/example2/colorbox.css')); ?>" rel="stylesheet" type="text/css" />
		<style>
			#cboxContent, #cboxLoadedContent, .cboxIframe {
				background: transparent;
			}
		</style>
	<?php $__env->stopPush(); ?>

	<?php $__env->startPush('crud_fields_scripts'); ?>
		<!-- include browse server js -->
		<script src="<?php echo e(asset('packages/jquery-colorbox/jquery.colorbox-min.js')); ?>"></script>
		<script type="text/javascript">
			// this global variable is used to remember what input to update with the file path
			// because elfinder is actually loaded in an iframe by colorbox
			var elfinderTarget = false;

			// function to update the file selected by elfinder
			function processSelectedFile(filePath, requestingField) {
				elfinderTarget.val(filePath.replace(/\\/g,"/"));
				elfinderTarget = false;
			}

			function bpFieldInitBrowseElement(element) {
				var triggerUrl = element.data('elfinder-trigger-url')
				var name = element.attr('name');

				element.siblings('.input-group-append').children('button.popup_selector').click(function (event) {
				    event.preventDefault();

				    elfinderTarget = element;

				    // trigger the reveal modal with elfinder inside
				    $.colorbox({
				        href: triggerUrl + '/' + name,
				        fastIframe: true,
				        iframe: true,
				        width: '80%',
				        height: '80%'
				    });
				});

				element.siblings('.input-group-append').children('button.clear_elfinder_picker').click(function (event) {
				    event.preventDefault();
				    element.val("");
				});
			}
		</script>
	<?php $__env->stopPush(); ?>

<?php endif; ?>



<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/fields/browse.blade.php ENDPATH**/ ?>