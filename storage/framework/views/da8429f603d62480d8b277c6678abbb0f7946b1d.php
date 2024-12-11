
<?php
    $values = data_get($entry, $column['name']);
?>

<span>
	<?php
        if ($values !== null) {
            if (is_array($values)) {
                $array_of_values = [];

                foreach ($values as $key => $value) {
                    if (! is_null($value)) {
                        $array_of_values[] = $column['options'][$value] ?? $value;
                    } else {
                        echo '-';
                        continue;
                    }
                }

                if (count($array_of_values) > 1) {
                    echo implode(', ', $array_of_values);
                } else {
                    echo array_first($array_of_values);
                }
            } else {
                echo $column['options'][$values] ?? $values;
            }
        } else {
            echo '-';
        }
    ?>
</span>
<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/columns/select_from_array.blade.php ENDPATH**/ ?>