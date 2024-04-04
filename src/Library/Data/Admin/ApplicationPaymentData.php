<?php

namespace App\Library\Data\Admin;

use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationPaymentData
{
    private array $validStates;

    #[Assert\GreaterThanOrEqual(0.0)]
    #[Assert\NotBlank]
    private ?float $amount = null;

    #[Assert\NotBlank]
    private ?ApplicationPaymentTypeEnum $type = null;

    #[Assert\Choice(callback: 'getValidStates')]
    #[Assert\NotBlank]
    private ?string $state = null;

    public function __construct(array $validStates)
    {
        $this->validStates = $validStates;
    }

    public function getValidStates(): array
    {
        return $this->validStates;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): ?ApplicationPaymentTypeEnum
    {
        return $this->type;
    }

    public function setType(?ApplicationPaymentTypeEnum $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): ApplicationPaymentData
    {
        $this->state = $state;

        return $this;
    }
}