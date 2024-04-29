<?php

namespace App\Model\Event\Admin\TextContent;

use App\Model\Entity\TextContent;
use App\Model\Event\AbstractModelEvent;
use LogicException;

class TextContentsCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.text_contents.create';

    /** @var TextContent[] */
    private array $textContents = [];

    public function getTextContents(): array
    {
        return $this->textContents;
    }

    public function setTextContents(array $textContents): self
    {
        foreach ($textContents as $textContent)
        {
            if (!$textContent instanceof TextContent)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, TextContent::class)
                );
            }
        }

        $this->textContents = $textContents;

        return $this;
    }
}