<?php

namespace App\Model\Event\Admin\DiscountConfig;

use App\Model\Entity\DiscountConfig;
use App\Model\Event\AbstractModelEvent;

class DiscountConfigDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.discount_config.delete';

    private DiscountConfig $entity;

    public function __construct(DiscountConfig $entity)
    {
        $this->entity = $entity;
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