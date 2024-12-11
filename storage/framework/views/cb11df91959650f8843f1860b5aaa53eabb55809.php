<?php if($crud->hasAccess('delete')): ?>
	<a href="javascript:void(0)" onclick="deleteEntry(this)" data-route="<?php echo e(url($crud->route.'/'.$entry->getKey())); ?>" class="btn btn-sm btn-link" data-button-type="delete"><i class="fa fa-trash"></i> <?php echo e(trans('backpack::crud.delete')); ?></a>
<?php endif; ?>




<?php $__env->startPush('after_scripts'); ?> <?php if($crud->request->ajax()): ?> <?php $__env->stopPush(); ?> <?php endif; ?>
<script>

	if (typeof deleteEntry != 'function') {
	  $("[data-button-type=delete]").unbind('click');

	  function deleteEntry(button) {
		// ask for confirmation before deleting an item
		// e.preventDefault();
		var button = $(button);
		var route = button.attr('data-route');
		var row = $("#crudTable a[data-route='"+route+"']").closest('tr');

		swal({
		  title: "<?php echo trans('backpack::base.warning'); ?>",
		  text: "<?php echo trans('backpack::crud.delete_confirm'); ?>",
		  icon: "warning",
		  buttons: {
		  	cancel: {
			  text: "<?php echo trans('backpack::crud.cancel'); ?>",
			  value: null,
			  visible: true,
			  className: "bg-secondary",
			  closeModal: true,
			},
		  	delete: {
			  text: "<?php echo trans('backpack::crud.delete'); ?>",
			  value: true,
			  visible: true,
			  className: "bg-danger",
			}
		  },
		}).then((value) => {
			if (value) {
				$.ajax({
			      url: route,
			      type: 'DELETE',
			      success: function(result) {
			          if (result == 1) {
			          	  // Show a success notification bubble
			              new Noty({
		                    type: "success",
		                    text: "<?php echo '<strong>'.trans('backpack::crud.delete_confirmation_title').'</strong><br>'.trans('backpack::crud.delete_confirmation_message'); ?>"
		                  }).show();

			              // Hide the modal, if any
			              $('.modal').modal('hide');

			              // Remove the details row, if it is open
			              if (row.hasClass("shown")) {
			                  row.next().remove();
			              }

			              // Remove the row from the datatable
			              row.remove();
			          } else {
			              // if the result is an array, it means 
			              // we have notification bubbles to show
			          	  if (result instanceof Object) {
			          	  	// trigger one or more bubble notifications 
			          	  	Object.entries(result).forEach(function(entry, index) {
			          	  	  var type = entry[0];
			          	  	  entry[1].forEach(function(message, i) {
					          	  new Noty({
				                    type: type,
				                    text: message
				                  }).show();
			          	  	  });
			          	  	});
			          	  } else {// Show an error alert
				              swal({
				              	title: "<?php echo trans('backpack::crud.delete_confirmation_not_title'); ?>",
	                            text: "<?php echo trans('backpack::crud.delete_confirmation_not_message'); ?>",
				              	icon: "error",
				              	timer: 4000,
				              	buttons: false,
				              });
			          	  }			          	  
			          }
			      },
			      error: function(result) {
			          // Show an alert with the result
			          swal({
		              	title: "<?php echo trans('backpack::crud.delete_confirmation_not_title'); ?>",
                        text: "<?php echo trans('backpack::crud.delete_confirmation_not_message'); ?>",
		              	icon: "error",
		              	timer: 4000,
		              	buttons: false,
		              });
			      }
			  });
			}
		});

      }
	}

	// make it so that the function above is run after each DataTable draw event
	// crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
</script>
<?php if(!$crud->request->ajax()): ?> <?php $__env->stopPush(); ?> <?php endif; ?>
<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/buttons/delete.blade.php ENDPATH**/ ?>