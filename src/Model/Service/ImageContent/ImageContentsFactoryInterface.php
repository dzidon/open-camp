<?php

namespace App\Model\Service\ImageContent;

use App\Model\Entity\ImageContent;

/**
 * Creates new image contents.
 */
interface ImageContentsFactoryInterface
{
    /**
     * Creates new image contents.
     *
     * @return ImageContent[]
     */
    public function createImageContents(): array;
}