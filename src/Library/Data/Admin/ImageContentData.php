<?php

namespace App\Library\Data\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class ImageContentData
{
    #[Assert\Length(max: 2000, maxMessage: 'image_content_url_too_long')]
    #[Assert\Url(message: 'image_content_url_invalid')]
    private ?string $url = null;

    #[Assert\Length(max: 64, maxMessage: 'image_content_alt_too_long')]
    #[Assert\NotBlank(message: 'image_content_alt_mandatory')]
    private ?string $alt = null;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }
}