<?php

namespace App\Library\Data\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class DiscountRecurringCamperConfigData
{
    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\When(
        expression: 'this.getTo() === null',
        constraints: [
            new Assert\NotBlank(message: 'min_or_mar_mandatory'),
        ],
    )]
    private ?int $from = null;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\When(
        expression: 'this.getFrom() === null',
        constraints: [
            new Assert\NotBlank(message: 'min_or_mar_mandatory'),
        ],
    )]
    #[Assert\When(
        expression: 'this.getFrom() !== null && this.getTo() !== null',
        constraints: [
            new Assert\GreaterThanOrEqual(propertyPath: 'from')
        ],
    )]
    private ?int $to = null;

    #[Assert\GreaterThan(0.0)]
    #[Assert\NotBlank]
    private ?float $discount = null;

    public function getFrom(): ?int
    {
        return $this->from;
    }

    public function setFrom(?int $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?int
    {
        return $this->to;
    }

    public function setTo(?int $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): self
    {
        $this->discount = $discount;

        return $this;
    }
}