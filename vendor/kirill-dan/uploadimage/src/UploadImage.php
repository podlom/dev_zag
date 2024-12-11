<?php

/**
 * Class for work with images.
 */

namespace Dan\UploadImage;

use Illuminate\Filesystem\Filesystem as File;
use Spatie\Glide\GlideImage;
use Dan\UploadImage\Exceptions\UploadImageException;

class UploadImage
{
    /**
     * Use thumbnails or not.
     */
    protected $thumbnail_status;

    /**
     * Base store for images.
     */
    protected $baseStore;

    /**
     * Original folder for images.
     */
    protected $original;

    /**
     * Original image will be resizing to 800px.
     */
    protected $originalResize;

    /**
     * Image quality for save image in percent.
     */
    protected $quality;

    /**
     * Width thumbnails for images.
     */
    protected $thumbnails;

    /**
     * Watermark image.
     */
    protected $watermark_path;

    /**
     * Watermark image for video.
     */
    protected $watermark_video_path;

    /**
     * Watermark text.
     */
    protected $watermark_text;

    /**
     * Minimal width for image.
     */
    protected $min_width;

    /**
     * Width for preview image.
     */
    protected $previewWidth;

    /**
     * Folder name for upload images from WYSIWYG editor.
     */
    protected $editor_folder;

    /**
     * Object for work with files.
     */
    public $file;

    /**
     *  Get settings from config file.
     */
    public function __construct($config)
    {
        $this->thumbnail_status = $config['thumbnail_status'];
        $this->baseStore = $config['baseStore'];
        $this->original = $config['original'];
        $this->originalResize = $config['originalResize'];
        $this->quality = $config['quality'];
        $this->thumbnails = $config['thumbnails'];
        $this->watermark_path = $config['watermark_path'];
        $this->watermark_video_path = $config['watermark_video_path'];
        $this->watermark_text = $config['watermark_text'];
        $this->min_width = $config['min_width'];
        $this->previewWidth = $config['previewWidth'];
        $this->editor_folder = $config['editor_folder'];

        $this->file = new File();
    }

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
    public function upload($file, $contentName, $watermark = false, $video = false, $thumbnails = false, $size = false)
    {
        //$thumbnails = $this->thumbnail_status;

        // Create path for storage and full path to image.
        $imageStorage = $this->baseStore . $contentName . 's/';
        // Path to file system.
        $imagePath = public_path() . $imageStorage;

        // If file URL string.
        if (is_string($file) && !empty($file)) {
            $newName = $this->saveLinkImage($file, $contentName);
        }

        // If file from form. Save file to disk.
        if (is_object($file)) {
            $newName = $this->saveFileToDisk($file, $contentName);
        }

        // If file was uploaded then make resize and add watermark.
        if (!isset($newName)) {
            throw new UploadImageException('Can\'t upload image!');
        }

        // If video content then cover image the video player watermark.
        $watermark_array = $this->createWatermarkArray($watermark, $video);

        // Path to file in file system.
        $originalPath = $imagePath . $this->original . $newName;

        // Get image width.
        $image_width = getimagesize($originalPath)[0];

        // If image width more then originalResize - make resize it.
        if ($image_width > $this->originalResize) {
            // Add resize attribute.
            $watermark_array['w'] = $this->originalResize;
        }

        $this->resizeImageAddWatermark($originalPath, $watermark_array);

        // If need make thumbnails.
        if ($thumbnails) {
            // If exist array with size
            if ($size && is_array($size))
            {
                $this->thumbnails = $size;
            }

            // Create thumbnails.
            $this->createThumbnails($imagePath, $originalPath, $newName);
        }

        // Url to image.
        $url = $imageStorage . $this->original . $newName;

        $newImage = new UploadImageGet($newName, $url, $originalPath);

        return $newImage;
    }

    /**
     *  Save image from link.
     *
     * @param $file string link to file
     * @param $contentName string content name (folder name for save)
     *
     * @return string path to file
     * @throws \Dan\UploadImage\Exceptions\UploadImageException
     * @throws \Dan\UploadImage\Exceptions\UploadImageException
     */
    public function saveLinkImage($file, $contentName)
    {
        // Create path for storage and full path to image.
        $imageStorage = $this->baseStore . $contentName . 's/';
        $imagePath = public_path() . $imageStorage;

        $file = trim($file);
        $imageSize = getimagesize($file);

        // Check if image.
        if (!$imageSize) {
            throw new UploadImageException('File should be image format!');
        }

        // If width image < $this->min_width (default 500px).
        if (getimagesize($file)[0] < $this->min_width) {
            throw new UploadImageException('Image should be more then ' . $this->min_width . 'px');
        }

        // Get real image extension.
        $ext = explode('/', $imageSize['mime'])[1];

        // Generate new file name.
        $newName = $this->generateNewName($contentName, $ext);

        // Path to file in file system.
        $originalPath = $imagePath . $this->original . $newName;

        // Get file from URL.
        $file = curl_init($file);

        // Save file to disk.
        $fp = fopen($originalPath, 'wb');
        curl_setopt($file, CURLOPT_FILE, $fp);
        curl_setopt($file, CURLOPT_HEADER, 0);
        curl_setopt($file, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($file);
        curl_close($file);
        fclose($fp);

        return $newName;
    }

    /**
     * Delete image from disk.
     *
     * @param $imageName string image name or array with images
     * @param $contentName string content name (use for folder and name)
     *
     */
    public function delete($imageName, $contentName)
    {
        $thumbnails = $this->thumbnail_status;

        // Create path for storage and full path to image.
        $imageStorage = $this->baseStore . $contentName . 's/';
        $imagePath = public_path() . $imageStorage;

        // Make array for once image.
        if (is_string($imageName)) {
            $imageName = [$imageName];
        }

        // If need delete array of images.
        if (is_array($imageName)) {
            // Delete each image.
            foreach ($imageName as $image) {
                // Delete old original image from disk.
                $this->file->delete($imagePath . $this->original . $image);

                // Delete all thumbnails if exist.
                if ($thumbnails) {
                    $this->deleteThumbnails($imagePath, $image);
                }
            }
        }
    }

    /**
     * Delete body images from disk.
     *
     * @param $textBody string with text where there images
     *
     */
    public function deleteBody($textBody)
    {
        // Get all images from post body.
        $images_body = $this->getImagesFromBody($textBody);

        // Delete body images from disk.
        if (count($images_body) > 0) {
            $this->delete($images_body, $this->editor_folder);
        }
    }

    /**
     * Create path to image.
     *
     * @param $contentName string content name (use for folder and name)
     * @param null $size integer width for image (use one of thumbnail array)
     *
     * @return mixed
     */
    public function load($contentName, $size = null)
    {
        // Create path to original image.
        $imagePath = $this->baseStore . $contentName . 's/' . $this->original;

        // If get size for image.
        if ($size) {
            // Get all thumbnails and compare with size.
            foreach ($this->thumbnails as $width) {
                if ($width == $size) {
                    $imagePath = $this->baseStore . $contentName . 's/w' . $width . '/';

                    return $imagePath;
                }
            }
        }

        return $imagePath;
    }

    /**
     * Convert image to base64 format.
     *
     * @param $image_path_file string path to file in a file system
     *
     * @return string base64 file format
     */
    public static function convertToBase64($image_path_file)
    {
        // Create Base64 image.
        $type = pathinfo($image_path_file, PATHINFO_EXTENSION);
        $data = file_get_contents($image_path_file, FILE_USE_INCLUDE_PATH);
        $dataUri = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $dataUri;
    }

    /**
     * Preview image for form.
     *
     * @param $file object instance image
     * @param $contentName string content name (use for folder and name)
     *
     * @return string new image stream Base64
     */
    public function preview($file, $contentName)
    {
        // Upload image and get path to file.
        $originalPath = $this->upload($file, $contentName)->getImagePath();

        // Convert image to base64 file.
        $image_path_name = UploadImage::convertToBase64($originalPath);

        // Delete original image from disk.
        $this->file->delete($originalPath);

        return $image_path_name;
    }

    /**
     * Get all images from body which keeping on the our server.
     *
     * @param $html string text with relative images links
     *
     * @return array with images
     */
    public function getImagesFromBody($html)
    {
        // Get all images from body.
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $get_body_images = $doc->getElementsByTagName('img');

        $body_images = [];

        foreach ($get_body_images as $body_image) {
            $src = $body_image->getAttribute('src');

            // If this is internal link.
            if (mb_strpos($src, 'http://') === false && mb_strpos($src, 'https://') === false) {
                $body_images[] = last(explode('/', $src));
            }
        }

        return $body_images;
    }

    /**
     * Save file to disk.
     *
     * @param $file object file from input form
     * @param $contentName string Model name
     *
     * @return string path to file with name
     * @throws \Dan\UploadImage\Exceptions\UploadImageException
     * @throws \Dan\UploadImage\Exceptions\UploadImageException
     */
    public function saveFileToDisk($file, $contentName)
    {
        // Create path for storage and full path to image.
        $imageStorage = $this->baseStore . $contentName . 's/';
        $imagePath = public_path() . $imageStorage;

        // Check if image.
        if (!getimagesize($file)) {
            throw new UploadImageException('File should be image format!');
        }

        // Get real path to file.
        $pathToFile = $file->getPathname();

        // Get image size.
        $imageSize = getimagesize($pathToFile);

        // If width image < $this->min_width (default 500px).
        if ($imageSize[0] < $this->min_width) {
            throw new UploadImageException('Image should be more then ' . $this->min_width . 'px');
        }

        // Get real image extension.
        $ext = explode('/', $imageSize['mime'])[1];

        // Generate new file name.
        $newName = $this->generateNewName($contentName, $ext);

        // Save image to disk.
        $file->move($imagePath . $this->original, $newName);

        return $newName;
    }

    /**
     * Generate new name for image.
     *
     * @param $contentName string Model name
     * @param $ext string extension of image file
     *
     * @return string
     */
    public function generateNewName($contentName, $ext)
    {
        $ind = time() . '_' . mb_strtolower(str_random(8));

        // New file name.
        $newName = $contentName . '_' . $ind . '.' . $ext;

        return $newName;
    }

    /**
     * Prepare array for create watermark
     *
     * @param $watermark bool status watermark
     * @param $video bool status for video player image
     *
     * @return array with watermark data
     */
    public function createWatermarkArray($watermark, $video)
    {
        // Create empty array.
        $watermark_array = [];

        // If video content then cover image the video player watermark.
        if ($video) {
            // Create array with watermark data.
            $watermark_array = [
                'mark' => public_path() . $this->watermark_video_path,
                'markpad' => 0,
                'markpos' => 'center'
            ];
        }

        // If not video content and need add watermark.
        if (!$video && $watermark) {
            // Create array with watermark data.
            $watermark_array = [
                'mark' => public_path() . $this->watermark_path,
                'markpad' => 5,
                'markpos' => 'bottom-right'
            ];
        }

        return $watermark_array;
    }

    /**
     * Resize image, change quality and add watermark
     *
     * @param $originalPath string path to image on the disk
     * @param $watermark_array array with attributes
     */
    public function resizeImageAddWatermark($originalPath, $watermark_array)
    {
        // Resize saved image and save to original folder
        // (help about attributes http://glide.thephpleague.com/1.0/api/quick-reference/).
        GlideImage::create($originalPath)
            ->modify(array_merge([
                'w' => $this->originalResize,
                'q' => $this->quality
            ], $watermark_array))
            ->save($originalPath);
    }

    /**
     * Create thumbnails.
     *
     * @param $imagePath string path to image folder
     * @param $originalPath string path to image folder with file name
     * @param $newName string image name
     */
    public function createThumbnails($imagePath, $originalPath, $newName)
    {
        // Get all thumbnails and save it.
        foreach ($this->thumbnails as $width) {
            // Path to folder where will be save image.
            $savedImagePath = $imagePath . 'w' . $width . '/';

            // File with path to save image.
            $savedImagePathFile = $savedImagePath . $newName;

            // Create new folder.
            $this->file->makeDirectory($savedImagePath, $mode = 0755, true, true);

            // Resize saved image and save to thumbnail folder
            // (help about attributes http://glide.thephpleague.com/1.0/api/quick-reference/).
            GlideImage::create($originalPath)
                ->modify(['w' => $width])
                ->save($savedImagePathFile);
        }
    }

    /**
     * Delete Thumbnails from disk.
     *
     * @param $imagePath string path to image on the disk
     * @param $imageName string image name
     */
    public function deleteThumbnails($imagePath, $imageName)
    {
        // Get all thumbnails and delete it.
        foreach ($this->thumbnails as $width) {
            // Delete old image from disk.
            $this->file->delete($imagePath . 'w' . $width . '/' . $imageName);
        }
    }
}
