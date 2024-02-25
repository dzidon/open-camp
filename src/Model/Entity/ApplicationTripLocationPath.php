<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Library\ApplicationTripLocation\ApplicationTripLocationArrayShape;
use App\Model\Repository\ApplicationTripLocationPathRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Trip location path attached to an application.
 */
#[ORM\Entity(repositoryClass: ApplicationTripLocationPathRepository::class)]
class ApplicationTripLocationPath
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isThere;

    #[ORM\Column(type: Types::JSON)]
    private array $locations;

    #[ORM\Column(type: Types::TEXT)]
    private string $location;

    #[ORM\ManyToOne(targetEntity: ApplicationCamper::class, inversedBy: 'applicationTripLocationPaths')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ApplicationCamper $applicationCamper;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(bool $isThere, array $locations, string $location, ApplicationCamper $applicationCamper)
    {
        $tripLocationArrayShape = new ApplicationTripLocationArrayShape();

        foreach ($locations as $locationFromCollection)
        {
            $tripLocationArrayShape->assertLocationArrayShape($locationFromCollection);
        }

        $this->id = Uuid::v4();
        $this->isThere = $isThere;
        $this->locations = $locations;
        $this->location = $location;
        $this->applicationCamper = $applicationCamper;
        $this->createdAt = new DateTimeImmutable('now');

        $this->applicationCamper->addApplicationTripLocationPath($this);
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function isThere(): string
    {
        return $this->isThere;
    }

    public function getPrice(): float
    {
        foreach ($this->locations as $location)
        {
            if ($this->location === $location['name'])
            {
                return $location['price'];
            }
        }

        return 0.0;
    }

    /**
     * @return array Contains sub-arrays with the following shape: ["price" => "float", "name" => "string"]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->assertLocation($location);

        $this->location = $location;

        return $this;
    }

    public function getApplicationCamper(): ApplicationCamper
    {
        return $this->applicationCamper;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function assertLocation(string $assertedLocation): void
    {
        foreach ($this->locations as $location)
        {
            if ($assertedLocation === $location['name'])
            {
                return;
            }
        }

        throw new LogicException(
            sprintf('Location %s cannot be set to an instance of "%s" as it is not present in the accepted locations.', $assertedLocation, self::class)
        );
    }
}