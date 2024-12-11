<?php

namespace Aimix\Banner\app\Observers;

use Aimix\Banner\app\Models\Banner;

class BannerObserver
{
    public function deleted(Banner $banner)
    {
        $banner->translations()->delete();
    }
}
