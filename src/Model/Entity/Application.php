<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Enum\Entity\ApplicationCustomerChannelEnum;
use App\Model\Library\DiscountConfig\DiscountConfigArrayShape;
use App\Model\Repository\ApplicationRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use LogicException;
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

    #[ORM\Column(type: Types::INTEGER, unique: true)]
    private int $invoiceNumber;

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

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customerChannel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customerChannelOther = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDraft = true;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $isAccepted = null;

    #[ORM\Column(length: 255)]
    private string $campName;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $campDateDescription;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentMethodLabel = null;

    #[ORM\Column(type: Types::FLOAT)]
    private float $deposit;

    #[ORM\Column(type: Types::FLOAT)]
    private float $priceWithoutDeposit;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $depositUntil;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(type: Types::FLOAT)]
    private float $tax;

    #[ORM\Column(type: Types::JSON)]
    private array $discountRecurringCampersConfig;

    #[ORM\Column(type: Types::JSON)]
    private array $discountSiblingsConfig;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $discountSiblingsIntervalFrom = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $discountSiblingsIntervalTo = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEuBusinessDataEnabled;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isNationalIdentifierEnabled;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEmailMandatory;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isPhoneNumberMandatory;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isPurchasableItemsIndividualMode;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $completedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $campDateStartAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $campDateEndAt;

    #[ORM\ManyToOne(targetEntity: CampDate::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?CampDate $campDate;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: PaymentMethod::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?PaymentMethod $paymentMethod = null;

    /** @var Collection<ApplicationContact> */
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationContact::class)]
    private Collection $applicationContacts;

    /** @var Collection<ApplicationCamper> */
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationCamper::class)]
    private Collection $applicationCampers;

    /** @var Collection<ApplicationFormFieldValue> */
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationFormFieldValue::class)]
    private Collection $applicationFormFieldValues;

    /** @var Collection<ApplicationAttachment> */
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationAttachment::class)]
    private Collection $applicationAttachments;

    /** @var Collection<ApplicationPurchasableItem> */
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationPurchasableItem::class)]
    private Collection $applicationPurchasableItems;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string   $simpleId,
                                int      $invoiceNumber,
                                string   $email,
                                string   $nameFirst,
                                string   $nameLast,
                                string   $street,
                                string   $town,
                                string   $zip,
                                string   $country,
                                string   $currency,
                                float    $tax,
                                array    $discountRecurringCampersConfig,
                                array    $discountSiblingsConfig,
                                bool     $isEuBusinessDataEnabled,
                                bool     $isNationalIdentifierEnabled,
                                bool     $isEmailMandatory,
                                bool     $isPhoneNumberMandatory,
                                bool     $isPurchasableItemsIndividualMode,
                                CampDate $campDate,
                                ?string  $campDateDescription = null)
    {
        $this->id = Uuid::v4();
        $this->simpleId = $simpleId;
        $this->invoiceNumber = $invoiceNumber;
        $this->email = $email;
        $this->nameFirst = $nameFirst;
        $this->nameLast = $nameLast;
        $this->street = $street;
        $this->town = $town;
        $this->zip = $zip;
        $this->country = $country;
        $this->currency = $currency;
        $this->tax = $tax;
        $this->discountRecurringCampersConfig = $discountRecurringCampersConfig;
        $this->discountSiblingsConfig = $discountSiblingsConfig;
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
        $this->isEmailMandatory = $isEmailMandatory;
        $this->isPhoneNumberMandatory = $isPhoneNumberMandatory;
        $this->isPurchasableItemsIndividualMode = $isPurchasableItemsIndividualMode;
        $this->campDateDescription = $campDateDescription;
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
        $this->depositUntil = $this->campDate->getDepositUntil();

        $discountConfigArrayShape = new DiscountConfigArrayShape();
        $discountConfigArrayShape->assertRecurringCampersConfig($this->discountRecurringCampersConfig);
        $discountConfigArrayShape->assertSiblingsConfig($this->discountSiblingsConfig);
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getSimpleId(): string
    {
        return $this->simpleId;
    }

    public function getInvoiceNumber(): int
    {
        return $this->invoiceNumber;
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCustomerChannel(): ?ApplicationCustomerChannelEnum
    {
        if ($this->customerChannel === null)
        {
            return null;
        }

        return ApplicationCustomerChannelEnum::tryFrom($this->customerChannel);
    }

    public function setCustomerChannel(?ApplicationCustomerChannelEnum $customerChannel, ?string $customerChannelOther = null): self
    {
        $this->customerChannel = $customerChannel?->value;
        $this->customerChannelOther = $customerChannelOther;

        $this->assertCustomerChannelOther();
        $this->setNullCustomerChannelOtherIfCustomerChannelIsNotOther();

        return $this;
    }

    public function getCustomerChannelOther(): ?string
    {
        return $this->customerChannelOther;
    }

    public function setCustomerChannelOther(?string $customerChannelOther): self
    {
        $this->customerChannelOther = $customerChannelOther;

        $this->assertCustomerChannelOther();
        $this->setNullCustomerChannelOtherIfCustomerChannelIsNotOther();

        return $this;
    }

    public function canBeCompleted(): bool
    {
        return
            $this->isDraft                         &&
            count($this->applicationContacts) >= 1 &&
            count($this->applicationCampers)  >= 1 &&
            $this->paymentMethod !== null
        ;
    }

    public function isDraft(): bool
    {
        return $this->isDraft;
    }

    public function setIsDraft(bool $isDraft): self
    {
        if ($this->isDraft && !$isDraft)
        {
            $this->completedAt = new DateTimeImmutable('now');
        }

        if (!$this->isDraft && $isDraft)
        {
            $this->completedAt = null;
        }

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

    public function getCampDateDescription(): ?string
    {
        return $this->campDateDescription;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function getDiscountRecurringCampersConfig(): array
    {
        return $this->discountRecurringCampersConfig;
    }

    public function getDiscountSiblingsConfig(): array
    {
        return $this->discountSiblingsConfig;
    }
    
    public function getDiscountSiblingsIntervalFrom(): ?int
    {
        return $this->discountSiblingsIntervalFrom;
    }

    public function getDiscountSiblingsIntervalTo(): ?int
    {
        return $this->discountSiblingsIntervalTo;
    }

    public function setDiscountSiblingsInterval(?int $discountSiblingsIntervalFrom, ?int $discountSiblingsIntervalTo): self
    {
        $this->assertDiscountIntervalExistsInConfig($discountSiblingsIntervalFrom, $discountSiblingsIntervalTo);

        if (!$this->isSiblingDiscountIntervalEligibleForNumberOfCampers($discountSiblingsIntervalFrom, $discountSiblingsIntervalTo))
        {
            return $this;
        }

        $this->discountSiblingsIntervalFrom = $discountSiblingsIntervalFrom;
        $this->discountSiblingsIntervalTo = $discountSiblingsIntervalTo;

        return $this;
    }

    public function resetSiblingsDiscountIfIntervalNotEligibleForNumberOfCampers(): void
    {
        if ($this->isSiblingDiscountIntervalEligibleForNumberOfCampers($this->discountSiblingsIntervalFrom, $this->discountSiblingsIntervalTo))
        {
            return;
        }

        $this->discountSiblingsIntervalFrom = null;
        $this->discountSiblingsIntervalTo = null;
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

    public function isPurchasableItemsIndividualMode(): bool
    {
        return $this->isPurchasableItemsIndividualMode;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getFullPrice(): float
    {
        $fullPrice = 0.0;

        foreach ($this->applicationCampers as $applicationCamper)
        {
            $fullPrice += $applicationCamper->getFullPrice();
        }

        foreach ($this->applicationPurchasableItems as $applicationPurchasableItem)
        {
            $fullPrice += $applicationPurchasableItem->getFullPrice();
        }

        return $fullPrice;
    }

    public function getFullDeposit(): float
    {
        $fullDeposit = 0.0;

        foreach ($this->applicationCampers as $applicationCamper)
        {
            $fullDeposit += $applicationCamper->getDeposit();
        }

        return $fullDeposit;
    }

    public function getFullPriceWithoutDeposit(): float
    {
        return $this->getFullPrice() - $this->getFullDeposit();
    }

    public function getFullPriceWithoutTax(): float
    {
        return $this->getFullPrice() / $this->getTaxDenominator();
    }

    public function getPricePerCamper(): float
    {
        return $this->deposit + $this->priceWithoutDeposit;
    }

    public function getPricePerCamperWithoutTax(): float
    {
        return $this->getPricePerCamper() / $this->getTaxDenominator();
    }

    public function getDeposit(): float
    {
        return $this->deposit;
    }

    public function getPriceWithoutDeposit(): float
    {
        return $this->priceWithoutDeposit;
    }

    public function getDepositUntil(): ?DateTimeImmutable
    {
        return $this->depositUntil;
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

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isPaymentMethodOnline(): bool
    {
        if ($this->paymentMethod === null)
        {
            return false;
        }

        return $this->paymentMethod->isOnline();
    }

    public function getPaymentMethodLabel(): ?string
    {
        return $this->paymentMethodLabel;
    }

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        $this->paymentMethodLabel = $this->paymentMethod?->getLabel();

        return $this;
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

    public function getPurchasableItemInstancesTotalAmount(): int
    {
        $totalAmount = 0.0;

        foreach ($this->applicationPurchasableItems as $applicationPurchasableItem)
        {
            $totalAmount += $applicationPurchasableItem->getInstancesTotalAmount();
        }

        return $totalAmount;
    }

    public function canUploadAttachmentsLater(): bool
    {
        foreach ($this->applicationAttachments as $applicationAttachment)
        {
            if ($applicationAttachment->canBeUploadedLater())
            {
                return true;
            }
        }

        foreach ($this->applicationCampers as $applicationCamper)
        {
            foreach ($applicationCamper->getApplicationAttachments() as $applicationAttachment)
            {
                if ($applicationAttachment->canBeUploadedLater())
                {
                    return true;
                }
            }
        }

        return false;
    }

    public function isDepositPaid(): bool
    {
        return false;
    }

    public function isRestPaid(): bool
    {
        return false;
    }

    public function isFullyPaid(): bool
    {
        if ($this->isDepositPaid() && $this->isRestPaid())
        {
            return true;
        }

        return false;
    }

    public function isAwaitingPayment(): bool
    {
        return !$this->isFullyPaid() && $this->getFullPrice() > 0.0;
    }

    public function canBeAccepted(): bool
    {
        if ($this->isDraft)
        {
            return false;
        }

        if (count($this->applicationContacts) < 1)
        {
            return false;
        }

        if (count($this->applicationCampers)  < 1)
        {
            return false;
        }

        if ($this->paymentMethod === null)
        {
            return false;
        }

        if ($this->canUploadAttachmentsLater())
        {
            return false;
        }

        if (!$this->isFullyPaid())
        {
            return false;
        }

        return true;
    }

    public function getTaxDenominator(): float
    {
        return 1.0 + ($this->tax / 100);
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function assertDiscountIntervalExistsInConfig(?int $discountSiblingsIntervalFrom, ?int $discountSiblingsIntervalTo): void
    {
        if ($discountSiblingsIntervalFrom === null && $discountSiblingsIntervalTo === null)
        {
            return;
        }

        $foundInConfig = false;

        foreach ($this->discountSiblingsConfig as $options)
        {
            $configFrom = $options['from'];
            $configTo = $options['to'];

            if ($discountSiblingsIntervalFrom === $configFrom && $discountSiblingsIntervalTo === $configTo)
            {
                $foundInConfig = true;

                break;
            }
        }

        if (!$foundInConfig)
        {
            throw new LogicException(sprintf(
                'You tried to set the siblings discount interval to [%s, %s] in "%s", but this interval is not in the config.', $discountSiblingsIntervalFrom, $discountSiblingsIntervalTo, self::class
            ));
        }
    }

    private function isSiblingDiscountIntervalEligibleForNumberOfCampers(?int $discountSiblingsIntervalFrom, ?int $discountSiblingsIntervalTo): bool
    {
        $numberOfApplicationCampers = count($this->getApplicationCampers());

        if ($discountSiblingsIntervalTo !== null && $numberOfApplicationCampers > $discountSiblingsIntervalTo)
        {
            return true;
        }

        return $discountSiblingsIntervalFrom === null || $numberOfApplicationCampers >= $discountSiblingsIntervalFrom;
    }

    private function assertCustomerChannelOther(): void
    {
        if ($this->getCustomerChannel() === ApplicationCustomerChannelEnum::OTHER && $this->getCustomerChannel() === null)
        {
            throw new LogicException('Application entity cannot have attribute "customerChannelOther" set to null when attribute "customerChannel" is set to "other".');
        }
    }

    private function setNullCustomerChannelOtherIfCustomerChannelIsNotOther(): void
    {
        if ($this->getCustomerChannel() !== ApplicationCustomerChannelEnum::OTHER)
        {
            $this->customerChannelOther = null;
        }
    }
}