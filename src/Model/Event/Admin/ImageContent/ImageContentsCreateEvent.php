<?php

namespace App\Model\Event\Admin\ImageContent;

use App\Model\Entity\ImageContent;
use App\Model\Event\AbstractModelEvent;
use LogicException;

class ImageContentsCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.image_contents.create';

    /** @var ImageContent[] */
    private array $imageContents = [];

    public function getImageContents(): array
    {
        return $this->imageContents;
    }

    public function setImageContents(array $imageContents): self
    {
        foreach ($imageContents as $imageContent)
        {
            if (!$imageContent instanceof ImageContent)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, ImageContent::class)
                );
            }
        }

        $this->imageContents = $imageContents;

        return $this;
    }
}