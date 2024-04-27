<?php

namespace App\Model\Event\Admin\Page;

use App\Library\Data\Admin\PageData;
use App\Model\Entity\Page;
use App\Model\Event\AbstractModelEvent;

class PageCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.page.create';

    private PageData $data;

    private ?Page $entity = null;

    public function __construct(PageData $data)
    {
        $this->data = $data;
    }

    public function getPageData(): PageData
    {
        return $this->data;
    }

    public function setPageData(PageData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->entity;
    }

    public function setPage(?Page $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}