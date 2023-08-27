<?php

namespace App\Model\Module\CampCatalog\Camp;

use DateTimeInterface;

/**
 * @inheritDoc
 */
class CampLifespan implements CampLifespanInterface
{
    private ?DateTimeInterface $startAt;

    private ?DateTimeInterface $endAt;

    public function __construct(?DateTimeInterface $startAt, ?DateTimeInterface $endAt)
    {
        $this->startAt = $startAt;
        $this->endAt = $endAt;
    }

    /**
     * @inheritDoc
     */
    public function getStartAt(): ?DateTimeInterface
    {
        return $this->startAt;
    }

    /**
     * @inheritDoc
     */
    public function getEndAt(): ?DateTimeInterface
    {
        return $this->endAt;
    }
}