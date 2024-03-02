<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\ApplicationPurchasableItemRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Purchasable item attached to an application.
 */
#[ORM\Entity(repositoryClass: ApplicationPurchasableItemRepository::class)]
class ApplicationPurchasableItem
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column(type: Types::FLOAT)]
    private float $price;

    #[ORM\Column(type: Types::INTEGER)]
    private int $maxAmount;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: Types::JSON)]
    private array $validVariantValues;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isGlobal;

    #[ORM\ManyToOne(targetEntity: PurchasableItem::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?PurchasableItem $purchasableItem;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'applicationPurchasableItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Application $application;

    /** @var Collection<ApplicationPurchasableItemInstance> */
    #[ORM\OneToMany(mappedBy: 'applicationPurchasableItem', targetEntity: ApplicationPurchasableItemInstance::class)]
    private Collection $applicationPurchasableItemInstances;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string          $label,
                                float           $price,
                                int             $maxAmount,
                                array           $validVariantValues,
                                int             $priority,
                                bool            $isGlobal,
                                PurchasableItem $purchasableItem,
                                Application     $application,
                                ?string         $description = null)
    {
        $this->id = Uuid::v4();
        $this->label = $label;
        $this->price = $price;
        $this->maxAmount = $maxAmount;
        $this->validVariantValues = $validVariantValues;
        $this->description = $description;
        $this->priority = $priority;
        $this->isGlobal = $isGlobal;
        $this->purchasableItem = $purchasableItem;
        $this->application = $application;
        $this->applicationPurchasableItemInstances = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');

        $application->addApplicationPurchasableItem($this);
        $this->assertValidVariantValues();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPriceWithoutTax(): float
    {
        return $this->price / $this->application->getTaxDenominator();
    }

    public function getFullPrice(): float
    {
        return $this->price * $this->getInstancesTotalAmount();
    }

    public function getInstancesTotalAmount(): int
    {
        $total = 0;

        foreach ($this->getApplicationPurchasableItemInstances() as $applicationPurchasableItemInstance)
        {
            $total += $applicationPurchasableItemInstance->getAmount();
        }

        return $total;
    }

    public function getCalculatedMaxAmount(): int
    {
        $numberOfCampers = count($this->application->getApplicationCampers());

        return $this->isGlobal ? $this->maxAmount : $this->maxAmount * $numberOfCampers;
    }

    public function isCalculatedMaxAmountGreaterThanOne(): bool
    {
        return $this->getCalculatedMaxAmount() > 1;
    }

    public function getMaxAmount(): int
    {
        return $this->maxAmount;
    }

    public function getValidVariantValues(?string $variant = null): array
    {
        if ($variant === null)
        {
            return $this->validVariantValues;
        }

        if (!array_key_exists($variant, $this->validVariantValues))
        {
            return [];
        }

        return $this->validVariantValues[$variant];
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isGlobal(): bool
    {
        return $this->isGlobal;
    }

    public function getPurchasableItem(): ?PurchasableItem
    {
        return $this->purchasableItem;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @return ApplicationPurchasableItemInstance[]
     */
    public function getApplicationPurchasableItemInstances(): array
    {
        return $this->applicationPurchasableItemInstances->toArray();
    }

    /**
     * @param ApplicationPurchasableItemInstance $applicationPurchasableItemInstance
     * @return $this
     * @internal Inverse side.
     */
    public function addApplicationPurchasableItemInstance(ApplicationPurchasableItemInstance $applicationPurchasableItemInstance): self
    {
        if ($applicationPurchasableItemInstance->getApplicationPurchasableItem() !== $this)
        {
            return $this;
        }

        if (!$this->applicationPurchasableItemInstances->contains($applicationPurchasableItemInstance))
        {
            $this->applicationPurchasableItemInstances->add($applicationPurchasableItemInstance);
        }

        return $this;
    }

    /**
     * @param ApplicationPurchasableItemInstance $applicationPurchasableItemInstance
     * @return $this
     * @internal Inverse side.
     */
    public function removeApplicationPurchasableItemInstance(ApplicationPurchasableItemInstance $applicationPurchasableItemInstance): self
    {
        $this->applicationPurchasableItemInstances->removeElement($applicationPurchasableItemInstance);

        return $this;
    }

    public function hasMultipleVariants(): bool
    {
        foreach ($this->validVariantValues as $values)
        {
            if (count($values) >= 2)
            {
                return true;
            }
        }

        return false;
    }

    public function hasInstancesAssignedToCamper(): bool
    {
        foreach ($this->applicationPurchasableItemInstances as $applicationPurchasableItemInstance)
        {
            $applicationCamper = $applicationPurchasableItemInstance->getApplicationCamper();

            if ($applicationCamper !== null)
            {
                return true;
            }
        }

        return false;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function assertValidVariantValues(): void
    {
        $exception = new LogicException(
            sprintf('Valid variant values passed to "%s" have to be an array with sub arrays that contain strings.', $this::class)
        );

        foreach ($this->validVariantValues as $subArray)
        {
            if (!is_array($subArray))
            {
                throw $exception;
            }

            foreach ($subArray as $value)
            {
                if (!is_string($value))
                {
                    throw $exception;
                }
            }
        }
    }
}