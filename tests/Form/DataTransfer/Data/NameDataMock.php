<?php

namespace App\Tests\Form\DataTransfer\Data;

class NameDataMock
{
    private string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = (string) $name;
    }
}