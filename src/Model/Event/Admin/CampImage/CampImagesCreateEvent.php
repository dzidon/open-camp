<?php

namespace App\Model\Event\Admin\CampImage;

use App\Library\Data\Admin\CampImagesUploadData;
use Symfony\Contracts\EventDispatcher\Event;

class CampImagesCreateEvent extends Event
{
    public const NAME = 'model.admin.camp_image.create';

    private CampImagesUploadData $data;

    public function __construct(CampImagesUploadData $data)
    {
        $this->data = $data;
    }

    public function getCampImagesUploadData(): CampImagesUploadData
    {
        return $this->data;
    }

    public function setCampImagesUploadData(CampImagesUploadData $data): self
    {
        $this->data = $data;

        return $this;
    }
}