<?php

namespace App\Model\Event\Admin\DiscountConfig;

use App\Library\Data\Admin\DiscountConfigData;
use App\Model\Entity\DiscountConfig;
use App\Model\Event\AbstractModelEvent;

class DiscountConfigUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.discount_config.update';

    private DiscountConfigData $data;

    private DiscountConfig $entity;

    public function __construct(DiscountConfigData $data, DiscountConfig $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getDiscountConfigData(): DiscountConfigData
    {
        return $this->data;
    }

    public function setDiscountConfigData(DiscountConfigData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getDiscountConfig(): DiscountConfig
    {
        return $this->entity;
    }

    public function setDiscountConfig(DiscountConfig $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}