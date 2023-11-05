<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\CampDateRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Camp date entity.
 */
#[ORM\Entity(repositoryClass: CampDateRepository::class)]
class CampDate
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $startAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $endAt;

    #[ORM\Column(type: Types::FLOAT)]
    private float $price;

    #[ORM\Column(type: Types::INTEGER)]
    private int $capacity;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isOpenAboveCapacity = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isClosed = false;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Camp::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Camp $camp;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $leaders;

    #[ORM\ManyToOne(targetEntity: TripLocationPath::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?TripLocationPath $tripLocationPathThere = null;

    #[ORM\ManyToOne(targetEntity: TripLocationPath::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?TripLocationPath $tripLocationPathBack = null;

    #[ORM\OneToMany(mappedBy: 'campDate', targetEntity: CampDateFormField::class)]
    private Collection $campDateFormFields;

    #[ORM\OneToMany(mappedBy: 'campDate', targetEntity: CampDateAttachmentConfig::class)]
    private Collection $campDateAttachmentConfigs;

    #[ORM\OneToMany(mappedBy: 'campDate', targetEntity: CampDatePurchasableItem::class)]
    private Collection $campDatePurchasableItems;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(DateTimeImmutable $startAt, DateTimeImmutable $endAt, float $price, int $capacity, Camp $camp)
    {
        $this->id = Uuid::v4();
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->price = $price;
        $this->capacity = $capacity;
        $this->camp = $camp;
        $this->leaders = new ArrayCollection();
        $this->campDateFormFields = new ArrayCollection();
        $this->campDateAttachmentConfigs = new ArrayCollection();
        $this->campDatePurchasableItems = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getStartAt(): DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }

    public function setCamp(Camp $camp): self
    {
        $this->camp = $camp;

        return $this;
    }

    /**
     * @return User[]
     */
    public function getLeaders(): array
    {
        return $this->leaders->toArray();
    }

    public function addLeader(User $user): self
    {
        if (!$this->leaders->contains($user))
        {
            $this->leaders->add($user);
        }

        return $this;
    }

    public function removeLeader(User $user): self
    {
        $this->leaders->removeElement($user);

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

    /**
     * @return CampDateFormField[]
     */
    public function getCampDateFormFields(): array
    {
        return $this->campDateFormFields->toArray();
    }

    /**
     * @internal Inverse side.
     * @param CampDateFormField $campDateFormField
     * @return $this
     */
    public function addCampDateFormField(CampDateFormField $campDateFormField): self
    {
        if (!$this->campDateFormFields->contains($campDateFormField))
        {
            $this->campDateFormFields->add($campDateFormField);
            $campDateFormField->setCampDate($this);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param CampDateFormField $campDateFormField
     * @return $this
     */
    public function removeCampDateFormField(CampDateFormField $campDateFormField): self
    {
        $this->campDateFormFields->removeElement($campDateFormField);

        return $this;
    }

    /**
     * @return CampDateAttachmentConfig[]
     */
    public function getCampDateAttachmentConfigs(): array
    {
        return $this->campDateAttachmentConfigs->toArray();
    }

    /**
     * @internal Inverse side.
     * @param CampDateAttachmentConfig $campDateAttachmentConfig
     * @return $this
     */
    public function addCampDateAttachmentConfig(CampDateAttachmentConfig $campDateAttachmentConfig): self
    {
        if (!$this->campDateAttachmentConfigs->contains($campDateAttachmentConfig))
        {
            $this->campDateAttachmentConfigs->add($campDateAttachmentConfig);
            $campDateAttachmentConfig->setCampDate($this);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param CampDateAttachmentConfig $campDateAttachmentConfig
     * @return $this
     */
    public function removeCampDateAttachmentConfig(CampDateAttachmentConfig $campDateAttachmentConfig): self
    {
        $this->campDateAttachmentConfigs->removeElement($campDateAttachmentConfig);

        return $this;
    }

    /**
     * @return CampDatePurchasableItem[]
     */
    public function getCampDatePurchasableItems(): array
    {
        return $this->campDatePurchasableItems->toArray();
    }

    /**
     * @internal Inverse side.
     * @param CampDatePurchasableItem $campDatePurchasableItem
     * @return $this
     */
    public function addCampDatePurchasableItem(CampDatePurchasableItem $campDatePurchasableItem): self
    {
        if (!$this->campDatePurchasableItems->contains($campDatePurchasableItem))
        {
            $this->campDatePurchasableItems->add($campDatePurchasableItem);
            $campDatePurchasableItem->setCampDate($this);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param CampDatePurchasableItem $campDatePurchasableItem
     * @return $this
     */
    public function removeCampDatePurchasableItem(CampDatePurchasableItem $campDatePurchasableItem): self
    {
        $this->campDatePurchasableItems->removeElement($campDatePurchasableItem);

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}