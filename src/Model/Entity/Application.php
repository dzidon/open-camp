<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\ApplicationRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lets campers apply to a camp date.
 */
#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 6, unique: true)]
    private string $simpleId;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $nameFirst;

    #[ORM\Column(length: 255)]
    private string $nameLast;

    #[ORM\Column(length: 255)]
    private string $street;

    #[ORM\Column(length: 255)]
    private string $town;

    #[ORM\Column(length: 11)]
    private string $zip;

    #[ORM\Column(length: 2)]
    private string $country;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $businessName = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $businessCin = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $businessVatId = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDraft = true;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $isAccepted = null;

    #[ORM\Column(length: 255)]
    private string $campName;

    #[ORM\Column(type: Types::FLOAT)]
    private float $deposit;

    #[ORM\Column(type: Types::FLOAT)]
    private float $priceWithoutDeposit;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(type: Types::FLOAT)]
    private float $tax;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEuBusinessDataEnabled;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isNationalIdentifierEnabled;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEmailMandatory;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isPhoneNumberMandatory;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $campDateStartAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $campDateEndAt;

    #[ORM\ManyToOne(targetEntity: CampDate::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?CampDate $campDate;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationContact::class)]
    private Collection $applicationContacts;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationCamper::class)]
    private Collection $applicationCampers;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationFormFieldValue::class)]
    private Collection $applicationFormFieldValues;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationAttachment::class)]
    private Collection $applicationAttachments;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationPurchasableItem::class)]
    private Collection $applicationPurchasableItems;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string   $simpleId,
                                string   $email,
                                string   $nameFirst,
                                string   $nameLast,
                                string   $street,
                                string   $town,
                                string   $zip,
                                string   $country,
                                string   $currency,
                                float    $tax,
                                bool     $isEuBusinessDataEnabled,
                                bool     $isNationalIdentifierEnabled,
                                bool     $isEmailMandatory,
                                bool     $isPhoneNumberMandatory,
                                CampDate $campDate,
                                ?User    $user)
    {
        $this->id = Uuid::v4();
        $this->simpleId = $simpleId;
        $this->email = $email;
        $this->nameFirst = $nameFirst;
        $this->nameLast = $nameLast;
        $this->street = $street;
        $this->town = $town;
        $this->zip = $zip;
        $this->country = $country;
        $this->user = $user;
        $this->currency = $currency;
        $this->tax = $tax;
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
        $this->isEmailMandatory = $isEmailMandatory;
        $this->isPhoneNumberMandatory = $isPhoneNumberMandatory;
        $this->applicationContacts = new ArrayCollection();
        $this->applicationCampers = new ArrayCollection();
        $this->applicationFormFieldValues = new ArrayCollection();
        $this->applicationAttachments = new ArrayCollection();
        $this->applicationPurchasableItems = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');

        $this->campDate = $campDate;
        $camp = $this->campDate->getCamp();
        $this->campName = $camp->getName();
        $this->campDateStartAt = $this->campDate->getStartAt();
        $this->campDateEndAt = $this->campDate->getEndAt();

        $this->deposit = $this->campDate->getDeposit();
        $this->priceWithoutDeposit = $this->campDate->getPriceWithoutDeposit();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getSimpleId(): string
    {
        return $this->simpleId;
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

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getTown(): string
    {
        return $this->town;
    }

    public function setTown(string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
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

    public function isDraft(): bool
    {
        return $this->isDraft;
    }

    public function setIsDraft(bool $isDraft): self
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    public function isAccepted(): ?bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(?bool $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    public function getCampName(): string
    {
        return $this->campName;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function isNationalIdentifierEnabled(): string
    {
        return $this->isNationalIdentifierEnabled;
    }

    public function isEuBusinessDataEnabled(): string
    {
        return $this->isEuBusinessDataEnabled;
    }

    public function isEmailMandatory(): bool
    {
        return $this->isEmailMandatory;
    }

    public function isPhoneNumberMandatory(): bool
    {
        return $this->isPhoneNumberMandatory;
    }

    public function getFullPrice(): float
    {
        return $this->deposit + $this->priceWithoutDeposit;
    }

    public function getFullPriceWithoutTax(): float
    {
        return $this->getDepositWithoutTax() + $this->getPriceWithoutDepositWithoutTax();
    }

    public function getDeposit(): float
    {
        return $this->deposit;
    }

    public function getDepositWithoutTax(): float
    {
        return round($this->deposit / $this->getTaxDenominator());
    }

    public function getPriceWithoutDeposit(): float
    {
        return $this->priceWithoutDeposit;
    }

    public function getPriceWithoutDepositWithoutTax(): float
    {
        return round($this->priceWithoutDeposit / $this->getTaxDenominator());
    }

    public function getCampDateStartAt(): ?DateTimeImmutable
    {
        return $this->campDateStartAt;
    }

    public function getCampDateEndAt(): ?DateTimeImmutable
    {
        return $this->campDateEndAt;
    }

    public function getCampDate(): ?CampDate
    {
        return $this->campDate;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return ApplicationContact[]
     */
    public function getApplicationContacts(): array
    {
        return $this->applicationContacts->toArray();
    }

    /**
     * @internal Inverse side.
     * @param ApplicationContact $applicationContact
     * @return $this
     */
    public function addApplicationContact(ApplicationContact $applicationContact): self
    {
        if ($applicationContact->getApplication() !== $this)
        {
            return $this;
        }

        if (!$this->applicationContacts->contains($applicationContact))
        {
            $this->applicationContacts->add($applicationContact);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param ApplicationContact $applicationContact
     * @return $this
     */
    public function removeApplicationContact(ApplicationContact $applicationContact): self
    {
        $this->applicationContacts->removeElement($applicationContact);

        return $this;
    }

    /**
     * @return ApplicationCamper[]
     */
    public function getApplicationCampers(): array
    {
        return $this->applicationCampers->toArray();
    }

    /**
     * @internal Inverse side.
     * @param ApplicationCamper $applicationCamper
     * @return $this
     */
    public function addApplicationCamper(ApplicationCamper $applicationCamper): self
    {
        if ($applicationCamper->getApplication() !== $this)
        {
            return $this;
        }

        if (!$this->applicationCampers->contains($applicationCamper))
        {
            $this->applicationCampers->add($applicationCamper);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param ApplicationCamper $applicationCamper
     * @return $this
     */
    public function removeApplicationCamper(ApplicationCamper $applicationCamper): self
    {
        $this->applicationCampers->removeElement($applicationCamper);

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
        if ($applicationFormFieldValue->getApplication() !== $this)
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
        if ($applicationAttachment->getApplication() !== $this)
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

    /**
     * @return ApplicationPurchasableItem[]
     */
    public function getApplicationPurchasableItems(): array
    {
        return $this->applicationPurchasableItems->toArray();
    }

    /**
     * @internal Inverse side.
     * @param ApplicationPurchasableItem $applicationPurchasableItem
     * @return $this
     */
    public function addApplicationPurchasableItem(ApplicationPurchasableItem $applicationPurchasableItem): self
    {
        if ($applicationPurchasableItem->getApplication() !== $this)
        {
            return $this;
        }

        if (!$this->applicationPurchasableItems->contains($applicationPurchasableItem))
        {
            $this->applicationPurchasableItems->add($applicationPurchasableItem);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param ApplicationPurchasableItem $applicationPurchasableItem
     * @return $this
     */
    public function removeApplicationPurchasableItem(ApplicationPurchasableItem $applicationPurchasableItem): self
    {
        $this->applicationPurchasableItems->removeElement($applicationPurchasableItem);

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

    private function getTaxDenominator(): float
    {
        return 1.0 + ($this->tax / 100);
    }
}