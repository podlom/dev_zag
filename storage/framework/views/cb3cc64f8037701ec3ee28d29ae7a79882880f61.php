
<?php
$checkValue = data_get($entry, $column['name']);

$checkedIcon = data_get($column, 'icons.checked', 'la-check-circle');
$uncheckedIcon = data_get($column, 'icons.unchecked', 'la-circle');

$exportCheckedText = data_get($column, 'labels.checked', trans('backpack::crud.yes'));
$exportUncheckedText = data_get($column, 'labels.unchecked', trans('backpack::crud.no'));

$icon = $checkValue == false ? $uncheckedIcon : $checkedIcon;
$text = $checkValue == false ? $exportUncheckedText : $exportCheckedText;
?>

<span>
    <i class="la <?php echo e($icon); ?>"></i>
</span>

<span class="sr-only"><?php echo e($text); ?></span>
<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/check.blade.php ENDPATH**/ ?>