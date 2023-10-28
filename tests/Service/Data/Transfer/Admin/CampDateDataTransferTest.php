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

        $expectedStartAt = new DateTimeImmutable('2000-01-01');
        $expectedEndAt = new DateTimeImmutable('2000-01-05');
        $expectedPrice = 1000.0;
        $expectedCapacity = 10;
        $expectedDescription = 'Instructions...';
        $expectedLeaders = [
            new User('bob1@gmail.com'),
            new User('bob2@gmail.com'),
        ];

        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS');
        $campDate = new CampDate($expectedStartAt, $expectedEndAt, $expectedPrice, $expectedCapacity, $camp);
        $campDate->setIsClosed(true);
        $campDate->setIsOpenAboveCapacity(true);
        $campDate->setDescription($expectedDescription);

        foreach ($expectedLeaders as $expectedLeader)
        {
            $campDate->addLeader($expectedLeader);
        }

        $data = new CampDateData($camp);
        $dataTransfer->fillData($data, $campDate);

        $this->assertSame($expectedStartAt, $data->getStartAt());
        $this->assertSame($expectedEndAt, $data->getEndAt());
        $this->assertSame($expectedPrice, $data->getPrice());
        $this->assertSame($expectedCapacity, $data->getCapacity());
        $this->assertTrue($data->isClosed());
        $this->assertTrue($data->isOpenAboveCapacity());
        $this->assertSame($expectedDescription, $data->getDescription());
        $this->assertSame($expectedLeaders, $data->getLeaders());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampDateDataTransfer();

        $expectedStartAt = new DateTimeImmutable('2000-01-01');
        $expectedEndAt = new DateTimeImmutable('2000-01-05');
        $expectedPrice = 1000.0;
        $expectedCapacity = 10;
        $expectedDescription = 'Instructions...';
        $expectedLeaders = [
            new User('bob1@gmail.com'),
            new User('bob2@gmail.com'),
        ];

        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS');

        $data = new CampDateData($camp);
        $data->setIsClosed(true);
        $data->setIsOpenAboveCapacity(true);
        $data->setStartAt($expectedStartAt);
        $data->setEndAt($expectedEndAt);
        $data->setPrice($expectedPrice);
        $data->setCapacity($expectedCapacity);
        $data->setDescription($expectedDescription);

        foreach ($expectedLeaders as $expectedLeader)
        {
            $data->addLeader($expectedLeader);
        }

        $campDate = new CampDate(new DateTimeImmutable('3000-01-02'), new DateTimeImmutable('3000-01-06'), 0.0, 0, $camp);
        $dataTransfer->fillEntity($data, $campDate);

        $this->assertSame($expectedStartAt, $campDate->getStartAt());
        $this->assertSame($expectedEndAt, $campDate->getEndAt());
        $this->assertSame($expectedPrice, $campDate->getPrice());
        $this->assertSame($expectedCapacity, $campDate->getCapacity());
        $this->assertTrue($campDate->isClosed());
        $this->assertTrue($campDate->isOpenAboveCapacity());
        $this->assertSame($expectedDescription, $campDate->getDescription());
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