<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Service\Data\Transfer\Admin\TripLocationDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TripLocationDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getTripLocationDataTransfer();

        $expectedName = 'Name';
        $expectedPrice = 1000.0;
        $expectedPriority = 100;

        $tripLocationPath = new TripLocationPath('Path');
        $tripLocation = new TripLocation($expectedName, $expectedPrice, $expectedPriority, $tripLocationPath);

        $data = new TripLocationData();
        $dataTransfer->fillData($data, $tripLocation);

        $this->assertSame($tripLocation->getId(), $data->getId());
        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedPrice, $data->getPrice());
        $this->assertSame($expectedPriority, $data->getPriority());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getTripLocationDataTransfer();

        $tripLocationPath = new TripLocationPath('Path');
        $tripLocation = new TripLocation('', 0.0, 0, $tripLocationPath);
        $expectedName = 'Name';
        $expectedPrice = 1000.0;
        $expectedPriority = 100;

        $data = new TripLocationData();
        $data->setName($expectedName);
        $data->setPrice($expectedPrice);
        $data->setPriority($expectedPriority);

        $dataTransfer->fillEntity($data, $tripLocation);

        $this->assertSame($expectedName, $tripLocation->getName());
        $this->assertSame($expectedPrice, $tripLocation->getPrice());
        $this->assertSame($expectedPriority, $tripLocation->getPriority());
    }

    private function getTripLocationDataTransfer(): TripLocationDataTransfer
    {
        $container = static::getContainer();

        /** @var TripLocationDataTransfer $dataTransfer */
        $dataTransfer = $container->get(TripLocationDataTransfer::class);

        return $dataTransfer;
    }
}