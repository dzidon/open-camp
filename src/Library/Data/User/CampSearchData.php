<?php

namespace App\Library\Data\User;

use DateTimeImmutable;

class CampSearchData
{
    private string $phrase = '';

    private ?int $age = null;

    private ?DateTimeImmutable $from = null;

    private ?DateTimeImmutable $to = null;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getFrom(): ?DateTimeImmutable
    {
        return $this->from;
    }

    public function setFrom(?DateTimeImmutable $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?DateTimeImmutable
    {
        return $this->to;
    }

    public function setTo(?DateTimeImmutable $to): self
    {
        $this->to = $to;

        return $this;
    }
}