<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\ApplicationPurchasableItemInstanceRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\ORM\Mapping as ORM;

/**
 * Purchasable item variant instance of an application.
 */
#[ORM\Entity(repositoryClass: ApplicationPurchasableItemInstanceRepository::class)]
class ApplicationPurchasableItemInstance
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(type: Types::JSON)]
    private array $chosenVariantValues;

    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

    #[ORM\ManyToOne(targetEntity: ApplicationPurchasableItem::class, inversedBy: 'applicationPurchasableItemInstances')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ApplicationPurchasableItem $applicationPurchasableItem;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(array $chosenVariantValues, int $amount, ApplicationPurchasableItem $applicationPurchasableItem)
    {
        $this->id = Uuid::v4();
        $this->chosenVariantValues = $chosenVariantValues;
        $this->amount = $amount;
        $this->applicationPurchasableItem = $applicationPurchasableItem;
        $this->createdAt = new DateTimeImmutable('now');

        $applicationPurchasableItem->addApplicationPurchasableItemInstance($this);
        $this->assertChosenVariantValues();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getApplicationPurchasableItem(): ApplicationPurchasableItem
    {
        return $this->applicationPurchasableItem;
    }

    public function getChosenVariantValuesAsString(): string
    {
        if (empty($this->chosenVariantValues))
        {
            return '';
        }

        $stringParts = [];

        foreach ($this->chosenVariantValues as $variant => $value)
        {
            $stringParts[] = "$variant: $value";
        }

        return implode(', ', $stringParts);
    }

    public function getChosenVariantValues(): array
    {
        return $this->chosenVariantValues;
    }

    public function getChosenVariantValue(string $variant): ?string
    {
        if (!array_key_exists($variant, $this->chosenVariantValues))
        {
            return null;
        }

        return $this->chosenVariantValues[$variant];
    }

    public function setChosenVariantValue(string $variant, string $value): self
    {
        $this->chosenVariantValues[$variant] = $value;
        $this->assertChosenVariantValues();

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPrice(): float
    {
        $pricePerInstance = $this->applicationPurchasableItem->getPrice();

        return $pricePerInstance * $this->amount;
    }

    public function getPriceWithoutTax(): float
    {
        $taxDenominator = $this->applicationPurchasableItem
            ->getApplication()
            ->getTaxDenominator()
        ;

        return $this->getPrice() / $taxDenominator;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function assertChosenVariantValues(): void
    {
        $validVariantValues = $this->applicationPurchasableItem->getValidVariantValues();

        foreach ($this->chosenVariantValues as $variant => $value)
        {
            if (!array_key_exists($variant, $validVariantValues) || !in_array($value, $validVariantValues[$variant]))
            {
                throw new LogicException(
                    sprintf('Chosen variant values passed to "%s" have to contain strings that correspond to the valid variant values in %s.', $this::class, ApplicationPurchasableItem::class)
                );
            }
        }
    }
}