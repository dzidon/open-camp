<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * User entity used for auth.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Role $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFirst = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameLast = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $town = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $businessName = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $businessCin = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $businessVatId = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $bornAt = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $bioShort = null;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $imageExtension = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $urlName = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $guidePriority = 0;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isFeaturedGuide = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $lastActiveAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $email)
    {
        $this->id = Uuid::v4();
        $this->email = $email;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    /**
     * Internal Symfony method that makes authorization work.
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $permissionNames[] = 'ROLE_USER';

        if ($this->role !== null)
        {
            foreach ($this->role->getPermissions() as $permission)
            {
                $permissionNames[] = $permission->getName();
            }
        }

        return $permissionNames;
    }

    /**
     * Checks if user's role has a permission with the given name.
     *
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(string $permissionName): bool
    {
        if ($this->role === null)
        {
            return false;
        }

        foreach ($this->role->getPermissions() as $permission)
        {
            if ($permissionName === $permission->getName())
            {
                return true;
            }
        }

        return false;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getNameFull(): ?string
    {
        if ($this->nameFirst !== null && $this->nameLast !== null)
        {
            return $this->nameFirst . ' ' . $this->nameLast;
        }

        return null;
    }

    public function getNameFirst(): ?string
    {
        return $this->nameFirst;
    }

    public function setNameFirst(?string $nameFirst): self
    {
        $this->nameFirst = $nameFirst;

        return $this;
    }

    public function getNameLast(): ?string
    {
        return $this->nameLast;
    }

    public function setNameLast(?string $nameLast): self
    {
        $this->nameLast = $nameLast;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function setBusinessName(?string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }

    public function getBusinessCin(): ?string
    {
        return $this->businessCin;
    }

    public function setBusinessCin(?string $businessCin): self
    {
        $this->businessCin = $businessCin;

        return $this;
    }

    public function getBusinessVatId(): ?string
    {
        return $this->businessVatId;
    }

    public function setBusinessVatId(?string $businessVatId): self
    {
        $this->businessVatId = $businessVatId;

        return $this;
    }

    public function getAge(): ?int
    {
        if ($this->bornAt === null)
        {
            return null;
        }

        $now = new DateTimeImmutable('now');
        $interval = $now->diff($this->bornAt);

        return $interval->y;
    }

    public function getBornAt(): ?DateTimeImmutable
    {
        return $this->bornAt;
    }

    public function setBornAt(?DateTimeImmutable $bornAt): self
    {
        $this->bornAt = $bornAt;

        return $this;
    }

    public function getBioShort(): ?string
    {
        return $this->bioShort;
    }

    public function setBioShort(?string $bioShort): self
    {
        $this->bioShort = $bioShort;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getImageFileName(): ?string
    {
        if ($this->imageExtension === null)
        {
            return null;
        }

        return $this->id->toRfc4122() . '.' . $this->imageExtension;
    }

    public function getImageExtension(): ?string
    {
        return $this->imageExtension;
    }

    public function setImageExtension(?string $imageExtension): self
    {
        $this->imageExtension = $imageExtension;

        return $this;
    }

    public function getUrlName(): ?string
    {
        return $this->urlName;
    }

    public function setUrlName(?string $urlName): self
    {
        $this->urlName = $urlName;

        return $this;
    }

    public function getGuidePriority(): int
    {
        return $this->guidePriority;
    }

    public function setGuidePriority(int $guidePriority): self
    {
        $this->guidePriority = $guidePriority;

        return $this;
    }

    public function isFeaturedGuide(): bool
    {
        return $this->isFeaturedGuide;
    }

    public function setIsFeaturedGuide(bool $isFeaturedGuide): self
    {
        $this->isFeaturedGuide = $isFeaturedGuide;

        return $this;
    }

    public function getLastActiveAt(): ?DateTimeImmutable
    {
        return $this->lastActiveAt;
    }

    public function setLastActiveAt(?DateTimeImmutable $lastActiveAt): self
    {
        $this->lastActiveAt = $lastActiveAt;

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
