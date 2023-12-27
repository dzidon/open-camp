<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateAttachmentConfig;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class CampDateAttachmentConfigTest extends TestCase
{
    private const PRIORITY = 100;

    private Camp $camp;
    private CampDate $campDate;
    private AttachmentConfig $attachmentConfig;
    private CampDateAttachmentConfig $campDateAttachmentConfig;

    public function testId(): void
    {
        $id = $this->campDateAttachmentConfig->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testCampDate(): void
    {
        $this->assertSame($this->campDate, $this->campDateAttachmentConfig->getCampDate());
        $this->assertContains($this->campDateAttachmentConfig, $this->campDate->getCampDateAttachmentConfigs());

        $campDateNew = new CampDate(new DateTimeImmutable('2000-01-02'), new DateTimeImmutable('2000-01-08'), 2000.0, 200.0, 20, $this->camp);
        $this->campDateAttachmentConfig->setCampDate($campDateNew);

        $this->assertSame($campDateNew, $this->campDateAttachmentConfig->getCampDate());
        $this->assertContains($this->campDateAttachmentConfig, $campDateNew->getCampDateAttachmentConfigs());
        $this->assertNotContains($this->campDateAttachmentConfig, $this->campDate->getCampDateAttachmentConfigs());
    }

    public function testAttachmentConfig(): void
    {
        $this->assertSame($this->attachmentConfig, $this->campDateAttachmentConfig->getAttachmentConfig());

        $attachmentConfigNew = new AttachmentConfig('New config', 'Label', 20.0);
        $this->campDateAttachmentConfig->setAttachmentConfig($attachmentConfigNew);
        $this->assertSame($attachmentConfigNew, $this->campDateAttachmentConfig->getAttachmentConfig());
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->campDateAttachmentConfig->getPriority());

        $newPriority = 200;
        $this->campDateAttachmentConfig->setPriority($newPriority);
        $this->assertSame($newPriority, $this->campDateAttachmentConfig->getPriority());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->campDateAttachmentConfig->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->campDateAttachmentConfig->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000.0, 100.0, 10, $this->camp);
        $this->attachmentConfig = new AttachmentConfig('Config', 'Label', 10.0);
        $this->campDateAttachmentConfig = new CampDateAttachmentConfig($this->campDate, $this->attachmentConfig, 100);
    }
}