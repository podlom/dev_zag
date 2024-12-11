<?php
    $horizontalTabs = $crud->getTabsType()=='horizontal' ? true : false;

    if ($errors->any() && array_key_exists(array_keys($errors->messages())[0], $crud->getCurrentFields()) &&
        array_key_exists('tab', $crud->getCurrentFields()[array_keys($errors->messages())[0]])) {
        $tabWithError = ($crud->getCurrentFields()[array_keys($errors->messages())[0]]['tab']);
    }
?>

<?php $__env->startPush('crud_fields_styles'); ?>
    <style>
        .nav-tabs-custom {
            box-shadow: none;
        }
        .nav-tabs-custom > .nav-tabs.nav-stacked > li {
            margin-right: 0;
        }

        .tab-pane .form-group h1:first-child,
        .tab-pane .form-group h2:first-child,
        .tab-pane .form-group h3:first-child {
            margin-top: 0;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php if($crud->getFieldsWithoutATab()->filter(function ($value, $key) { return $value['type'] != 'hidden'; })->count()): ?>
<div class="card">
    <div class="card-body row">
    <?php echo $__env->make('crud::inc.show_fields', ['fields' => $crud->getFieldsWithoutATab()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
<?php else: ?>
    <?php echo $__env->make('crud::inc.show_fields', ['fields' => $crud->getFieldsWithoutATab()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<div class="tab-container <?php echo e($horizontalTabs ? '' : 'container'); ?> mb-2">

    <div class="nav-tabs-custom <?php echo e($horizontalTabs ? '' : 'row'); ?>" id="form_tabs">
        <ul class="nav <?php echo e($horizontalTabs ? 'nav-tabs' : 'flex-column nav-pills'); ?> <?php echo e($horizontalTabs ? '' : 'col-md-3'); ?>" role="tablist">
            <?php $__currentLoopData = $crud->getTabs(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li role="presentation" class="nav-item">
                    <a href="#tab_<?php echo e(str_slug($tab)); ?>" 
                        aria-controls="tab_<?php echo e(str_slug($tab)); ?>" 
                        role="tab" 
                        tab_name="<?php echo e(str_slug($tab)); ?>" 
                        data-toggle="tab" 
                        class="nav-link <?php echo e(isset($tabWithError) ? ($tab == $tabWithError ? 'active' : '') : ($k == 0 ? 'active' : '')); ?>"
                        ><?php echo e($tab); ?></a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

        <div class="tab-content p-0 <?php echo e($horizontalTabs ? '' : 'col-md-9'); ?>">

            <?php $__currentLoopData = $crud->getTabs(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div role="tabpanel" class="tab-pane <?php echo e(isset($tabWithError) ? ($tab == $tabWithError ? ' active' : '') : ($k == 0 ? ' active' : '')); ?>" id="tab_<?php echo e(str_slug($tab)); ?>">

                <div class="row">
                <?php echo $__env->make('crud::inc.show_fields', ['fields' => $crud->getTabFields($tab)], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>
    </div>
</div>

<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/inc/show_tabbed_fields.blade.php ENDPATH**/ ?>