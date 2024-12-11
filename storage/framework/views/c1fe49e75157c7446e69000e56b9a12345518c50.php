<!-- CKeditor -->
<div <?php echo $__env->make('crud::inc.field_wrapper_attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> >
    <label><?php echo $field['label']; ?></label>
    <?php echo $__env->make('crud::inc.field_translatable_icon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <textarea
    	id="ckeditor-<?php echo e($field['name']); ?>"
        name="<?php echo e($field['name']); ?>"
        data-init-function="bpFieldInitCKEditorElement"
        <?php echo $__env->make('crud::inc.field_attributes', ['default_class' => 'form-control'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    	><?php echo e(old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? ''); ?></textarea>

    
    <?php if(isset($field['hint'])): ?>
        <p class="help-block"><?php echo $field['hint']; ?></p>
    <?php endif; ?>
</div>





<?php if($crud->fieldTypeNotLoaded($field)): ?>
    <?php
        $crud->markFieldTypeAsLoaded($field);
    ?>

    
    <?php $__env->startPush('crud_fields_styles'); ?>
    <?php $__env->stopPush(); ?>

    
    <?php $__env->startPush('crud_fields_scripts'); ?>
        <script src="<?php echo e(asset('packages/ckeditor/ckeditor.js')); ?>"></script>
        <script src="<?php echo e(asset('packages/ckeditor/adapters/jquery.js')); ?>"></script>
        <script>
            function bpFieldInitCKEditorElement(element) {
                // remove any previous CKEditors from right next to the textarea
                element.siblings("[id^='cke_ckeditor']").remove();

                // trigger a new CKEditor
                element.ckeditor({
                    "filebrowserBrowseUrl": "<?php echo e(url(config('backpack.base.route_prefix').'/elfinder/ckeditor')); ?>",
                    "extraPlugins" : 'wordcount,<?php echo e(isset($field['extra_plugins']) ? implode(',', $field['extra_plugins']) : 'embed,widget'); ?>',
                    "embed_provider": '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}'
                    <?php if(isset($field['options']) && count($field['options'])): ?>
                        <?php echo ', '.trim(json_encode($field['options']), "{}"); ?>

                    <?php endif; ?>
                });
            }
        </script>
    <?php $__env->stopPush(); ?>

<?php endif; ?>



<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/vendor/backpack/crud/fields/ckeditor.blade.php ENDPATH**/ ?>