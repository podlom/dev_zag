<?php

/**
 * Controller for upload images from WYSIWYG and Delete image from WYSIWYG.
 */

namespace Dan\UploadImage\Controllers;

use Dan\UploadImage\UploadImageFacade as UploadImage;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Dan\UploadImage\Exceptions\UploadImageException;

class UploadImageController extends Controller
{
    /**
     * Folder name for upload images from WYSIWYG editor.
     */
    protected $editor_folder;

    /**
     * Width for preview image.
     */
    protected $previewWidth;

    /**
     * Watermark image status for WYSIWYG editor (default disable).
     */
    protected $watermarkEditorStatus;

    // Get settings from config file.
    public function __construct()
    {
        $config = \Config::get('upload-image.image-settings');

        $this->editor_folder = $config['editor_folder'];
        $this->previewWidth = $config['previewWidth'];
        $this->watermarkEditorStatus = $config['watermarkEditorStatus'];
    }

    /**
     * Upload file to server.
     */
    public function upload(Request $request)
    {
        // Check exist file (files or link).
        if (!$request->file('files') && !$request->get('image')) {
            return response()->json(['status' => 500]);
        }

        // If array with files.
        if ($request->file('files')) {
            $files = $request->file('files');
        }

        // If link to file.
        if ($request->get('image')) {
            // Get file from url.
            $files[] = $request->get('image');
        }

        // If file is array with many files.
        if (!is_array($files)) {
            return response()->json(['status' => 500]);
        }

        $images = [];
        $errors = [];

        // Get every file and upload it.
        foreach ($files as $file) {
            try {
                // Upload and save image.
                $savedImage = UploadImage::upload($file, $this->editor_folder, $this->watermarkEditorStatus);

                // Get only image url.
                $images[] = $savedImage->getImageUrl();
            } catch (UploadImageException $e) {
                $errors[] = $e->getMessage();
            }
        }

        return response()->json(['url' => $images, 'error' => $errors]);
    }

    /**
     * Delete file from server.
     */
    public function delete(Request $request)
    {
        // Check exist file.
        if ($request->get('file')) {
            $image_name = explode('/', $request->get('file'));

            // Delete image from server.
            UploadImage::delete(last($image_name), $this->editor_folder);
        }

        return response()->json(['status' => 200]);
    }

    /**
     * Create preview image.
     */
    public function preview(Request $request)
    {
        // Check exist preview request.
        if ($request->get('preview')) {
            return response()->json(['preview_width' => $this->previewWidth]);
        }

        return response()->json(['status' => 500]);
    }
}
