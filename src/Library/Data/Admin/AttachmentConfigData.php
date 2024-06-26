<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueAttachmentConfig;
use App\Model\Entity\AttachmentConfig;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueAttachmentConfig]
class AttachmentConfigData
{
    private ?AttachmentConfig $attachmentConfig;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[Assert\Length(max: 255)]
    private ?string $help = null;

    #[Assert\GreaterThan(0.0)]
    #[Assert\NotBlank]
    private ?float $maxSize = null;

    #[Assert\NotBlank]
    private ?AttachmentConfigRequiredTypeEnum $requiredType = null;

    /**
     * @var FileExtensionData[]
     */
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'file_extensions_mandatory')]
    private array $fileExtensionsData = [];

    private bool $isGlobal = false;

    public function __construct(?AttachmentConfig $attachmentConfig = null)
    {
        $this->attachmentConfig = $attachmentConfig;
    }

    public function getAttachmentConfig(): ?AttachmentConfig
    {
        return $this->attachmentConfig;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function setHelp(?string $help): self
    {
        $this->help = $help;

        return $this;
    }

    public function getMaxSize(): ?float
    {
        return $this->maxSize;
    }

    public function setMaxSize(?float $maxSize): self
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    public function getRequiredType(): ?AttachmentConfigRequiredTypeEnum
    {
        return $this->requiredType;
    }

    public function setRequiredType(?AttachmentConfigRequiredTypeEnum $requiredType): self
    {
        $this->requiredType = $requiredType;

        return $this;
    }

    public function getFileExtensionsData(): array
    {
        return $this->fileExtensionsData;
    }

    public function setFileExtensionsData(array $fileExtensionsData): self
    {
        foreach ($fileExtensionsData as $fileExtensionData)
        {
            if (!$fileExtensionData instanceof FileExtensionData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, FileExtensionData::class)
                );
            }
        }

        $this->fileExtensionsData = $fileExtensionsData;

        return $this;
    }
    
    public function addFileExtensionData(FileExtensionData $fileExtensionData): self
    {
        if (in_array($fileExtensionData, $this->fileExtensionsData, true))
        {
            return $this;
        }

        $this->fileExtensionsData[] = $fileExtensionData;

        return $this;
    }

    public function removeFileExtensionData(FileExtensionData $fileExtensionData): self
    {
        $key = array_search($fileExtensionData, $this->fileExtensionsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->fileExtensionsData[$key]);

        return $this;
    }

    public function isGlobal(): bool
    {
        return $this->isGlobal;
    }

    public function setIsGlobal(bool $isGlobal): self
    {
        $this->isGlobal = $isGlobal;

        return $this;
    }
}