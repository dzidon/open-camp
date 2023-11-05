<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\FormField;
use Symfony\Component\Validator\Constraints as Assert;

class CampDateFormFieldData
{
    #[Assert\NotBlank]
    private ?FormField $formField = null;

    #[Assert\NotBlank]
    private ?int $priority = 0;

    public function getFormField(): ?FormField
    {
        return $this->formField;
    }

    public function setFormField(?FormField $formField): self
    {
        $this->formField = $formField;

        return $this;
    }

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