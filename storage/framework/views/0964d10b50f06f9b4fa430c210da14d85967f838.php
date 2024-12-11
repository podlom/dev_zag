
<?php
	$value = data_get($entry, $column['name']);
	$style = isset($column['style'])? $column['style'] : '';
	if (is_array($value)) {
		$value = json_encode($value);
	}
?>

<span style="<?php echo e($style); ?>"><?php echo e((array_key_exists('prefix', $column) ? $column['prefix'] : '').str_limit(strip_tags($value), array_key_exists('limit', $column) ? $column['limit'] : 40, "[...]").(array_key_exists('suffix', $column) ? $column['suffix'] : '')); ?></span><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/text.blade.php ENDPATH**/ ?>