<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\DiscountConfigIntegerIntervals;
use App\Library\Constraint\UniqueDiscountConfig;
use App\Model\Entity\DiscountConfig;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueDiscountConfig]
class DiscountConfigData
{
    private ?DiscountConfig $discountConfig;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    /** @var DiscountRecurringCamperConfigData[] */
    #[Assert\Valid]
    #[DiscountConfigIntegerIntervals]
    private array $discountRecurringCamperConfigsData = [];

    /** @var DiscountSiblingConfigData[] */
    #[Assert\Valid]
    #[DiscountConfigIntegerIntervals]
    private array $discountSiblingConfigsData = [];

    public function __construct(?DiscountConfig $discountConfig = null)
    {
        $this->discountConfig = $discountConfig;
    }

    public function getDiscountConfig(): ?DiscountConfig
    {
        return $this->discountConfig;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDiscountRecurringCamperConfigsData(): array
    {
        return $this->discountRecurringCamperConfigsData;
    }

    public function setDiscountRecurringCamperConfigsData(array $discountRecurringCamperConfigsData): self
    {
        foreach ($discountRecurringCamperConfigsData as $discountRecurringCamperConfigData)
        {
            if (!$discountRecurringCamperConfigData instanceof DiscountRecurringCamperConfigData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, DiscountRecurringCamperConfigData::class)
                );
            }
        }

        $this->discountRecurringCamperConfigsData = $discountRecurringCamperConfigsData;

        return $this;
    }

    public function addDiscountRecurringCamperConfigData(DiscountRecurringCamperConfigData $discountRecurringCamperConfigData): self
    {
        if (in_array($discountRecurringCamperConfigData, $this->discountRecurringCamperConfigsData, true))
        {
            return $this;
        }

        $this->discountRecurringCamperConfigsData[] = $discountRecurringCamperConfigData;

        return $this;
    }

    public function removeDiscountRecurringCamperConfigData(DiscountRecurringCamperConfigData $discountRecurringCamperConfigData): self
    {
        $key = array_search($discountRecurringCamperConfigData, $this->discountRecurringCamperConfigsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->discountRecurringCamperConfigsData[$key]);

        return $this;
    }

    public function getDiscountSiblingConfigsData(): array
    {
        return $this->discountSiblingConfigsData;
    }

    public function setDiscountSiblingConfigsData(array $discountSiblingConfigsData): self
    {
        foreach ($discountSiblingConfigsData as $discountSiblingConfigData)
        {
            if (!$discountSiblingConfigData instanceof DiscountSiblingConfigData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, DiscountSiblingConfigData::class)
                );
            }
        }

        $this->discountSiblingConfigsData = $discountSiblingConfigsData;

        return $this;
    }

    public function addDiscountSiblingConfigData(DiscountSiblingConfigData $discountSiblingConfigData): self
    {
        if (in_array($discountSiblingConfigData, $this->discountSiblingConfigsData, true))
        {
            return $this;
        }

        $this->discountSiblingConfigsData[] = $discountSiblingConfigData;

        return $this;
    }

    public function removeDiscountSiblingConfigData(DiscountSiblingConfigData $discountSiblingConfigData): self
    {
        $key = array_search($discountSiblingConfigData, $this->discountSiblingConfigsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->discountSiblingConfigsData[$key]);

        return $this;
    }
}