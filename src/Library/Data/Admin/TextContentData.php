<?php

namespace App\Library\Data\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class TextContentData
{
    #[Assert\Length(max: 5000)]
    #[Assert\NotBlank]
    private ?string $content = null;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }
}