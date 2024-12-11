
<?php
   $column['text'] = data_get($entry, $column['name'])->count();
   $column['prefix'] = $column['prefix'] ?? '';
   $column['suffix'] = $column['suffix'] ?? '  items';
   $column['text'] = $column['prefix'].$column['text'].$column['suffix'];
?>

<span>
    <?php echo $__env->renderWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
        <?php echo e($column['text']); ?>

    <?php echo $__env->renderWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</span><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/relationship_count.blade.php ENDPATH**/ ?>