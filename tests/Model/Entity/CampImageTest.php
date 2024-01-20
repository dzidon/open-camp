<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class CampImageTest extends TestCase
{
    private const PRIORITY = 100;
    private const EXTENSION = 'png';

    private Camp $camp;
    private CampImage $campImage;

    public function testId(): void
    {
        $id = $this->campImage->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->campImage->getPriority());

        $this->campImage->setPriority(200);
        $this->assertSame(200, $this->campImage->getPriority());
    }

    public function testExtension(): void
    {
        $this->assertSame(self::EXTENSION, $this->campImage->getExtension());

        $this->campImage->setExtension('jpg');
        $this->assertSame('jpg', $this->campImage->getExtension());
    }

    public function testCamp(): void
    {
        $this->assertSame($this->camp, $this->campImage->getCamp());

        $campNew = new Camp('Camp new', 'camp-new', 5, 10, 321);
        $this->campImage->setCamp($campNew);

        $this->assertSame($campNew, $this->campImage->getCamp());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->campImage->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->campImage->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 321);

        $this->campImage = new CampImage(self::PRIORITY, self::EXTENSION, $this->camp);
    }
}