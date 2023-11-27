<?php

namespace App\Model\Library\Camp;

/**
 * @inheritDoc
 */
class CampLifespanCollection implements CampLifespanCollectionInterface
{
    /**
     * @var CampLifespanInterface[]
     */
    private array $campLifespans = [];

    /**
     * @inheritDoc
     */
    public function getCampLifespan(string $campId): ?CampLifespanInterface
    {
        if (!array_key_exists($campId, $this->campLifespans))
        {
            return null;
        }

        return $this->campLifespans[$campId];
    }

    /**
     * @inheritDoc
     */
    public function getCampLifespans(): array
    {
        return $this->campLifespans;
    }

    /**
     * @inheritDoc
     */
    public function addCampLifespan(string $campId, CampLifespanInterface $campLifespan): self
    {
        $this->campLifespans[$campId] = $campLifespan;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeCampLifespan(string|CampLifespanInterface $campLifespan): self
    {
        if ($campLifespan instanceof CampLifespanInterface)
        {
            $key = array_search($campLifespan, $this->campLifespans, true);

            if ($key === false)
            {
                return $this;
            }
        }
        else
        {
            $key = $campLifespan;

            if (!array_key_exists($key, $this->campLifespans))
            {
                return $this;
            }
        }

        unset($this->campLifespans[$key]);

        return $this;
    }
}