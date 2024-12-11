<?php
// if the field is required in the FormRequest, it should have an asterisk
$required = (isset($action) && $crud->isRequired($field['name'], $action)) ? ' required' : '';
// if the developer has intentionally set the required attribute on the field
// forget whatever is in the FormRequest, do what the developer wants
$required = (isset($field['showAsterisk'])) ? ($field['showAsterisk'] ? ' required' : '') : $required;
?>

<?php if(isset($field['wrapperAttributes'])): ?>
    <?php if(!isset($field['wrapperAttributes']['class'])): ?>
        class="form-group col-sm-12 <?php echo e($required); ?>"
    <?php else: ?>
        class="<?php echo e($field['wrapperAttributes']['class']); ?> <?php echo e($required); ?>"
    <?php endif; ?>

    <?php
        unset($field['wrapperAttributes']['class']);
    ?>

    <?php $__currentLoopData = $field['wrapperAttributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(is_string($attribute)): ?>
            <?php echo e($attribute); ?>="<?php echo e($value); ?>"
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php else: ?>
    class="form-group col-sm-12<?php echo e($required); ?>"
<?php endif; ?>
<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/inc/field_wrapper_attributes.blade.php ENDPATH**/ ?>