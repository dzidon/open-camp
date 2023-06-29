<?php

namespace App\Tests\Form\DataTransfer\Data;

class LabelDataMock
{
    private string $label = '';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = (string) $label;
    }
}