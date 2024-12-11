<?php if(config('backpack.base.show_powered_by') || config('backpack.base.developer_link')): ?>
    <div class="text-muted ml-auto mr-auto">
      <?php if(config('backpack.base.developer_link') && config('backpack.base.developer_name')): ?>
      <?php echo e(trans('backpack::base.handcrafted_by')); ?> <a target="_blank" href="<?php echo e(config('backpack.base.developer_link')); ?>"><?php echo e(config('backpack.base.developer_name')); ?></a>.
      <?php endif; ?>
      <?php if(config('backpack.base.show_powered_by')): ?>
      <?php echo e(trans('backpack::base.powered_by')); ?> <a target="_blank" href="http://backpackforlaravel.com?ref=panel_footer_link">Backpack for Laravel</a>.
      <?php endif; ?>
    </div>
<?php endif; ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/base/inc/footer.blade.php ENDPATH**/ ?>