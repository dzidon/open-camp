<?php

namespace App\Tests\Library\Data\User;

use App\Library\Data\User\CampSearchData;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampSearchDataTest extends KernelTestCase
{
    public function testPhrase(): void
    {
        $data = new CampSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testAge(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getAge());

        $data->setAge(2);
        $this->assertSame(2, $data->getAge());

        $data->setAge(null);
        $this->assertNull($data->getAge());
    }

    public function testStartAt(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getFrom());
        $this->assertFalse($data->isOpenOnly());

        $expectedDateFrom = new DateTimeImmutable('now');
        $data->setFrom($expectedDateFrom);
        $this->assertSame($expectedDateFrom, $data->getFrom());
        $this->assertTrue($data->isOpenOnly());

        $data->setFrom(null);
        $this->assertNull($data->getFrom());
    }

    public function testEndAt(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getTo());
        $this->assertFalse($data->isOpenOnly());

        $expectedDateTo = new DateTimeImmutable('now');
        $data->setTo($expectedDateTo);
        $this->assertSame($expectedDateTo, $data->getTo());
        $this->assertTrue($data->isOpenOnly());

        $data->setTo(null);
        $this->assertNull($data->getTo());
    }

    public function testIsOpenOnly(): void
    {
        $data = new CampSearchData();
        $this->assertFalse($data->isOpenOnly());

        $data->setIsOpenOnly(true);
        $this->assertTrue($data->isOpenOnly());
    }

    public function testIsOpenOnlyFalseIfDateIsFilled(): void
    {
        $data = new CampSearchData();
        $data->setFrom(new DateTimeImmutable('now'));
        $data->setIsOpenOnly(false);
        $this->assertTrue($data->isOpenOnly());

        $data = new CampSearchData();
        $data->setTo(new DateTimeImmutable('now'));
        $data->setIsOpenOnly(false);
        $this->assertTrue($data->isOpenOnly());
    }
}