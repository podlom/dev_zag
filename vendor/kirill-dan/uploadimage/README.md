# UploadImage v2.0.1

For Laravel 5.3 / 5.4 / 5.5

* [Demo](https://yousuper.org/)
* [Site author](https://cleverman.org/)
* [Rus article](https://cleverman.org/post/37) 

This package give you next opportunities:
 * Easy upload image file to the content type folder
 * Create thumbnails for your image
 * You can easily load image for any content type from disk (original or thumbnail image)
 * You can easily delete your image from disk
 * You can easily delete all images from your body text where images with relative links
 * You can easily creating preview image without storage image on the disk 
 * You can use ajax for easily upload/delete image in your WYSIWYG editor
 * You can easily add a watermark on your images 
 * You can storing your images on the disk or in the DB in the Base64 format
 
 ## History:
 * v2.0.1 -  Thumbnails can assign in method: upload($file, $contentName, $watermark = false, $video = false, $thumbnails = false)
 * v2.0 -    Support Laravel 5.5
 * v1.0.70 - Add to config 'watermarkEditorStatus' for WYSIWYG editors. Change UploadImageController
 * v1.0.61 - Get real image extension.
 * v1.0.50 - You can disable or enable watermark. See example below.
 * v1.0.5 - Fix check file on image. 
 * v1.0.4 - Refactoring all code. Add exceptions, change arrays to methods. Fix some bugs. 
 * v1.0.3 - Fix some bugs.
 * v1.0.2 - replace functional for show image preview in form,
            method upload return error with status if image width < image width in settings file
 * v1.0.1 - fix some bugs
 
## Requirements
 
 * spatie/laravel-glide
 * php gd library

## Install package

### Add package to your project:
```
composer require kirill-dan/uploadimage 2.*
```

### Add to file config/app.php next entries:

Section Providers:
```php
'providers' => [
    .......
    Spatie\Glide\GlideServiceProvider::class,
    Dan\UploadImage\UploadImageServiceProvider::class,
]
```

Section Facades:
```php
'aliases' => [
    .......
    'GlideImage' => Spatie\Glide\GlideImageFacade::class,
    'UploadImage' => Dan\UploadImage\UploadImageFacade::class,
]
```

Enter next command to a terminal:
```
php artisan vendor:publish --provider="Dan\UploadImage\UploadImageServiceProvider"
```

Will be copied two files:

config/upload-image.php - this is settings for UploadImage package

resources/assets/js/upload_image_preview.js - you should include this file to elixir/mix:

Open file webpack.mix.js and add upload_image_preview.js to the array. For example:
```php
mix.sass('resources/assets/sass/app.scss', 'public/css/app.css').version();

mix.js(['resources/assets/js/app.js', 'resources/assets/js/upload_image_preview.js'],
    'public/js/all.js').version();
```

After execute command in terminal (for production): 
```
npm run production
```

For Controller where you want use UploadImage add namespace Facade:
```php
use UploadImage;
```

If you want see preview image after selected image in the file input field then wrap your file input field in the next code:
```
 <div class="image-preview-block">
     <div class="image-preview-image"></div>
     {!! Form::file('image', ['class' => 'image-preview-input']) !!}
 </div>
```

## You can use next methods:

**Upload you image to disk from file form:**
```php
/**
 * Upload image to disk.
 *
 * @param $file object instance image or image string
 * @param $contentName string content name (use for create and named folder)
 * @param bool $watermark bool watermark status (by default = false)
 * @param bool $video if true then add watermark with video player image to an image
 * @param bool $thumbnails create thumbnails for original image
 *
 * @return object image
 * @throws UploadImageException
 */
UploadImage::upload($file, $contentName, $watermark = false, $video = false, $thumbnails = false)
```

For example:
Add to your controller 
```
use UploadImage;
use Dan\UploadImage\Exceptions\UploadImageException;
```

```php
$file = $request->file('image');

$video = $rubric->name == 'Video' ? true : false;
$watermark = true;
$thumbnail = true;

// Upload and save image.
try {
    // Upload and save image.
    $input['image'] = UploadImage::upload($file, 'post', $watermark, $video, $thumbnail)->getImageName();
} catch (UploadImageException $e) {

    return back()->withInput()->withErrors(['image', $e->getMessage()]);
}

```

### Delete your image from disk:
```php
/**
 * Delete image from disk.
 *
 * @param $imageName string image name or array with images
 * @param $contentName string content name (use for folder and name)
 *
 */
UploadImage::delete($imageName, $contentName);
```

For example:
```php
// Delete old image.
UploadImage::delete($post->image, 'post');
```

### Delete your image from body text:
```php
/**
 * Delete body images from disk.
 *
 * @param $textBody string with text where there images
 *
 */
UploadImage::deleteBody($textBody);
```

For example:
```php
// Delete all images from post body (added in editor).
UploadImage::deleteBody($post->body);
```

### Load your image from disk:
```php
/**
 * Create path to image.
 *
 * @param $contentName string content name (use for folder and name)
 * @param null $size integer width for image (use one of thumbnail array)
 *
 * @return mixed
 */
UploadImage::load($contentName, $size = null);
```

For example:
```php
// Give all data to template.
return view('posts.index', [
    'posts' => $posts,
    'path' => UploadImage::load('post')
]);
```

### Get preview image in Base64 format:
```php
/**
 * Preview image for form.
 *
 * @param $file object instance image
 * @param $contentName string content name (use for folder and name)
 *
 * @return array new image stream Base64
 */
UploadImage::preview($file, $contentName);
```

For example:
```php
// Get image preview.
$image_url = UploadImage::preview($file, 'collage_image');
```

## You can use next routes:

```
// Save image from WYSIWYG editor.
ajax/uploader/upload

// Delete image from WYSIWYG editor.
ajax/uploader/delete

// Create preview image for form (use wraper, see above).
ajax/uploader/preview
```

For example:
```js
// Upload files on the server and get url images.
function uploadFile(filesForm) {
    data = new FormData();

    // Add all files from form to array.
    for (var i = 0; i < filesForm.length; i++) {
        data.append("files[]", filesForm[i]);
    }

    $.ajax({
        data: data,
        type: "POST",
        url: "/ajax/uploader/upload",
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        contentType: false,
        processData: false,
        success: function (images) {

            // Get all images and insert to editor.
            for (var i = 0; i < images['url'].length; i++) {

                // Insert image into summernote.
                editor.summernote('insertImage', images['url'][i], function ($image) {
                    //$image.css('width', $image.width() / 3);
                    //$image.attr('data-filename', 'retriever')
                });
            }
        }
    });
}

// Delete file from the server.
function deleteFile(file) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: "/ajax/uploader/delete",
        cache: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        contentType: false,
        processData: false,
        success: function (image) {
            //console.log(image);
        }
    });
}
```