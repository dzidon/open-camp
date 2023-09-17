<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\FileExtension;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class FileExtensionTest extends TestCase
{
    private const EXTENSION = 'png';

    private FileExtension $fileExtension;

    public function testId(): void
    {
        $id = $this->fileExtension->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testExtension(): void
    {
        $this->assertSame(self::EXTENSION, $this->fileExtension->getExtension());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->fileExtension->getCreatedAt()->getTimestamp());
    }

    protected function setUp(): void
    {
        $this->fileExtension = new FileExtension(self::EXTENSION);
    }
}