<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\CampDateUserRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\ORM\Mapping as ORM;

/**
 * Many to many connection between camp dates and users.
 */
#[ORM\Entity(repositoryClass: CampDateUserRepository::class)]
class CampDateUser
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: CampDate::class, inversedBy: 'campDateUsers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CampDate $campDate;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $canUpdateApplicationsState = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $canUpdateApplications = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $canUpdateApplicationPayments = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(CampDate $campDate, User $user)
    {
        $this->id = Uuid::v4();
        $this->user = $user;
        $this->createdAt = new DateTimeImmutable('now');
        $this->setCampDate($campDate);
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getCampDate(): CampDate
    {
        return $this->campDate;
    }

    public function setCampDate(CampDate $campDate): self
    {
        if (isset($this->campDate))
        {
            if ($this->campDate === $campDate)
            {
                return $this;
            }

            $this->campDate->removeCampDateUser($this);
        }

        $this->campDate = $campDate;
        $this->campDate->addCampDateUser($this);

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function canUpdateApplicationsState(): bool
    {
        return $this->canUpdateApplicationsState;
    }

    public function setCanUpdateApplicationsState(bool $canUpdateApplicationsState): self
    {
        $this->canUpdateApplicationsState = $canUpdateApplicationsState;

        return $this;
    }

    public function canUpdateApplications(): bool
    {
        return $this->canUpdateApplications;
    }

    public function setCanUpdateApplications(bool $canUpdateApplications): self
    {
        $this->canUpdateApplications = $canUpdateApplications;

        return $this;
    }

    public function canUpdateApplicationPayments(): bool
    {
        return $this->canUpdateApplicationPayments;
    }

    public function setCanUpdateApplicationPayments(bool $canUpdateApplicationPayments): self
    {
        $this->canUpdateApplicationPayments = $canUpdateApplicationPayments;

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