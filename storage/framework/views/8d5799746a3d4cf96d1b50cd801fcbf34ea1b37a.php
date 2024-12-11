
<?php
	$value = data_get($entry, $column['name']);
?>

<span><a href="mailto:<?php echo e($entry->{$column['name']}); ?>"><?php echo e(str_limit(strip_tags($value), array_key_exists('limit', $column) ? $column['limit'] : 40, "[...]")); ?></a></span><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/email.blade.php ENDPATH**/ ?>