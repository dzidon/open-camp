<?php

namespace App\Library\Data\Common;

use Symfony\Component\Validator\Constraints as Assert;

class ApplicationPurchasableItemVariantData
{
    private string $label;

    private array $validValues;

    #[Assert\Choice(callback: 'getValidValues')]
    private ?string $value = null;

    public function __construct(string $label, array $validValues = [])
    {
        $this->label = $label;
        $this->validValues = $validValues;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValidValues(): array
    {
        return $this->validValues;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}