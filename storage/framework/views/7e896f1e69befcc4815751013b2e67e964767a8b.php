
<?php
    $value = data_get($entry, $column['name']);
?>

<span data-order="<?php echo e($value); ?>">
    <?php if(!empty($value)): ?>
	<?php echo e(\Carbon\Carbon::parse($value)
		->locale(App::getLocale())
		->isoFormat($column['format'] ?? config('backpack.base.default_datetime_format'))); ?>

    <?php endif; ?>
</span><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/datetime.blade.php ENDPATH**/ ?>