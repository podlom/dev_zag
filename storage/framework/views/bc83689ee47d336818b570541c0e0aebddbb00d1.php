<?php
$multiple = array_get($field, 'multiple', true);
$sortable = array_get($field, 'sortable', false);
$value = old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '';

if (!$multiple && is_array($value)) {
    $value = array_first($value);
}

if (!isset($field['wrapperAttributes']) || !isset($field['wrapperAttributes']['data-init-function']))
{
    $field['wrapperAttributes']['data-init-function'] = 'bpFieldInitBrowseMultipleElement';

    if ($multiple) {
        $field['wrapperAttributes']['data-popup-title'] = trans('backpack::crud.select_files');
        $field['wrapperAttributes']['data-multiple'] = "true";
    } else {
        $field['wrapperAttributes']['data-popup-title'] = trans('backpack::crud.select_file');
        $field['wrapperAttributes']['data-multiple'] = "false";
    }
    $field['wrapperAttributes']['data-only-mimes'] = json_encode($field['mime_types'] ?? []);

    if($sortable){
        $field['wrapperAttributes']['sortable'] = "true";
    }
}
?>

<div <?php echo $__env->make('crud::inc.field_wrapper_attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> >

    <div><label><?php echo $field['label']; ?></label></div>
    <?php echo $__env->make('crud::inc.field_translatable_icon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if($multiple): ?>
        <div class="list">
            <?php $__currentLoopData = (array)$value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($v): ?>
                    <div class="input-group input-group-sm">
                        <input type="text" name="<?php echo e($field['name']); ?>[]" value="<?php echo e($v); ?>" data-marker="multipleBrowseInput"
                                <?php echo $__env->make('crud::inc.field_attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> readonly>
                        <div class="input-group-btn">
                            <button type="button" class="browse remove btn btn-sm btn-light">
                                <i class="la la-trash"></i>
                            </button>
                            <?php if($sortable): ?>
                                <button type="button" class="browse move btn btn-sm btn-light"><span class="la la-sort"></span></button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <input type="text" name="<?php echo e($field['name']); ?>" value="<?php echo e($value); ?>" <?php echo $__env->make('crud::inc.field_attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> readonly>
    <?php endif; ?>

    <div class="btn-group" role="group" aria-label="..." style="margin-top: 3px;">
        <button type="button" class="browse popup btn btn-sm btn-light">
            <i class="la la-cloud-upload"></i>
            <?php echo e(trans('backpack::crud.browse_uploads')); ?>

        </button>
        <button type="button" class="browse clear btn btn-sm btn-light">
            <i class="la la-eraser"></i>
            <?php echo e(trans('backpack::crud.clear')); ?>

        </button>
    </div>

    <?php if(isset($field['hint'])): ?>
        <p class="help-block"><?php echo $field['hint']; ?></p>
    <?php endif; ?>

    <script type="text/html" data-marker="browse_multiple_template">
        <div class="input-group input-group-sm">
            <input type="text" name="<?php echo e($field['name']); ?>[]" <?php echo $__env->make('crud::inc.field_attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> readonly>
            <div class="input-group-btn">
                <button type="button" class="browse remove btn btn-sm btn-light">
                    <i class="la la-trash"></i>
                </button>
                <?php if($sortable): ?>
                    <button type="button" class="browse move btn btn-sm btn-light"><span class="la la-sort"></span></button>
                <?php endif; ?>
            </div>
        </div>
    </script>
</div>





<?php if($crud->fieldTypeNotLoaded($field)): ?>
    <?php
        $crud->markFieldTypeAsLoaded($field);
    ?>

    
    <?php $__env->startPush('crud_fields_styles'); ?>
        <!-- include browse server css -->
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('packages/jquery-ui-dist/jquery-ui.min.css')); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('packages/barryvdh/elfinder/css/elfinder.min.css')); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('packages/barryvdh/elfinder/css/theme.css')); ?>">
        <link href="<?php echo e(asset('packages/jquery-colorbox/example2/colorbox.css')); ?>" rel="stylesheet" type="text/css" />
        <style>
            #cboxContent, #cboxLoadedContent, .cboxIframe {
                background: transparent;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('crud_fields_scripts'); ?>
        <!-- include browse server js -->
        <script src="<?php echo e(asset('packages/jquery-ui-dist/jquery-ui.min.js')); ?>"></script>
        <script src="<?php echo e(asset('packages/jquery-colorbox/jquery.colorbox-min.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('packages/barryvdh/elfinder/js/elfinder.min.js')); ?>"></script>
        
        <?php if( ($locale = \App::getLocale()) != 'en' ): ?>
            <script type="text/javascript" src="<?php echo e(asset("packages/barryvdh/elfinder/js/i18n/elfinder.{$locale}.js")); ?>"></script>
        <?php endif; ?>

        <script>
            function bpFieldInitBrowseMultipleElement(element) {
                var $template = element.find("[data-marker=browse_multiple_template]").html();
                var $list = element.find(".list");
                var $popupButton = element.find(".popup");
                var $clearButton = element.find(".clear");
                var $removeButton = element.find(".remove");
                var $input = element.find('input[data-marker=multipleBrowseInput]');
                var $popupTitle = element.attr('data-popup-title');
                var $onlyMimesArray = JSON.parse(element.attr('data-only-mimes'));
                var $multiple = element.attr('data-multiple');
                var $sortable = element.attr('sortable');

                if($sortable){
                    $list.sortable({
                        handle: 'button.move',
                        cancel: ''
                    });
                }

                element.on('click', 'button.popup', function (event) {
                    event.preventDefault();

                    var div = $('<div>');
                    div.elfinder({
                        lang: '<?php echo e(\App::getLocale()); ?>',
                        customData: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        url: '<?php echo e(route("elfinder.connector")); ?>',
                        soundPath: '<?php echo e(asset('/packages/barryvdh/elfinder/sounds')); ?>',
                        dialog: {
                            width: 900,
                            modal: true,
                            title: $popupTitle,
                        },
                        resizable: false,
                        onlyMimes: $onlyMimesArray,
                        commandsOptions: {
                            getfile: {
                                multiple: $multiple,
                                oncomplete: 'destroy'
                            }
                        },
                        getFileCallback: function (files) {
                            if ($multiple) {
                                files.forEach(function (file) {
                                    var newInput = $($template);
                                    newInput.find('input').val(file.path);
                                    $list.append(newInput);
                                });

                                if($sortable){
                                    $list.sortable("refresh")
                                }
                            } else {
                                $input.val(files.path);
                            }

                            $.colorbox.close();
                        }
                    }).elfinder('instance');

                    // trigger the reveal modal with elfinder inside
                    $.colorbox({
                        href: div,
                        inline: true,
                        width: '80%',
                        height: '80%'
                    });
                });

                element.on('click', 'button.clear', function (event) {
                    event.preventDefault();

                    if ($multiple) {
                        $input.parents('.input-group').remove();
                    } else {
                        $input.val('');
                    }
                });

                if ($multiple) {
                    element.on('click', 'button.remove', function (event) {
                        event.preventDefault();
                        $(this).parents('.input-group').remove();
                    });
                }
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>



<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/fields/browse_multiple.blade.php ENDPATH**/ ?>