<?php

namespace App\Model\Library\Camp;

/**
 * Contains lifespans of camps.
 */
interface CampLifespanCollectionInterface
{
    /**
     * Gets lifespan of a camp by its id.
     *
     * @param string $campId
     * @return CampLifespanInterface|null
     */
    public function getCampLifespan(string $campId): ?CampLifespanInterface;

    /**
     * @return array
     */
    public function getCampLifespans(): array;

    /**
     * @param string $campId
     * @param CampLifespanInterface $campLifespan
     * @return $this
     */
    public function addCampLifespan(string $campId, CampLifespanInterface $campLifespan): self;

    /**
     * @param string|CampLifespanInterface $campLifespan
     * @return $this
     */
    public function removeCampLifespan(string|CampLifespanInterface $campLifespan): self;
}