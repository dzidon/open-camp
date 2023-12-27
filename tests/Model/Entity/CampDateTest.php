<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\TripLocationPath;
use App\Model\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class CampDateTest extends TestCase
{
    private const START_AT = '2000-01-01 12:00:00';
    private const END_AT = '2000-01-07 12:00:00';
    private const DEPOSIT = 1000.0;
    private const PRICE_WITHOUT_DEPOSIT = 2000.0;
    private const CAPACITY = 10;

    private Camp $camp;
    private CampDate $campDate;

    public function testId(): void
    {
        $id = $this->campDate->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testStartAt(): void
    {
        $startAt = $this->campDate->getStartAt();
        $this->assertSame(self::START_AT, $startAt->format('Y-m-d H:i:s'));

        $newStartAtString = '2000-01-02 16:00:00';
        $newStartAt = new DateTimeImmutable($newStartAtString);
        $this->campDate->setStartAt($newStartAt);
        $startAt = $this->campDate->getStartAt();
        $this->assertSame($newStartAtString, $startAt->format('Y-m-d H:i:s'));
    }

    public function testEndAt(): void
    {
        $endAt = $this->campDate->getEndAt();
        $this->assertSame(self::END_AT, $endAt->format('Y-m-d H:i:s'));

        $newEndAtString = '2000-01-09 16:00:00';
        $newEndAt = new DateTimeImmutable($newEndAtString);
        $this->campDate->setEndAt($newEndAt);
        $endAt = $this->campDate->getEndAt();
        $this->assertSame($newEndAtString, $endAt->format('Y-m-d H:i:s'));
    }

    public function testDeposit(): void
    {
        $this->assertSame(self::DEPOSIT, $this->campDate->getDeposit());

        $this->campDate->setDeposit(1500.0);
        $this->assertSame(1500.0, $this->campDate->getDeposit());
    }

    public function testPriceWithoutDeposit(): void
    {
        $this->assertSame(self::PRICE_WITHOUT_DEPOSIT, $this->campDate->getPriceWithoutDeposit());

        $this->campDate->setPriceWithoutDeposit(1500.0);
        $this->assertSame(1500.0, $this->campDate->getPriceWithoutDeposit());
    }

    public function testCapacity(): void
    {
        $this->assertSame(self::CAPACITY, $this->campDate->getCapacity());

        $this->campDate->setCapacity(20);
        $this->assertSame(20, $this->campDate->getCapacity());
    }

    public function testIsClosed(): void
    {
        $this->assertFalse($this->campDate->isClosed());

        $this->campDate->setIsClosed(true);
        $this->assertTrue($this->campDate->isClosed());
    }

    public function testIsOpenAboveCapacity(): void
    {
        $this->assertFalse($this->campDate->isOpenAboveCapacity());

        $this->campDate->setIsOpenAboveCapacity(true);
        $this->assertTrue($this->campDate->isOpenAboveCapacity());
    }

    public function testDescription(): void
    {
        $this->assertNull($this->campDate->getDescription());

        $this->campDate->setDescription('text');
        $this->assertSame('text', $this->campDate->getDescription());

        $this->campDate->setDescription(null);
        $this->assertNull($this->campDate->getDescription());
    }

    public function testCamp(): void
    {
        $this->assertSame($this->camp, $this->campDate->getCamp());

        $campNew = new Camp('Camp new', 'camp-new', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->campDate->setCamp($campNew);

        $this->assertSame($campNew, $this->campDate->getCamp());
    }

    public function testLeaders(): void
    {
        $this->assertEmpty($this->campDate->getLeaders());

        $user = new User('bob@gmail.com');
        $this->campDate->addLeader($user);
        $this->assertContains($user, $this->campDate->getLeaders());

        $this->campDate->removeLeader($user);
        $this->assertEmpty($this->campDate->getLeaders());
    }

    public function testTripLocationPathThere(): void
    {
        $this->assertNull($this->campDate->getTripLocationPathThere());

        $tripLocationPath = new TripLocationPath('Path');
        $this->campDate->setTripLocationPathThere($tripLocationPath);
        $this->assertSame($tripLocationPath, $this->campDate->getTripLocationPathThere());

        $this->campDate->setTripLocationPathThere(null);
        $this->assertNull($this->campDate->getTripLocationPathThere());
    }

    public function testTripLocationPathBack(): void
    {
        $this->assertNull($this->campDate->getTripLocationPathBack());

        $tripLocationPath = new TripLocationPath('Path');
        $this->campDate->setTripLocationPathBack($tripLocationPath);
        $this->assertSame($tripLocationPath, $this->campDate->getTripLocationPathBack());

        $this->campDate->setTripLocationPathBack(null);
        $this->assertNull($this->campDate->getTripLocationPathBack());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->campDate->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->campDate->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);

        $startAt = new DateTimeImmutable(self::START_AT);
        $endAt = new DateTimeImmutable(self::END_AT);
        $this->campDate = new CampDate($startAt, $endAt, self::DEPOSIT, self::PRICE_WITHOUT_DEPOSIT, self::CAPACITY, $this->camp);
    }
}