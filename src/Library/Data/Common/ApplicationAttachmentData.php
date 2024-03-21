<?php

namespace App\Library\Data\Common;

use App\Library\Constraint\ApplicationAttachmentFile;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ApplicationAttachmentFile]
class ApplicationAttachmentData
{
    private float $maxSize; // MB

    private string $requiredType;

    private array $extensions;

    private bool $isAlreadyUploaded;

    private int $priority;

    private string $label;

    private ?string $help;

    private bool $suppressRequiredLaterHelp;

    #[Assert\When(
        expression: '!this.isAlreadyUploaded() and this.getRequiredType() === enum("App\\\Model\\\Enum\\\Entity\\\AttachmentConfigRequiredTypeEnum::REQUIRED")',
        constraints: [
            new Assert\NotBlank(),
        ]
    )]
    private ?File $file = null;

    public function __construct(float                            $maxSize,
                                AttachmentConfigRequiredTypeEnum $requiredType,
                                array                            $extensions,
                                bool                             $isAlreadyUploaded,
                                int                              $priority,
                                string                           $label,
                                ?string                          $help = null,
                                bool                             $suppressRequiredLaterHelp = false)
    {
        $this->maxSize = $maxSize;
        $this->requiredType = $requiredType->value;
        $this->extensions = $extensions;
        $this->isAlreadyUploaded = $isAlreadyUploaded;
        $this->priority = $priority;
        $this->label = $label;
        $this->help = $help;
        $this->suppressRequiredLaterHelp = $suppressRequiredLaterHelp;
    }

    public function getMaxSize(): float
    {
        return $this->maxSize;
    }

    public function isRequiredLater(): bool
    {
        return $this->getRequiredType() === AttachmentConfigRequiredTypeEnum::REQUIRED_LATER;
    }

    public function getRequiredType(): AttachmentConfigRequiredTypeEnum
    {
        return AttachmentConfigRequiredTypeEnum::tryFrom($this->requiredType);
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function isAlreadyUploaded(): float
    {
        return $this->isAlreadyUploaded;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function suppressRequiredLaterHelp(): bool
    {
        return $this->suppressRequiredLaterHelp;
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