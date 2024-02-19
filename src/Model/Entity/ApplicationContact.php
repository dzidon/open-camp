<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Enum\Entity\ContactRoleEnum;
use App\Model\Repository\ApplicationContactRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Contacts attached to applications.
 */
#[ORM\Entity(repositoryClass: ApplicationContactRepository::class)]
class ApplicationContact
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255)]
    private string $nameFirst;

    #[ORM\Column(length: 255)]
    private string $nameLast;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'phone_number', nullable: true)]
    private ?PhoneNumber $phoneNumber = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\Column(length: 32)]
    private string $role;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roleOther = null;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'applicationContacts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Application $application;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string          $nameFirst,
                                string          $nameLast,
                                Application     $application,
                                int             $priority,
                                ContactRoleEnum $role,
                                ?string         $roleOther = null)
    {
        $this->id = Uuid::v4();
        $this->nameFirst = $nameFirst;
        $this->nameLast = $nameLast;
        $this->application = $application;
        $this->priority = $priority;
        $this->createdAt = new DateTimeImmutable('now');
        $this->setRole($role, $roleOther);

        $application->addApplicationContact($this);
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getNameFull(): string
    {
        return $this->nameFirst . ' ' . $this->nameLast;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber === null ? null : clone $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumber $phoneNumber): self
    {
        if ($phoneNumber === null)
        {
            $this->phoneNumber = $phoneNumber;

            return $this;
        }

        if ($this->phoneNumber !== null && $this->phoneNumber->equals($phoneNumber))
        {
            return $this;
        }

        $this->phoneNumber = clone $phoneNumber;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getRole(): ContactRoleEnum
    {
        return ContactRoleEnum::tryFrom($this->role);
    }

    public function setRole(ContactRoleEnum $role, ?string $roleOther = null): self
    {
        $this->role = $role->value;
        $this->roleOther = $roleOther;

        $this->assertRoleOther();
        $this->setNullRoleOtherIfRoleIsNotOther();

        return $this;
    }

    public function getRoleOther(): ?string
    {
        return $this->roleOther;
    }

    public function setRoleOther(?string $roleOther): self
    {
        $this->roleOther = $roleOther;

        $this->assertRoleOther();
        $this->setNullRoleOtherIfRoleIsNotOther();

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function assertRoleOther(): void
    {
        if ($this->getRole() === ContactRoleEnum::OTHER && $this->getRoleOther() === null)
        {
            throw new LogicException('Contact entity cannot have attribute "roleOther" set to null when attribute "role" is set to "other".');
        }
    }

    private function setNullRoleOtherIfRoleIsNotOther(): void
    {
        if ($this->getRole() !== ContactRoleEnum::OTHER)
        {
            $this->roleOther = null;
        }
    }
}