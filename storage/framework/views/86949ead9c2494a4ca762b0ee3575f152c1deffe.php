
<?php
    $value = data_get($entry, $column['name']);
?>

<span data-order="<?php echo e($value); ?>">
	<?php if($value === true || $value === 1 || $value === '1'): ?>
        <?php if( isset( $column['options'][1] ) ): ?>
            <?php echo $column['options'][1]; ?>

        <?php else: ?>
            <?php echo e(Lang::has('backpack::crud.yes')?trans('backpack::crud.yes'):'Yes'); ?>

        <?php endif; ?>
    <?php else: ?>
        <?php if( isset( $column['options'][0] ) ): ?>
            <?php echo $column['options'][0]; ?>

        <?php else: ?>
            <?php echo e(Lang::has('backpack::crud.no')?trans('backpack::crud.no'):'No'); ?>

        <?php endif; ?>
    <?php endif; ?>
</span><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/boolean.blade.php ENDPATH**/ ?>