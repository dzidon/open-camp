<?php

namespace App\Library\Data\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class FileExtensionData
{
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $extension = null;

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }
}