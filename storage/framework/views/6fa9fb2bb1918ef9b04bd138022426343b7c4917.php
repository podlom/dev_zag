<!-- configurable color picker -->

<div <?php echo $__env->make('crud::inc.field_wrapper_attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> >
    <label><?php echo $field['label']; ?></label>
    <?php echo $__env->make('crud::inc.field_translatable_icon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="input-group colorpicker-component">

        <input
        	type="text"
        	name="<?php echo e($field['name']); ?>"
            value="<?php echo e(old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? ''); ?>"
            data-init-function="bpFieldInitColorPickerElement"
            <?php echo $__env->make('crud::inc.field_attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        	>
        <div class="input-group-addon">
            <i class="color-preview-<?php echo e($field['name']); ?>"></i>
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
        <link rel="stylesheet" href="<?php echo e(asset('packages/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css')); ?>" />
    <?php $__env->stopPush(); ?>

    
    <?php $__env->startPush('crud_fields_scripts'); ?>
    <script type="text/javascript" src="<?php echo e(asset('packages/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js')); ?>"></script>
    <script>
        function bpFieldInitColorPickerElement(element) {
            // https://itsjaviaguilar.com/bootstrap-colorpicker/
            var config = jQuery.extend({}, <?php echo isset($field['color_picker_options']) ? json_encode($field['color_picker_options']) : '{}'; ?>);
            var picker = element.parents('.colorpicker-component').colorpicker(config);

            element.on('focus', function(){
                picker.colorpicker('show');
            });
        }
    </script>
    <?php $__env->stopPush(); ?>

<?php endif; ?>



<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/fields/color_picker.blade.php ENDPATH**/ ?>