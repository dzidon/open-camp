<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use App\Model\Repository\ApplicationPaymentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\ORM\Mapping as ORM;

/**
 * Application payment entity.
 */
#[ORM\Entity(repositoryClass: ApplicationPaymentRepository::class)]
class ApplicationPayment
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(type: Types::JSON)]
    private array $states;

    #[ORM\Column(type: Types::JSON)]
    private array $paidStates;

    #[ORM\Column(type: Types::JSON)]
    private array $cancelledStates;

    #[ORM\Column(type: Types::JSON)]
    private array $refundedStates;

    #[ORM\Column(type: Types::JSON)]
    private array $pendingStates;

    #[ORM\Column(type: Types::JSON)]
    private array $validStateChanges;

    #[ORM\Column(type: Types::FLOAT)]
    private float $amount;

    #[ORM\Column(length: 16)]
    private string $type;

    #[ORM\Column(length: 255)]
    private string $state;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isOnline;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $externalId;

    #[ORM\Column(length: 1000, unique: true, nullable: true)]
    private ?string $externalUrl;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'applicationPayments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Application $application;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(float                      $amount,
                                ApplicationPaymentTypeEnum $type,
                                string                     $state,
                                bool                       $isOnline,
                                Application                $application,
                                array                      $states,
                                array                      $paidStates,
                                array                      $cancelledStates,
                                array                      $refundedStates,
                                array                      $pendingStates,
                                array                      $validStateChanges,
                                ?string                    $externalId = null,
                                ?string                    $externalUrl = null)
    {
        $this->id = Uuid::v4();
        $this->amount = $amount;
        $this->type = $type->value;
        $this->isOnline = $isOnline;
        $this->application = $application;
        $this->states = $states;
        $this->paidStates = $paidStates;
        $this->cancelledStates = $cancelledStates;
        $this->refundedStates = $refundedStates;
        $this->pendingStates = $pendingStates;
        $this->validStateChanges = $validStateChanges;
        $this->externalId = $externalId;
        $this->externalUrl = $externalUrl;
        $this->createdAt = new DateTimeImmutable('now');
        $this->setState($state);
        $application->addApplicationPayment($this);
        $this->assertValidStates();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getStates(): array
    {
        return $this->states;
    }

    public function getPaidStates(): array
    {
        return $this->paidStates;
    }

    public function getCancelledStates(): array
    {
        return $this->cancelledStates;
    }

    public function getRefundedStates(): array
    {
        return $this->refundedStates;
    }

    public function getPendingStates(): array
    {
        return $this->pendingStates;
    }

    public function getValidStateChanges(): array
    {
        return $this->validStateChanges;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): ApplicationPaymentTypeEnum
    {
        return ApplicationPaymentTypeEnum::tryFrom($this->type);
    }

    public function setType(ApplicationPaymentTypeEnum $type): self
    {
        $this->type = $type->value;

        return $this;
    }

    public function isPaid(): bool
    {
        return in_array($this->state, $this->paidStates);
    }

    public function isCancelled(): bool
    {
        return in_array($this->state, $this->cancelledStates);
    }

    public function isRefunded(): bool
    {
        return in_array($this->state, $this->refundedStates);
    }

    public function isPending(): bool
    {
        return in_array($this->state, $this->pendingStates);
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCurrentValidStateChanges(): array
    {
        $validStateChanges = $this->validStateChanges;

        if (!array_key_exists($this->state, $validStateChanges))
        {
            return [];
        }

        return $validStateChanges[$this->state];
    }

    public function canChangeToState(string $state): bool
    {
        $validStateChanges = $this->validStateChanges;

        return array_key_exists($this->state, $validStateChanges) && in_array($state, $validStateChanges[$this->state]);
    }

    public function setState(string $newState): self
    {
        $this->assertValidState($this->state ?? null, $newState);
        $this->state = $newState;

        return $this;
    }

    public function isOnline(): bool
    {
        return $this->isOnline;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
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

    private function assertValidState(?string $currentState, string $newState): void
    {
        $idString = $this->id->toRfc4122();
        $validStates = $this->states;
        $validStateChanges = $this->validStateChanges;

        if (!in_array($newState, $validStates))
        {
            $validStatesString = implode(', ', $this->states);

            throw new LogicException(
                sprintf('State "%s" is invalid in entity %s (ID: %s). Valid states are: %s.', $newState, self::class, $idString, $validStatesString)
            );
        }

        if ($currentState !== null)
        {
            $currentStates = array_keys($validStateChanges);

            if (!in_array($currentState, $currentStates) || !in_array($newState, $validStateChanges[$currentState]))
            {
                $validNewStatesString = implode(', ', $validStateChanges[$currentState]);

                throw new LogicException(
                    sprintf('%s (ID: %s) cannot be set from current state "%s" to new state "%s". Valid new states are: %s.', self::class, $idString, $currentState, $newState, $validNewStatesString)
                );
            }
        }
    }

    private function assertValidStates(): void
    {
        $idString = $this->id->toRfc4122();
        $stateAttributesToValidate = ['paidStates', 'cancelledStates', 'refundedStates', 'pendingStates'];

        foreach ($stateAttributesToValidate as $stateAttributeToValidate)
        {
            if (empty($this->$stateAttributeToValidate))
            {
                throw new LogicException(
                    sprintf('Array "%s" passed to %s (ID: %s) must not be empty.', $stateAttributeToValidate, self::class, $idString)
                );
            }

            foreach ($this->$stateAttributeToValidate as $state)
            {
                if (!in_array($state, $this->states))
                {
                    $validStatesString = implode(', ', $this->states);

                    throw new LogicException(
                        sprintf('Value "%s" in array "%s" passed to %s (ID: %s) is not valid. Valid states are: %s.', $state, $stateAttributeToValidate, self::class, $idString, $validStatesString)
                    );
                }
            }
        }

        foreach ($this->validStateChanges as $currentState => $newStates)
        {
            if (!in_array($currentState, $this->states))
            {
                $validStatesString = implode(', ', $this->states);

                throw new LogicException(
                    sprintf('Array "validStateChanges" in %s (ID: %s) has an invalid state "%s" as a key. Valid states are: %s.', self::class, $idString, $currentState, $validStatesString)
                );
            }

            foreach ($newStates as $newState)
            {
                if (!in_array($newState, $this->states))
                {
                    throw new LogicException(
                        sprintf('Array "validStateChanges" in %s (ID: %s) has an invalid state change "%s" -> "%s".', self::class, $idString, $currentState, $newState)
                    );
                }
            }
        }
    }
}