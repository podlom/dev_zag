<?php

declare(strict_types=1);


namespace App\Sitemap;

use Spatie\Sitemap\Tags\Url;

class CustomUrl extends Url
{
    protected $images = [];

    public function addImage($imageUrl)
    {
        $this->images[] = $imageUrl;
        return $this;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['images'] = $this->images;
        return $data;
    }

    public function getImagesXml(): string
    {
        $imagesXml = '';

        foreach ($this->images as $image) {
            $imagesXml .= "<image:image><image:loc>{$image}</image:loc></image:image>";
        }

        return $imagesXml;
    }

    public function __toString(): string
    {
        $xml = parent::__toString();

        $imagesXml = $this->getImagesXml();

        // Insert images XML before the closing </url> tag
        return str_replace('</url>', $imagesXml . '</url>', $xml);
    }
}
