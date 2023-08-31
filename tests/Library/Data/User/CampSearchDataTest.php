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

        $expectedDateFrom = new DateTimeImmutable('now');
        $data->setFrom($expectedDateFrom);
        $this->assertSame($expectedDateFrom, $data->getFrom());

        $data->setFrom(null);
        $this->assertNull($data->getFrom());
    }

    public function testEndAt(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getTo());

        $expectedDateTo = new DateTimeImmutable('now');
        $data->setTo($expectedDateTo);
        $this->assertSame($expectedDateTo, $data->getTo());

        $data->setTo(null);
        $this->assertNull($data->getTo());
    }
}