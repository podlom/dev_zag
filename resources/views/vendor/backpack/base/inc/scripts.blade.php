@if (config('backpack.base.scripts') && count(config('backpack.base.scripts')))
    @foreach (config('backpack.base.scripts') as $path)
    <script type="text/javascript" src="{{ asset($path).'?v='.config('backpack.base.cachebusting_string') }}"></script>
    @endforeach
@endif

@if (config('backpack.base.mix_scripts') && count(config('backpack.base.mix_scripts')))
    @foreach (config('backpack.base.mix_scripts') as $path => $manifest)
    <script type="text/javascript" src="{{ mix($path, $manifest) }}"></script>
    @endforeach
@endif

@include('backpack::inc.alerts')

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

    {{-- Enable deep link to tab --}}
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
                url: "{{ url('admin/compareMeta') }}",
                method: 'post',
                context: document.body,
                data: {
                    meta_title: meta_title,
                    meta_desc: meta_desc,
                    id: {{ $entry->id?? 0 }}
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
</style>