
<span>
    <?php
        $attributes = $crud->getModelAttributeFromRelation($entry, $column['entity'], $column['attribute']);
        if (count($attributes)) {
            echo e(str_limit(strip_tags(implode(', ', $attributes)), array_key_exists('limit', $column) ? $column['limit'] : 40, '[...]'));
        } else {
            echo '-';
        }
    ?>
</span>
<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/select.blade.php ENDPATH**/ ?>