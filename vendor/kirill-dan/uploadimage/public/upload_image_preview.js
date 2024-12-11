$(document).ready(function () {

    /**
     * Create preview image after select file.
     *
     * Necessary wrap file input in the following way:
     *
     * <div class="image-preview-block">
     *     <div class="image-preview-image"></div>
     *     {!! Form::file('image', ['class' => 'image-preview-input']) !!}
     * </div>
     */
    $('.image-preview-block').on('change', '.image-preview-input', function () {
        var file = this.files[0];
        if (file != null && file.type.match('image.*')) {
            var reader = new FileReader();

            // Get settings for preview.
            $.ajax({
                data: {'preview': true},
                type: "POST",
                url: "/ajax/uploader/preview",
                cache: false,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                success: function (settings) {
                    console.log(settings);
                    reader.onload = function (e) {
                        var preview = '<img src="' + e.target.result + '" class="img-rounded" style="width: 100%"/>';
                        $('.image-preview-block .image-preview-image').empty().html(preview).css('width', settings.preview_width)
                    };

                    reader.onerror = function (event) {
                        console.log(event.target.error.code);
                    };

                    reader.readAsDataURL(file)
                }
            });
        }
    })
});