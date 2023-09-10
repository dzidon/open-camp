<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class TripLocationTest extends TestCase
{
    private TripLocationPath $tripLocationPath;
    private TripLocation $tripLocation;

    private const NAME = 'Location';
    private const PRICE = 1000.0;
    private const PRIORITY = 100;

    public function testId(): void
    {
        $id = $this->tripLocation->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->tripLocation->getName());

        $newName = 'New location';
        $this->tripLocation->setName($newName);
        $this->assertSame($newName, $this->tripLocation->getName());
    }

    public function testPrice(): void
    {
        $this->assertSame(self::PRICE, $this->tripLocation->getPrice());

        $this->tripLocation->setPrice(1500.0);
        $this->assertSame(1500.0, $this->tripLocation->getPrice());
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->tripLocation->getPriority());

        $this->tripLocation->setPriority(150);
        $this->assertSame(150, $this->tripLocation->getPriority());
    }

    public function testTripLocationPath(): void
    {
        $this->assertSame($this->tripLocationPath, $this->tripLocation->getTripLocationPath());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->tripLocation->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->tripLocation->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->tripLocationPath = new TripLocationPath('Path');
        $this->tripLocation = new TripLocation(self::NAME, self::PRICE, self::PRIORITY, $this->tripLocationPath);
    }
}