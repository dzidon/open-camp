<?php

namespace App\Model\Event\Admin\CampDate;

use App\Library\Data\Admin\CampDateData;
use Symfony\Contracts\EventDispatcher\Event;

class CampDateCreateEvent extends Event
{
    public const NAME = 'model.admin.camp_date.create';

    private CampDateData $data;

    public function __construct(CampDateData $data)
    {
        $this->data = $data;
    }

    public function getCampDateData(): CampDateData
    {
        return $this->data;
    }

    public function setCampDateData(CampDateData $data): self
    {
        $this->data = $data;

        return $this;
    }
}