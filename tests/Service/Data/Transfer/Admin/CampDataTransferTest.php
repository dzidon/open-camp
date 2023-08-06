<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampCategory;
use App\Service\Data\Transfer\Admin\CampDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCampDataTransfer();

        $expectedName = 'Name';
        $expectedUrlName = 'name';
        $expectedAgeMin = 2;
        $expectedAgeMax = 4;
        $expectedDescriptionShort = 'short';
        $expectedDescriptionLong = 'long';
        $expectedCampCategory = new CampCategory('Parent', 'parent');

        $camp = new Camp($expectedName, $expectedUrlName, $expectedAgeMin, $expectedAgeMax);
        $camp->setDescriptionShort($expectedDescriptionShort);
        $camp->setDescriptionLong($expectedDescriptionLong);
        $camp->setCampCategory($expectedCampCategory);

        $data = new CampData();
        $dataTransfer->fillData($data, $camp);

        $this->assertSame($camp->getId(), $data->getId());
        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedUrlName, $data->getUrlName());
        $this->assertSame($expectedAgeMin, $data->getAgeMin());
        $this->assertSame($expectedAgeMax, $data->getAgeMax());
        $this->assertSame($expectedDescriptionShort, $data->getDescriptionShort());
        $this->assertSame($expectedDescriptionLong, $data->getDescriptionLong());
        $this->assertSame($expectedCampCategory, $data->getCampCategory());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampDataTransfer();

        $expectedName = 'Name';
        $expectedUrlName = 'name';
        $expectedAgeMin = 2;
        $expectedAgeMax = 4;
        $expectedDescriptionShort = 'short';
        $expectedDescriptionLong = 'long';
        $expectedCampCategory = new CampCategory('Parent', 'parent');

        $data = new CampData();
        $data->setName($expectedName);
        $data->setUrlName($expectedUrlName);
        $data->setAgeMin($expectedAgeMin);
        $data->setAgeMax($expectedAgeMax);
        $data->setDescriptionShort($expectedDescriptionShort);
        $data->setDescriptionLong($expectedDescriptionLong);
        $data->setCampCategory($expectedCampCategory);

        $camp = new Camp('', '', 0, 0);
        $dataTransfer->fillEntity($data, $camp);

        $this->assertSame($expectedName, $camp->getName());
        $this->assertSame($expectedUrlName, $camp->getUrlName());
        $this->assertSame($expectedAgeMin, $camp->getAgeMin());
        $this->assertSame($expectedAgeMax, $camp->getAgeMax());
        $this->assertSame($expectedDescriptionShort, $camp->getDescriptionShort());
        $this->assertSame($expectedDescriptionLong, $camp->getDescriptionLong());
        $this->assertSame($expectedCampCategory, $camp->getCampCategory());
    }

    private function getCampDataTransfer(): CampDataTransfer
    {
        $container = static::getContainer();

        /** @var CampDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CampDataTransfer::class);

        return $dataTransfer;
    }
}