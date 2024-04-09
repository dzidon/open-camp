<?php

namespace App\Library\Data\Admin;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationAdminAttachmentCreateData
{
    #[Assert\NotBlank]
    private ?string $label = null;

    #[Assert\NotBlank]
    private ?File $file = null;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }
}