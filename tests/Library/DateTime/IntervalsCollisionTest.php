<?php

namespace App\Tests\Library\DateTime;

use App\Library\DateTime\IntervalsCollision;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class IntervalsCollisionTest extends TestCase
{
    public function testIsFoundOverlapLeft(): void
    {
        $from1 = new DateTimeImmutable('2000-01-01');
        $to1 = new DateTimeImmutable('2000-01-07');
        $from2 = new DateTimeImmutable('2000-01-05');
        $to2 = new DateTimeImmutable('2000-01-10');
        $collision = new IntervalsCollision($from1, $to1, $from2, $to2);

        $this->assertTrue($collision->isFound());
    }

    public function testIsFoundOverlapRight(): void
    {
        $from1 = new DateTimeImmutable('2000-01-05');
        $to1 = new DateTimeImmutable('2000-01-10');
        $from2 = new DateTimeImmutable('2000-01-01');
        $to2 = new DateTimeImmutable('2000-01-07');
        $collision = new IntervalsCollision($from1, $to1, $from2, $to2);

        $this->assertTrue($collision->isFound());
    }

    public function testIsFoundOverlapInsideFirst(): void
    {
        $from1 = new DateTimeImmutable('2000-01-06');
        $to1 = new DateTimeImmutable('2000-01-09');
        $from2 = new DateTimeImmutable('2000-01-05');
        $to2 = new DateTimeImmutable('2000-01-10');
        $collision = new IntervalsCollision($from1, $to1, $from2, $to2);

        $this->assertTrue($collision->isFound());
    }

    public function testIsFoundOverlapInsideSecond(): void
    {
        $from1 = new DateTimeImmutable('2000-01-05');
        $to1 = new DateTimeImmutable('2000-01-10');
        $from2 = new DateTimeImmutable('2000-01-06');
        $to2 = new DateTimeImmutable('2000-01-09');
        $collision = new IntervalsCollision($from1, $to1, $from2, $to2);

        $this->assertTrue($collision->isFound());
    }

    public function testIsFoundEqualStartAndEndLeft(): void
    {
        $from1 = new DateTimeImmutable('2000-01-01');
        $to1 = new DateTimeImmutable('2000-01-05');
        $from2 = new DateTimeImmutable('2000-01-05');
        $to2 = new DateTimeImmutable('2000-01-10');
        $collision = new IntervalsCollision($from1, $to1, $from2, $to2);

        $this->assertTrue($collision->isFound());
    }

    public function testIsFoundEqualStartAndEndRight(): void
    {
        $from1 = new DateTimeImmutable('2000-01-05');
        $to1 = new DateTimeImmutable('2000-01-10');
        $from2 = new DateTimeImmutable('2000-01-01');
        $to2 = new DateTimeImmutable('2000-01-05');
        $collision = new IntervalsCollision($from1, $to1, $from2, $to2);

        $this->assertTrue($collision->isFound());
    }

    public function testIsFoundEqualStartAndEndLeftOpen(): void
    {
        $from1 = new DateTimeImmutable('2000-01-01');
        $to1 = new DateTimeImmutable('2000-01-05');
        $from2 = new DateTimeImmutable('2000-01-05');
        $to2 = new DateTimeImmutable('2000-01-10');
        $collision = new IntervalsCollision($from1, $to1, $from2, $to2, false);

        $this->assertFalse($collision->isFound());
    }

    public function testIsFoundEqualStartAndEndRightOpen(): void
    {
        $from1 = new DateTimeImmutable('2000-01-05');
        $to1 = new DateTimeImmutable('2000-01-10');
        $from2 = new DateTimeImmutable('2000-01-01');
        $to2 = new DateTimeImmutable('2000-01-05');
        $collision = new IntervalsCollision($from1, $to1, $from2, $to2, false);

        $this->assertFalse($collision->isFound());
    }
}