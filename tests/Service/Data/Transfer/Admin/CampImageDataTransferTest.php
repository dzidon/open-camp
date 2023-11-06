<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampImageData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Service\Data\Transfer\Admin\CampImageDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampImageDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCampImageDataTransfer();

        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $expectedPriority = 150;

        $campImage = new CampImage($expectedPriority, 'png', $camp);

        $data = new CampImageData();
        $dataTransfer->fillData($data, $campImage);

        $this->assertSame($expectedPriority, $data->getPriority());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampImageDataTransfer();

        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $expectedPriority = 150;

        $campImage = new CampImage(0, 'png', $camp);

        $data = new CampImageData();
        $data->setPriority($expectedPriority);
        $dataTransfer->fillEntity($data, $campImage);

        $this->assertSame($expectedPriority, $campImage->getPriority());
    }

    private function getCampImageDataTransfer(): CampImageDataTransfer
    {
        $container = static::getContainer();

        /** @var CampImageDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CampImageDataTransfer::class);

        return $dataTransfer;
    }
}