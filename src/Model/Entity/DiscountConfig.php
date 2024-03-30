<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Library\DiscountConfig\DiscountConfigArrayValidator;
use App\Model\Repository\DiscountConfigRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Admin discount configuration.
 */
#[ORM\Entity(repositoryClass: DiscountConfigRepository::class)]
class DiscountConfig
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::JSON)]
    private array $recurringCampersConfig = [];

    #[ORM\Column(type: Types::JSON)]
    private array $siblingsConfig = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name)
    {
        $this->id = Uuid::v4();
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRecurringCampersConfig(): array
    {
        return $this->recurringCampersConfig;
    }

    public function setRecurringCampersConfig(array $recurringCampersConfig): self
    {
        DiscountConfigArrayValidator::assertRecurringCampersConfig($recurringCampersConfig);

        $this->recurringCampersConfig = [];

        foreach ($recurringCampersConfig as $recurringCamperConfig)
        {
            $from = $recurringCamperConfig['from'];
            $index = (int) $from === null ? 0 : $from;

            $this->recurringCampersConfig[$index] = $recurringCamperConfig;
        }

        ksort($this->recurringCampersConfig);

        return $this;
    }

    public function getSiblingsConfig(): array
    {
        return $this->siblingsConfig;
    }

    public function setSiblingsConfig(array $siblingsConfig): self
    {
        DiscountConfigArrayValidator::assertSiblingsConfig($siblingsConfig);

        $this->siblingsConfig = [];

        foreach ($siblingsConfig as $siblingConfig)
        {
            $from = $siblingConfig['from'];
            $index = $from === null ? 0 : $from;

            $this->siblingsConfig[$index] = $siblingConfig;
        }

        ksort($this->siblingsConfig);

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