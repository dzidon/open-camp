<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\TripLocationPath;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class TripLocationPathTest extends TestCase
{
    private const NAME = 'Path';

    private TripLocationPath $tripLocationPath;

    public function testId(): void
    {
        $id = $this->tripLocationPath->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->tripLocationPath->getName());

        $newName = 'New path';
        $this->tripLocationPath->setName($newName);
        $this->assertSame($newName, $this->tripLocationPath->getName());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->tripLocationPath->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->tripLocationPath->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->tripLocationPath = new TripLocationPath(self::NAME);
    }
}