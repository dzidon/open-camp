<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\TripLocationPathData;
use App\Model\Entity\TripLocationPath;
use App\Service\Data\Transfer\Admin\TripLocationPathDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TripLocationPathDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getTripLocationPathDataTransfer();

        $expectedName = 'Name';
        $tripLocationPath = new TripLocationPath($expectedName);

        $data = new TripLocationPathData();
        $dataTransfer->fillData($data, $tripLocationPath);

        $this->assertSame($expectedName, $data->getName());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getTripLocationPathDataTransfer();

        $expectedName = 'Name';
        $tripLocationPath = new TripLocationPath('');

        $data = new TripLocationPathData();
        $data->setName($expectedName);

        $dataTransfer->fillEntity($data, $tripLocationPath);

        $this->assertSame($expectedName, $tripLocationPath->getName());
    }

    private function getTripLocationPathDataTransfer(): TripLocationPathDataTransfer
    {
        $container = static::getContainer();

        /** @var TripLocationPathDataTransfer $dataTransfer */
        $dataTransfer = $container->get(TripLocationPathDataTransfer::class);

        return $dataTransfer;
    }
}