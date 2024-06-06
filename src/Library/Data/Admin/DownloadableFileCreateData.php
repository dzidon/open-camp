<?php

namespace App\Library\Data\Admin;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class DownloadableFileCreateData
{
    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[Assert\Length(max: 128)]
    private ?string $description = null;

    #[Assert\NotBlank]
    private ?int $priority = 0;

    #[Assert\NotBlank]
    private ?File $file = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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