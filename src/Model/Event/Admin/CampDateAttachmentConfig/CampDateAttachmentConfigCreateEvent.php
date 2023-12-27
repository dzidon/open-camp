<?php

namespace App\Model\Event\Admin\CampDateAttachmentConfig;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateAttachmentConfig;
use App\Model\Event\AbstractModelEvent;

class CampDateAttachmentConfigCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_attachment_config.create';

    private CampDateAttachmentConfigData $data;

    private CampDate $campDate;

    private ?CampDateAttachmentConfig $entity = null;

    public function __construct(CampDateAttachmentConfigData $data, CampDate $campDate)
    {
        $this->data = $data;
        $this->campDate = $campDate;
    }

    public function getCampDateAttachmentConfigData(): CampDateAttachmentConfigData
    {
        return $this->data;
    }

    public function setCampDateAttachmentConfigData(CampDateAttachmentConfigData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCampDate(): CampDate
    {
        return $this->campDate;
    }

    public function setCampDate(CampDate $campDate): self
    {
        $this->campDate = $campDate;

        return $this;
    }

    public function getCampDateAttachmentConfig(): ?CampDateAttachmentConfig
    {
        return $this->entity;
    }

    public function setCampDateAttachmentConfig(?CampDateAttachmentConfig $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}