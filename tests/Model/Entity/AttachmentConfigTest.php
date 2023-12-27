<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\FileExtension;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class AttachmentConfigTest extends TestCase
{
    private const NAME = 'Name';
    private const LABEL = 'Label';
    private const MAX_SIZE = 10.0;

    private AttachmentConfig $attachmentConfig;

    public function testId(): void
    {
        $id = $this->attachmentConfig->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->attachmentConfig->getName());

        $newName = 'New Name';
        $this->attachmentConfig->setName($newName);
        $this->assertSame($newName, $this->attachmentConfig->getName());
    }

    public function testLabel(): void
    {
        $this->assertSame(self::LABEL, $this->attachmentConfig->getLabel());

        $newLabel = 'New Label';
        $this->attachmentConfig->setLabel($newLabel);
        $this->assertSame($newLabel, $this->attachmentConfig->getLabel());
    }

    public function testHelp(): void
    {
        $this->assertNull($this->attachmentConfig->getHelp());

        $this->attachmentConfig->setHelp('text');
        $this->assertSame('text', $this->attachmentConfig->getHelp());

        $this->attachmentConfig->setHelp(null);
        $this->assertNull($this->attachmentConfig->getHelp());
    }

    public function testMaxSize(): void
    {
        $this->assertSame(self::MAX_SIZE, $this->attachmentConfig->getMaxSize());

        $newMaxSize = 15.0;
        $this->attachmentConfig->setMaxSize($newMaxSize);
        $this->assertSame($newMaxSize, $this->attachmentConfig->getMaxSize());
    }

    public function testRequiredType(): void
    {
        $this->assertSame(AttachmentConfigRequiredTypeEnum::OPTIONAL, $this->attachmentConfig->getRequiredType());

        $newRequiredType = AttachmentConfigRequiredTypeEnum::REQUIRED;
        $this->attachmentConfig->setRequiredType($newRequiredType);
        $this->assertSame($newRequiredType, $this->attachmentConfig->getRequiredType());
    }

    public function testFileExtensions(): void
    {
        $this->assertEmpty($this->attachmentConfig->getFileExtensions());

        $fileExtensions = new FileExtension('png');
        $this->attachmentConfig->addFileExtension($fileExtensions);
        $this->assertContains($fileExtensions, $this->attachmentConfig->getFileExtensions());

        $this->attachmentConfig->removeFileExtension($fileExtensions);
        $this->assertEmpty($this->attachmentConfig->getFileExtensions());
    }

    public function testIsGlobal(): void
    {
        $this->assertFalse($this->attachmentConfig->isGlobal());

        $this->attachmentConfig->setIsGlobal(true);
        $this->assertTrue($this->attachmentConfig->isGlobal());

        $this->attachmentConfig->setIsGlobal(false);
        $this->assertFalse($this->attachmentConfig->isGlobal());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->attachmentConfig->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->attachmentConfig->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->attachmentConfig = new AttachmentConfig(self::NAME, self::LABEL, self::MAX_SIZE);
    }
}