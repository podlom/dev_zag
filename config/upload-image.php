<?php

// Configuration UploadImage package.

return [
    'image-settings' => [

        // Use thumbnails or not.
        'thumbnail_status' => false,

        // Base store for images.
        'baseStore' => '/upload',

        // Original folder for images.
        'original' => 'new_images_3/',

        // Original image will be resizing to 800px.
        'originalResize' => 1000,

        // Image quality for save image in percent.
        'quality' => 80,

        // Array with width thumbnails for images.
        'thumbnails' => ['350', '600'],

        // Watermark image status for WYSIWYG editor (default disable).
        'watermarkEditorStatus' => false,

        // Watermark image.
        'watermark_path' => '/images/design/watermark.png',

        // Watermark image.
        'watermark_video_path' => '/images/design/logo_player.png',

        // Watermark text.
        'watermark_text' => 'CleverMan.org',

        // Minimal width for uploading image.
        'min_width' => 0,

        // Width for preview image.
        'previewWidth' => 200,

        // Folder name for upload images from WYSIWYG editor.
        'editor_folder' => '',
    ]
];