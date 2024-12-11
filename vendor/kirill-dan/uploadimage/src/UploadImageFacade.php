<?php

namespace Dan\UploadImage;

use Illuminate\Support\Facades\Facade;

class UploadImageFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor()
    {
        return 'upload-image';
    }
}