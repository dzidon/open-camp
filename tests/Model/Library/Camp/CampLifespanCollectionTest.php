<?php

namespace App\Tests\Model\Library\Camp;

use App\Model\Library\Camp\CampLifespan;
use App\Model\Library\Camp\CampLifespanCollection;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CampLifespanCollectionTest extends TestCase
{
    private CampLifespanCollection $campLifespanCollection;

    public function testCampLifespans(): void
    {
        $this->assertSame([], $this->campLifespanCollection->getCampLifespans());

        $campLifespan = new CampLifespan(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'));
        $this->campLifespanCollection->addCampLifespan('abc', $campLifespan);
        $this->assertContains($campLifespan, $this->campLifespanCollection->getCampLifespans());

        $this->campLifespanCollection->removeCampLifespan($campLifespan);
        $this->assertNotContains($campLifespan, $this->campLifespanCollection->getCampLifespans());

        $this->campLifespanCollection->addCampLifespan('abc', $campLifespan);
        $retrievedCampLifespan = $this->campLifespanCollection->getCampLifespan('abc');
        $this->assertSame($campLifespan, $retrievedCampLifespan);

        $this->campLifespanCollection->removeCampLifespan('abc');
        $this->assertNotContains($campLifespan, $this->campLifespanCollection->getCampLifespans());
    }

    protected function setUp(): void
    {
        $this->campLifespanCollection = new CampLifespanCollection();
    }
}