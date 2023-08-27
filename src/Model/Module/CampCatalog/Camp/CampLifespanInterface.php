<?php

namespace App\Model\Module\CampCatalog\Camp;

use DateTimeInterface;

/**
 * Contains the lowest start date time and the highest end date time of a camp.
 */
interface CampLifespanInterface
{
    /**
     * Returns the lowest start date time of a camp.
     *
     * @return DateTimeInterface|null
     */
    public function getStartAt(): ?DateTimeInterface;

    /**
     * Returns the highest end date time of a camp.
     *
     * @return DateTimeInterface|null
     */
    public function getEndAt(): ?DateTimeInterface;
}