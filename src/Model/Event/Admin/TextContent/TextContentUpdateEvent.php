<?php

namespace App\Model\Event\Admin\TextContent;

use App\Library\Data\Admin\TextContentData;
use App\Model\Entity\TextContent;
use App\Model\Event\AbstractModelEvent;

class TextContentUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.text_content.update';

    private TextContentData $data;

    private TextContent $entity;

    public function __construct(TextContentData $data, TextContent $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getTextContentData(): TextContentData
    {
        return $this->data;
    }

    public function setTextContentData(TextContentData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getTextContent(): TextContent
    {
        return $this->entity;
    }

    public function setTextContent(TextContent $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}