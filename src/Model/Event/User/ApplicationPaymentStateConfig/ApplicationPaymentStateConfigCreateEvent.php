<?php

namespace App\Model\Event\User\ApplicationPaymentStateConfig;

use App\Model\Entity\ApplicationPaymentStateConfig;
use App\Model\Event\AbstractModelEvent;

class ApplicationPaymentStateConfigCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_payment_state_config.create';

    private array $states;

    private array $paidStates;

    private array $cancelledStates;

    private array $refundedStates;

    private array $pendingStates;

    private array $validStateChanges;

    private ?ApplicationPaymentStateConfig $applicationPaymentStateConfig = null;

    public function __construct(array $states,
                                array $paidStates,
                                array $cancelledStates,
                                array $refundedStates,
                                array $pendingStates,
                                array $validStateChanges)
    {
        $this->states = $states;
        $this->paidStates = $paidStates;
        $this->cancelledStates = $cancelledStates;
        $this->refundedStates = $refundedStates;
        $this->pendingStates = $pendingStates;
        $this->validStateChanges = $validStateChanges;
    }

    public function getStates(): array
    {
        return $this->states;
    }

    public function setStates(array $states): self
    {
        $this->states = $states;

        return $this;
    }

    public function getPaidStates(): array
    {
        return $this->paidStates;
    }

    public function setPaidStates(array $paidStates): self
    {
        $this->paidStates = $paidStates;

        return $this;
    }

    public function getCancelledStates(): array
    {
        return $this->cancelledStates;
    }

    public function setCancelledStates(array $cancelledStates): self
    {
        $this->cancelledStates = $cancelledStates;

        return $this;
    }

    public function getRefundedStates(): array
    {
        return $this->refundedStates;
    }

    public function setRefundedStates(array $refundedStates): self
    {
        $this->refundedStates = $refundedStates;

        return $this;
    }

    public function getPendingStates(): array
    {
        return $this->pendingStates;
    }

    public function setPendingStates(array $pendingStates): self
    {
        $this->pendingStates = $pendingStates;

        return $this;
    }

    public function getValidStateChanges(): array
    {
        return $this->validStateChanges;
    }

    public function setValidStateChanges(array $validStateChanges): self
    {
        $this->validStateChanges = $validStateChanges;

        return $this;
    }

    public function getApplicationPaymentStateConfig(): ?ApplicationPaymentStateConfig
    {
        return $this->applicationPaymentStateConfig;
    }

    public function setApplicationPaymentStateConfig(?ApplicationPaymentStateConfig $applicationPaymentStateConfig): self
    {
        $this->applicationPaymentStateConfig = $applicationPaymentStateConfig;

        return $this;
    }
}