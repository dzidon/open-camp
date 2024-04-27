<?php

namespace App\Model\Event\Admin\Page;

use App\Model\Entity\Page;
use App\Model\Event\AbstractModelEvent;

class PageDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.page.delete';

    private Page $entity;

    public function __construct(Page $entity)
    {
        $this->entity = $entity;
    }

    public function getPage(): Page
    {
        return $this->entity;
    }

    public function setPage(Page $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}