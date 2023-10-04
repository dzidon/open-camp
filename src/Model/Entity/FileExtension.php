<?php

namespace App\Model\Entity;

use App\Model\Repository\FileExtensionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\ORM\Mapping as ORM;

/**
 * File extension entity.
 */
#[ORM\Entity(repositoryClass: FileExtensionRepository::class)]
class FileExtension
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $extension;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public function __construct(string $extension)
    {
        $this->id = Uuid::v4();
        $this->extension = $extension;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}