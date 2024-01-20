<?php

namespace App\Model\Event\Admin\DiscountConfig;

use App\Library\Data\Admin\DiscountConfigData;
use App\Model\Entity\DiscountConfig;
use App\Model\Event\AbstractModelEvent;

class DiscountConfigCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.discount_config.create';

    private DiscountConfigData $data;

    private ?DiscountConfig $DiscountConfig = null;

    public function __construct(DiscountConfigData $data)
    {
        $this->data = $data;
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

    public function getDiscountConfig(): ?DiscountConfig
    {
        return $this->DiscountConfig;
    }

    public function setDiscountConfig(?DiscountConfig $DiscountConfig): self
    {
        $this->DiscountConfig = $DiscountConfig;

        return $this;
    }
}