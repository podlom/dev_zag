<?php

// Configuration UploadImage package.

return [
    'image-settings' => [

        // Use thumbnails or not.
        'thumbnail_status' => false,

        // Base store for images.
        'baseStore' => '/images/uploads/',

        // Original folder for images.
        'original' => 'original/',

        // Original image will be resizing to 800px.
        'originalResize' => 800,

        // Image quality for save image in percent.
        'quality' => 80,

        // Array with width thumbnails for images.
        'thumbnails' => ['200', '400'],

        // Watermark image status for WYSIWYG editor (default disable).
        'watermarkEditorStatus' => false,

        // Watermark image.
        'watermark_path' => '/images/design/watermark.png',

        // Watermark image.
        'watermark_video_path' => '/images/design/logo_player.png',

        // Watermark text.
        'watermark_text' => 'CleverMan.org',

        // Minimal width for uploading image.
        'min_width' => 500,

        // Width for preview image.
        'previewWidth' => 200,

        // Folder name for upload images from WYSIWYG editor.
        'editor_folder' => 'editor_post',
    ]
];