<?php

namespace App\Model\Entity;

use App\Library\Enum\GenderEnum;
use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\ApplicationCamperRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Campers attached to applications.
 */
#[ORM\Entity(repositoryClass: ApplicationCamperRepository::class)]
class ApplicationCamper
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255)]
    private string $nameFirst;

    #[ORM\Column(length: 255)]
    private string $nameLast;

    #[ORM\Column(length: 1)]
    private string $gender;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $bornAt;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationalIdentifier = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $dietaryRestrictions = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $healthRestrictions = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $medication = null;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'applicationCampers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Application $application;

    /** @var Collection<ApplicationTripLocationPath> */
    #[ORM\OneToMany(mappedBy: 'applicationCamper', targetEntity: ApplicationTripLocationPath::class)]
    private Collection $applicationTripLocationPaths;

    /** @var Collection<ApplicationFormFieldValue> */
    #[ORM\OneToMany(mappedBy: 'applicationCamper', targetEntity: ApplicationFormFieldValue::class)]
    private Collection $applicationFormFieldValues;

    /** @var Collection<ApplicationAttachment> */
    #[ORM\OneToMany(mappedBy: 'applicationCamper', targetEntity: ApplicationAttachment::class)]
    private Collection $applicationAttachments;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $nameFirst, string $nameLast, GenderEnum $gender, DateTimeImmutable $bornAt, Application $application)
    {
        $this->id = Uuid::v4();
        $this->nameFirst = $nameFirst;
        $this->nameLast = $nameLast;
        $this->gender = $gender->value;
        $this->bornAt = $bornAt;
        $this->application = $application;
        $this->applicationTripLocationPaths = new ArrayCollection();
        $this->applicationFormFieldValues = new ArrayCollection();
        $this->applicationAttachments = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');

        $application->addApplicationCamper($this);
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getNameFirst(): string
    {
        return $this->nameFirst;
    }

    public function setNameFirst(string $nameFirst): self
    {
        $this->nameFirst = $nameFirst;

        return $this;
    }

    public function getNameLast(): string
    {
        return $this->nameLast;
    }

    public function setNameLast(string $nameLast): self
    {
        $this->nameLast = $nameLast;

        return $this;
    }

    public function getGender(): GenderEnum
    {
        return GenderEnum::tryFrom($this->gender);
    }

    public function setGender(GenderEnum $gender): self
    {
        $this->gender = $gender->value;

        return $this;
    }

    public function getBornAt(): DateTimeImmutable
    {
        return $this->bornAt;
    }

    public function setBornAt(DateTimeImmutable $bornAt): self
    {
        $this->bornAt = $bornAt;

        return $this;
    }

    public function getNationalIdentifier(): ?string
    {
        return $this->nationalIdentifier;
    }

    public function setNationalIdentifier(?string $nationalIdentifier): self
    {
        $this->nationalIdentifier = $nationalIdentifier;

        return $this;
    }

    public function getDietaryRestrictions(): ?string
    {
        return $this->dietaryRestrictions;
    }

    public function setDietaryRestrictions(?string $dietaryRestrictions): self
    {
        $this->dietaryRestrictions = $dietaryRestrictions;

        return $this;
    }

    public function getHealthRestrictions(): ?string
    {
        return $this->healthRestrictions;
    }

    public function setHealthRestrictions(?string $healthRestrictions): self
    {
        $this->healthRestrictions = $healthRestrictions;

        return $this;
    }

    public function getMedication(): ?string
    {
        return $this->medication;
    }

    public function setMedication(?string $medication): self
    {
        $this->medication = $medication;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @return ApplicationTripLocationPath[]
     */
    public function getApplicationTripLocationPaths(): array
    {
        return $this->applicationTripLocationPaths->toArray();
    }

    public function getApplicationTripLocationPathThere(): ?ApplicationTripLocationPath
    {
        foreach ($this->applicationTripLocationPaths as $applicationTripLocationPath)
        {
            if ($applicationTripLocationPath->isThere())
            {
                return $applicationTripLocationPath;
            }
        }

        return null;
    }

    public function getApplicationTripLocationPathBack(): ?ApplicationTripLocationPath
    {
        foreach ($this->applicationTripLocationPaths as $applicationTripLocationPath)
        {
            if (!$applicationTripLocationPath->isThere())
            {
                return $applicationTripLocationPath;
            }
        }

        return null;
    }

    /**
     * @internal Inverse side.
     * @param ApplicationTripLocationPath $applicationTripLocationPath
     * @return $this
     */
    public function addApplicationTripLocationPath(ApplicationTripLocationPath $applicationTripLocationPath): self
    {
        if ($applicationTripLocationPath->getApplicationCamper() !== $this)
        {
            return $this;
        }

        if (!$this->applicationTripLocationPaths->contains($applicationTripLocationPath))
        {
            $this->applicationTripLocationPaths->add($applicationTripLocationPath);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param ApplicationTripLocationPath $applicationTripLocationPath
     * @return $this
     */
    public function removeApplicationTripLocationPath(ApplicationTripLocationPath $applicationTripLocationPath): self
    {
        $this->applicationFormFieldValues->removeElement($applicationTripLocationPath);

        return $this;
    }
    
    /**
     * @return ApplicationFormFieldValue[]
     */
    public function getApplicationFormFieldValues(): array
    {
        return $this->applicationFormFieldValues->toArray();
    }

    /**
     * @internal Inverse side.
     * @param ApplicationFormFieldValue $applicationFormFieldValue
     * @return $this
     */
    public function addApplicationFormFieldValue(ApplicationFormFieldValue $applicationFormFieldValue): self
    {
        if ($applicationFormFieldValue->getApplicationCamper() !== $this)
        {
            return $this;
        }

        if (!$this->applicationFormFieldValues->contains($applicationFormFieldValue))
        {
            $this->applicationFormFieldValues->add($applicationFormFieldValue);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param ApplicationFormFieldValue $applicationFormFieldValue
     * @return $this
     */
    public function removeApplicationFormFieldValue(ApplicationFormFieldValue $applicationFormFieldValue): self
    {
        $this->applicationFormFieldValues->removeElement($applicationFormFieldValue);

        return $this;
    }

    /**
     * @return ApplicationAttachment[]
     */
    public function getApplicationAttachments(): array
    {
        return $this->applicationAttachments->toArray();
    }

    /**
     * @internal Inverse side.
     * @param ApplicationAttachment $applicationAttachment
     * @return $this
     */
    public function addApplicationAttachment(ApplicationAttachment $applicationAttachment): self
    {
        if ($applicationAttachment->getApplicationCamper() !== $this)
        {
            return $this;
        }

        if (!$this->applicationAttachments->contains($applicationAttachment))
        {
            $this->applicationAttachments->add($applicationAttachment);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param ApplicationAttachment $applicationAttachment
     * @return $this
     */
    public function removeApplicationAttachment(ApplicationAttachment $applicationAttachment): self
    {
        $this->applicationAttachments->removeElement($applicationAttachment);

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