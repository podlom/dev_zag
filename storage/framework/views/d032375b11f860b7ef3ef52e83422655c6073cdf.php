

<li filter-name="<?php echo e($filter->name); ?>"
	filter-type="<?php echo e($filter->type); ?>"
	class="nav-item <?php echo e(Request::get($filter->name)?'active':''); ?>">
    <a class="nav-link" href=""
		parameter="<?php echo e($filter->name); ?>"
    	><?php echo e($filter->label); ?></a>
  </li>








    





<?php $__env->startPush('crud_list_scripts'); ?>
    <script>
		jQuery(document).ready(function($) {
			$("li[filter-name=<?php echo e($filter->name); ?>] a").click(function(e) {
				e.preventDefault();

				var parameter = $(this).attr('parameter');

		    	// behaviour for ajax table
				var ajax_table = $("#crudTable").DataTable();
				var current_url = ajax_table.ajax.url();

				if (URI(current_url).hasQuery(parameter)) {
					var new_url = URI(current_url).removeQuery(parameter, true);
				} else {
					var new_url = URI(current_url).addQuery(parameter, true);
				}

				new_url = normalizeAmpersand(new_url.toString());

				// replace the datatables ajax url with new_url and reload it
				ajax_table.ajax.url(new_url).load();

				// add filter to URL
				crud.updateUrl(new_url);

				// mark this filter as active in the navbar-filters
				if (URI(new_url).hasQuery('<?php echo e($filter->name); ?>', true)) {
					$("li[filter-name=<?php echo e($filter->name); ?>]").removeClass('active').addClass('active');
                    $('#remove_filters_button').removeClass('hidden');
				}
				else
				{
					$("li[filter-name=<?php echo e($filter->name); ?>]").trigger("filter:clear");
				}
			});

			// clear filter event (used here and by the Remove all filters button)
			$("li[filter-name=<?php echo e($filter->name); ?>]").on('filter:clear', function(e) {
				// console.log('dropdown filter cleared');
				$("li[filter-name=<?php echo e($filter->name); ?>]").removeClass('active');
			});
		});
	</script>
<?php $__env->stopPush(); ?>


<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/vendor/backpack/crud/src/resources/views/crud/filters/simple.blade.php ENDPATH**/ ?>