<?php if($crud->hasAccess('clone') && !$entry->original): ?>
	<a href="javascript:void(0)" onclick="cloneEntry(this)" data-route="<?php echo e(url($crud->route.'/'.$entry->getKey().'/clone')); ?>" class="btn btn-sm btn-link" data-button-type="clone"><i class="fa fa-clone"></i> <?php echo e(trans('backpack::crud.clone')); ?></a>
<?php endif; ?>




<?php $__env->startPush('after_scripts'); ?> <?php if($crud->request->ajax()): ?> <?php $__env->stopPush(); ?> <?php endif; ?>
<script>
	if (typeof cloneEntry != 'function') {
	  $("[data-button-type=clone]").unbind('click');

	  function cloneEntry(button) {
	      // ask for confirmation before deleting an item
	      // e.preventDefault();
	      var button = $(button);
	      var route = button.attr('data-route');

          $.ajax({
              url: route,
              type: 'POST',
              success: function(result) {
                  // Show an alert with the result
                  new Noty({
                    type: "success",
                    text: "<?php echo trans('backpack::crud.clone_success'); ?>"
                  }).show();

                  // Hide the modal, if any
                  $('.modal').modal('hide');

                  if (typeof crud !== 'undefined') {
                    crud.table.ajax.reload();
                  }
              },
              error: function(result) {
                  // Show an alert with the result
                  new Noty({
                    type: "warning",
                    text: "<?php echo trans('backpack::crud.clone_failure'); ?>"
                  }).show();
              }
          });
      }
	}

	// make it so that the function above is run after each DataTable draw event
	// crud.addFunctionToDataTablesDrawEventQueue('cloneEntry');
</script>
<?php if(!$crud->request->ajax()): ?> <?php $__env->stopPush(); ?> <?php endif; ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/vendor/backpack/crud/buttons/clone.blade.php ENDPATH**/ ?>