<?php

namespace App\Model\Event\Admin\Page;

use App\Library\Data\Admin\PageData;
use App\Model\Entity\Page;
use App\Model\Event\AbstractModelEvent;

class PageUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.page.update';

    private PageData $data;

    private Page $entity;

    public function __construct(PageData $data, Page $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
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