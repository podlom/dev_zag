
<?php
    $style = isset($column['style'])? $column['style'] : '';
?>
<span style="<?php echo e($style); ?>">
    <?php echo $column['function']($entry); ?>

</span><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/closure.blade.php ENDPATH**/ ?>