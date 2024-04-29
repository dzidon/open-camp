<?php

namespace App\Model\Service\TextContent;

use App\Model\Entity\TextContent;

/**
 * Creates new text contents.
 */
interface TextContentsFactoryInterface
{
    /**
     * Creates new text contents.
     *
     * @return TextContent[]
     */
    public function createTextContents(): array;
}