<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueAttachmentConfig;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueAttachmentConfig]
class AttachmentConfigData
{
    private ?UuidV4 $id = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

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

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function setId(?UuidV4 $id): self
    {
        $this->id = $id;

        return $this;
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

    public function addFileExtensionsDatum(FileExtensionData $fileExtensionData): self
    {
        if (in_array($fileExtensionData, $this->fileExtensionsData, true))
        {
            return $this;
        }

        $this->fileExtensionsData[] = $fileExtensionData;

        return $this;
    }

    public function removeFileExtensionsDatum(FileExtensionData $fileExtensionData): self
    {
        $key = array_search($fileExtensionData, $this->fileExtensionsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->fileExtensionsData[$key]);

        return $this;
    }
}