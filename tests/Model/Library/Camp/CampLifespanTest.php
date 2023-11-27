<?php

namespace App\Tests\Model\Library\Camp;

use App\Model\Library\Camp\CampLifespan;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CampLifespanTest extends TestCase
{
    private CampLifespan $campLifespan;

    public function testStartAt(): void
    {
        $startAt = $this->campLifespan->getStartAt();
        $this->assertSame((new DateTimeImmutable('2000-01-01'))->getTimestamp(), $startAt->getTimestamp());
    }

    public function testEndAt(): void
    {
        $endAt = $this->campLifespan->getEndAt();
        $this->assertSame((new DateTimeImmutable('2000-01-07'))->getTimestamp(), $endAt->getTimestamp());
    }

    protected function setUp(): void
    {
        $this->campLifespan = new CampLifespan(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'));
    }
}