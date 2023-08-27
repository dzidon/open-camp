<?php

namespace App\Library\Data\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class CampImageData
{
    #[Assert\NotBlank]
    private ?int $priority = 0;

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}