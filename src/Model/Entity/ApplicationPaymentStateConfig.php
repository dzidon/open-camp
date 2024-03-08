<?php

namespace App\Model\Entity;

use App\Model\Repository\ApplicationPaymentStateConfigRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\ORM\Mapping as ORM;

/**
 * Application payment config entity.
 */
#[ORM\Entity(repositoryClass: ApplicationPaymentStateConfigRepository::class)]
class ApplicationPaymentStateConfig
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

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public function __construct(array $states,
                                array $paidStates,
                                array $cancelledStates,
                                array $refundedStates,
                                array $pendingStates,
                                array $validStateChanges)
    {
        $this->id = Uuid::v4();
        $this->states = $states;
        $this->paidStates = $paidStates;
        $this->cancelledStates = $cancelledStates;
        $this->refundedStates = $refundedStates;
        $this->pendingStates = $pendingStates;
        $this->validStateChanges = $validStateChanges;
        $this->createdAt = new DateTimeImmutable('now');

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
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