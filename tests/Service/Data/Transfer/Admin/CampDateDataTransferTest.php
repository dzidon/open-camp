<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Service\Data\Transfer\Admin\CampDateDataTransfer;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampDateDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCampDateDataTransfer();

        $expectedCamp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS');
        $expectedStartAt = new DateTimeImmutable('2000-01-01');
        $expectedEndAt = new DateTimeImmutable('2000-01-05');
        $expectedPrice = 1000.0;
        $expectedCapacity = 10;
        $expectedTripInstructions = 'Instructions...';
        $expectedLeaders = [
            new User('bob1@gmail.com'),
            new User('bob2@gmail.com'),
        ];

        $campDate = new CampDate($expectedStartAt, $expectedEndAt, $expectedPrice, $expectedCapacity, $expectedCamp);
        $campDate->setIsClosed(true);
        $campDate->setTripInstructions($expectedTripInstructions);
        foreach ($expectedLeaders as $expectedLeader)
        {
            $campDate->addLeader($expectedLeader);
        }

        $data = new CampDateData();
        $dataTransfer->fillData($data, $campDate);

        $this->assertSame($campDate->getId(), $data->getId());
        $this->assertSame($expectedCamp, $data->getCamp());
        $this->assertSame($expectedStartAt, $data->getStartAt());
        $this->assertSame($expectedEndAt, $data->getEndAt());
        $this->assertSame($expectedPrice, $data->getPrice());
        $this->assertSame($expectedCapacity, $data->getCapacity());
        $this->assertTrue($data->isClosed());
        $this->assertSame($expectedTripInstructions, $data->getTripInstructions());
        $this->assertSame($expectedLeaders, $data->getLeaders());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampDateDataTransfer();

        $expectedStartAt = new DateTimeImmutable('2000-01-01');
        $expectedEndAt = new DateTimeImmutable('2000-01-05');
        $expectedPrice = 1000.0;
        $expectedCapacity = 10;
        $expectedTripInstructions = 'Instructions...';
        $expectedLeaders = [
            new User('bob1@gmail.com'),
            new User('bob2@gmail.com'),
        ];

        $data = new CampDateData();
        $data->setIsClosed(true);
        $data->setStartAt($expectedStartAt);
        $data->setEndAt($expectedEndAt);
        $data->setPrice($expectedPrice);
        $data->setCapacity($expectedCapacity);
        $data->setTripInstructions($expectedTripInstructions);
        $data->setLeaders($expectedLeaders);

        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS');
        $campDate = new CampDate(new DateTimeImmutable('3000-01-02'), new DateTimeImmutable('3000-01-06'), 0.0, 0, $camp);
        $dataTransfer->fillEntity($data, $campDate);

        $this->assertSame($expectedStartAt, $campDate->getStartAt());
        $this->assertSame($expectedEndAt, $campDate->getEndAt());
        $this->assertSame($expectedPrice, $campDate->getPrice());
        $this->assertSame($expectedCapacity, $campDate->getCapacity());
        $this->assertTrue($campDate->isClosed());
        $this->assertSame($expectedTripInstructions, $campDate->getTripInstructions());
        $this->assertSame($expectedLeaders, $campDate->getLeaders());
    }

    private function getCampDateDataTransfer(): CampDateDataTransfer
    {
        $container = static::getContainer();

        /** @var CampDateDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CampDateDataTransfer::class);

        return $dataTransfer;
    }
}