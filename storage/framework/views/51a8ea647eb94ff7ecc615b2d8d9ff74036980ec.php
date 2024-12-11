
<?php
    $value = data_get($entry, $column['name']);
    if ($value != null) {
    	$value = number_format($value,
		    array_key_exists('decimals', $column) ? $column['decimals'] : 0,
		    array_key_exists('dec_point', $column) ? $column['dec_point'] : '.',
		    array_key_exists('thousands_sep', $column) ? $column['thousands_sep'] : ','
		 );
    }
?>
<span><?php echo e((array_key_exists('prefix', $column) ? $column['prefix'] : '').$value.(array_key_exists('suffix', $column) ? $column['suffix'] : '')); ?></span><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/number.blade.php ENDPATH**/ ?>