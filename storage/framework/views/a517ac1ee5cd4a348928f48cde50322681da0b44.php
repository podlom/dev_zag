<?php if(config('backpack.base.scripts') && count(config('backpack.base.scripts'))): ?>
    <?php $__currentLoopData = config('backpack.base.scripts'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <script type="text/javascript" src="<?php echo e(asset($path).'?v='.config('backpack.base.cachebusting_string')); ?>"></script>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<?php if(config('backpack.base.mix_scripts') && count(config('backpack.base.mix_scripts'))): ?>
    <?php $__currentLoopData = config('backpack.base.mix_scripts'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path => $manifest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <script type="text/javascript" src="<?php echo e(mix($path, $manifest)); ?>"></script>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<?php echo $__env->make('backpack::inc.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- page script -->
<script type="text/javascript">
    // To make Pace works on Ajax calls
    $(document).ajaxStart(function() { Pace.restart(); });

    // Ajax calls should always have the CSRF token attached to them, otherwise they won't work
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    
    var activeTab = $('[href="' + location.hash.replace("#", "#tab_") + '"]');
    location.hash && activeTab && activeTab.tab('show');
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        location.hash = e.target.hash.replace("#tab_", "#");
    });
</script>

<!-- Count input chars -->
<script>
    $(document).ready(function() {
        $('input[type=text]:not([readonly]):not([data-bs-datetimepicker]), textarea:not([data-init-function="bpFieldInitCKEditorElement"])').each(function() {
            $(this).parents('.form-group').find('label').append('<span class="chars_count"></span>');
            countChars($(this));
        });

        $('input[type=text]:not([readonly]):not([data-bs-datetimepicker]), textarea:not([data-init-function="bpFieldInitCKEditorElement"])').keyup(function(e) {
            countChars($(this));
            
            if($(this).attr('name') === 'meta_title' || $(this).attr('name') === 'meta_description' || $(this).attr('name') === 'meta_desc')
                compareMeta($(this));
        });

        function countChars(element) {
            let length = element.val().length;
            element.parents('.form-group').find('.chars_count').text(length);

            if((element.attr('name') === 'meta_title' || element.attr('name') === 'title') && length >= 70)
                element.addClass('error');
            else if((element.attr('name') === 'meta_title' || element.attr('name') === 'title') && length < 70)
                element.removeClass('error');

            if((element.attr('name') === 'meta_description' || element.attr('name') === 'meta_desc') && length >= 135)
                element.addClass('error');
            else if((element.attr('name') === 'meta_description' || element.attr('name') === 'meta_desc') && length < 135)
                element.removeClass('error');
        }

        function compareMeta(element) {
            let val = element.val();
            let meta_title = '';
            let meta_desc = '';
            if(element.attr('name') === 'meta_title')
                meta_title = val;
            else if((element.attr('name') === 'meta_description' || element.attr('name') === 'meta_desc'))
                meta_desc = val;

            $.ajax({
                url: "<?php echo e(url('admin/compareMeta')); ?>",
                method: 'post',
                context: document.body,
                data: {
                    meta_title: meta_title,
                    meta_desc: meta_desc,
                    id: <?php echo e($entry->id?? 0); ?>

                },
            }).done(function(response) {
                if(response)
                    element.addClass('warning');
                else
                    element.removeClass('warning');
            });
        }
    });
</script>

<style>
    label:not(.form-check-label) {
        display: flex;
        align-items: center;
    }
    .chars_count {
        font-weight: 400;
        font-size: 12px;
        margin-left: auto;
    }
    input.error, textarea.error {
        border-color: #ff0000 !important;
    }
    input.warning, textarea.warning {
        background: #ff9191 !important;
    }
</style><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/vendor/backpack/base/inc/scripts.blade.php ENDPATH**/ ?>