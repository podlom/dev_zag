
<?php
    $value = data_get($entry, $column['name']);
    $value = is_string($value) ? $value : ''; // don't try to show arrays/object if the column was autoSet
?>

<span><?php echo $value; ?></span><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/textarea.blade.php ENDPATH**/ ?>