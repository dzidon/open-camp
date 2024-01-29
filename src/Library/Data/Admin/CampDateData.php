<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\CampDateInterval;
use App\Library\Constraint\CampDateUserUnique;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\DiscountConfig;
use App\Model\Entity\TripLocationPath;
use DateTimeImmutable;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

#[CampDateInterval]
class CampDateData
{
    private ?CampDate $campDate;

    private Camp $camp;

    #[Assert\NotBlank]
    private ?DateTimeImmutable $startAt = null;

    #[Assert\GreaterThanOrEqual(propertyPath: 'startAt')]
    #[Assert\NotBlank]
    private ?DateTimeImmutable $endAt = null;

    #[Assert\GreaterThanOrEqual(0.0)]
    #[Assert\NotBlank]
    private ?float $deposit = null;

    #[Assert\When(
        expression: 'this.getDeposit() !== null && this.getDeposit() > 0.0',
        constraints: [
            new Assert\NotBlank(),
        ],
    )]
    private ?DateTimeImmutable $depositUntil = null;

    #[Assert\GreaterThanOrEqual(0.0)]
    #[Assert\NotBlank]
    private ?float $priceWithoutDeposit = null;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\NotBlank]
    private ?int $capacity = null;

    private bool $isOpenAboveCapacity = false;

    private bool $isClosed = false;

    private bool $isHidden = false;

    #[Assert\Length(max: 2000)]
    private ?string $description = null;

    private ?DiscountConfig $discountConfig = null;

    private ?TripLocationPath $tripLocationPathThere = null;

    private ?TripLocationPath $tripLocationPathBack = null;

    /**
     * @var CampDateFormFieldData[]
     */
    #[Assert\Valid]
    private array $campDateFormFieldsData = [];

    /**
     * @var CampDateAttachmentConfigData[]
     */
    #[Assert\Valid]
    private array $campDateAttachmentConfigsData = [];

    /**
     * @var CampDatePurchasableItemData[]
     */
    #[Assert\Valid]
    private array $campDatePurchasableItemsData = [];

    /**
     * @var CampDateUserData[]
     */
    #[Assert\Valid]
    #[CampDateUserUnique]
    private array $campDateUsersData = [];

    public function __construct(Camp $camp, ?CampDate $campDate = null)
    {
        $this->camp = $camp;
        $this->campDate = $campDate;
    }

    public function getCampDate(): ?CampDate
    {
        return $this->campDate;
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }

    public function getStartAt(): ?DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getDeposit(): ?float
    {
        return $this->deposit;
    }

    public function setDeposit(?float $deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }

    public function getDepositUntil(): ?DateTimeImmutable
    {
        return $this->depositUntil;
    }

    public function setDepositUntil(?DateTimeImmutable $depositUntil): self
    {
        $this->depositUntil = $depositUntil;

        return $this;
    }

    public function getPriceWithoutDeposit(): ?float
    {
        return $this->priceWithoutDeposit;
    }

    public function setPriceWithoutDeposit(?float $priceWithoutDeposit): self
    {
        $this->priceWithoutDeposit = $priceWithoutDeposit;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function isOpenAboveCapacity(): bool
    {
        return $this->isOpenAboveCapacity;
    }

    public function setIsOpenAboveCapacity(bool $isOpenAboveCapacity): self
    {
        $this->isOpenAboveCapacity = $isOpenAboveCapacity;

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDiscountConfig(): ?DiscountConfig
    {
        return $this->discountConfig;
    }

    public function setDiscountConfig(?DiscountConfig $discountConfig): self
    {
        $this->discountConfig = $discountConfig;

        return $this;
    }

    public function getTripLocationPathThere(): ?TripLocationPath
    {
        return $this->tripLocationPathThere;
    }

    public function setTripLocationPathThere(?TripLocationPath $tripLocationPathThere): self
    {
        $this->tripLocationPathThere = $tripLocationPathThere;

        return $this;
    }

    public function getTripLocationPathBack(): ?TripLocationPath
    {
        return $this->tripLocationPathBack;
    }

    public function setTripLocationPathBack(?TripLocationPath $tripLocationPathBack): self
    {
        $this->tripLocationPathBack = $tripLocationPathBack;

        return $this;
    }

    public function getCampDateFormFieldsData(): array
    {
        return $this->campDateFormFieldsData;
    }

    public function setCampDateFormFieldsData(array $campDateFormFieldsData): self
    {
        foreach ($campDateFormFieldsData as $campDateFormFieldData)
        {
            if (!$campDateFormFieldData instanceof CampDateFormFieldData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, CampDateFormFieldData::class)
                );
            }
        }

        $this->campDateFormFieldsData = $campDateFormFieldsData;

        return $this;
    }
    
    public function addCampDateFormFieldData(CampDateFormFieldData $campDateFormFieldData): self
    {
        if (in_array($campDateFormFieldData, $this->campDateFormFieldsData, true))
        {
            return $this;
        }

        $this->campDateFormFieldsData[] = $campDateFormFieldData;

        return $this;
    }

    public function removeCampDateFormFieldData(CampDateFormFieldData $campDateFormFieldData): self
    {
        $key = array_search($campDateFormFieldData, $this->campDateFormFieldsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->campDateFormFieldsData[$key]);

        return $this;
    }

    public function getCampDateAttachmentConfigsData(): array
    {
        return $this->campDateAttachmentConfigsData;
    }

    public function setCampDateAttachmentConfigsData(array $campDateAttachmentConfigsData): self
    {
        foreach ($campDateAttachmentConfigsData as $campDateAttachmentConfigData)
        {
            if (!$campDateAttachmentConfigData instanceof CampDateAttachmentConfigData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, CampDateAttachmentConfigData::class)
                );
            }
        }

        $this->campDateAttachmentConfigsData = $campDateAttachmentConfigsData;

        return $this;
    }
    
    public function addCampDateAttachmentConfigData(CampDateAttachmentConfigData $campDateAttachmentConfigData): self
    {
        if (in_array($campDateAttachmentConfigData, $this->campDateAttachmentConfigsData, true))
        {
            return $this;
        }

        $this->campDateAttachmentConfigsData[] = $campDateAttachmentConfigData;

        return $this;
    }

    public function removeCampDateAttachmentConfigData(CampDateAttachmentConfigData $campDateAttachmentConfigData): self
    {
        $key = array_search($campDateAttachmentConfigData, $this->campDateAttachmentConfigsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->campDateAttachmentConfigsData[$key]);

        return $this;
    }

    public function getCampDatePurchasableItemsData(): array
    {
        return $this->campDatePurchasableItemsData;
    }

    public function setCampDatePurchasableItemsData(array $campDatePurchasableItemsData): self
    {
        foreach ($campDatePurchasableItemsData as $campDatePurchasableItemData)
        {
            if (!$campDatePurchasableItemData instanceof CampDatePurchasableItemData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, CampDatePurchasableItemData::class)
                );
            }
        }

        $this->campDatePurchasableItemsData = $campDatePurchasableItemsData;

        return $this;
    }
    
    public function addCampDatePurchasableItemData(CampDatePurchasableItemData $campDatePurchasableItemData): self
    {
        if (in_array($campDatePurchasableItemData, $this->campDatePurchasableItemsData, true))
        {
            return $this;
        }

        $this->campDatePurchasableItemsData[] = $campDatePurchasableItemData;

        return $this;
    }

    public function removeCampDatePurchasableItemData(CampDatePurchasableItemData $campDatePurchasableItemData): self
    {
        $key = array_search($campDatePurchasableItemData, $this->campDatePurchasableItemsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->campDatePurchasableItemsData[$key]);

        return $this;
    }

    public function getCampDateUsersData(): array
    {
        return $this->campDateUsersData;
    }

    public function setCampDateUsersData(array $campDateUsersData): self
    {
        foreach ($campDateUsersData as $campDateUserData)
        {
            if (!$campDateUserData instanceof CampDateUserData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, CampDateUserData::class)
                );
            }
        }

        $this->campDateUsersData = $campDateUsersData;

        return $this;
    }

    public function addCampDateUserData(CampDateUserData $campDateUserData): self
    {
        if (in_array($campDateUserData, $this->campDateUsersData, true))
        {
            return $this;
        }

        $this->campDateUsersData[] = $campDateUserData;

        return $this;
    }

    public function removeCampDateUserData(CampDateUserData $campDateUserData): self
    {
        $key = array_search($campDateUserData, $this->campDateUsersData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->campDateUsersData[$key]);

        return $this;
    }
}