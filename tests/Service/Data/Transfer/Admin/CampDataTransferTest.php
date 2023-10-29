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
        $expectedStreet = 'Street';
        $expectedTown = 'Town';
        $expectedZip = 'Zip';
        $expectedCountry = 'Country';
        $expectedDescriptionShort = 'short';
        $expectedDescriptionLong = 'long';
        $expectedFeaturedPriority = 123;
        $expectedCampCategory = new CampCategory('Parent', 'parent');

        $camp = new Camp($expectedName, $expectedUrlName, $expectedAgeMin, $expectedAgeMax, $expectedStreet, $expectedTown, $expectedZip, $expectedCountry);
        $camp->setDescriptionShort($expectedDescriptionShort);
        $camp->setDescriptionLong($expectedDescriptionLong);
        $camp->setPriority($expectedFeaturedPriority);
        $camp->setCampCategory($expectedCampCategory);
        $camp->setIsFeatured(true);

        $data = new CampData();
        $dataTransfer->fillData($data, $camp);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedUrlName, $data->getUrlName());
        $this->assertSame($expectedAgeMin, $data->getAgeMin());
        $this->assertSame($expectedAgeMax, $data->getAgeMax());
        $this->assertSame($expectedStreet, $data->getStreet());
        $this->assertSame($expectedTown, $data->getTown());
        $this->assertSame($expectedZip, $data->getZip());
        $this->assertSame($expectedCountry, $data->getCountry());
        $this->assertSame($expectedDescriptionShort, $data->getDescriptionShort());
        $this->assertSame($expectedDescriptionLong, $data->getDescriptionLong());
        $this->assertSame($expectedFeaturedPriority, $data->getPriority());
        $this->assertSame($expectedCampCategory, $data->getCampCategory());
        $this->assertTrue($data->isFeatured());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampDataTransfer();

        $expectedName = 'Name';
        $expectedUrlName = 'name';
        $expectedAgeMin = 2;
        $expectedAgeMax = 4;
        $expectedStreet = 'Street';
        $expectedTown = 'Town';
        $expectedZip = 'Zip';
        $expectedCountry = 'Country';
        $expectedDescriptionShort = 'short';
        $expectedDescriptionLong = 'long';
        $expectedFeaturedPriority = 123;
        $expectedCampCategory = new CampCategory('Parent', 'parent');

        $data = new CampData();
        $data->setName($expectedName);
        $data->setUrlName($expectedUrlName);
        $data->setAgeMin($expectedAgeMin);
        $data->setAgeMax($expectedAgeMax);
        $data->setStreet($expectedStreet);
        $data->setTown($expectedTown);
        $data->setZip($expectedZip);
        $data->setCountry($expectedCountry);
        $data->setDescriptionShort($expectedDescriptionShort);
        $data->setDescriptionLong($expectedDescriptionLong);
        $data->setPriority($expectedFeaturedPriority);
        $data->setCampCategory($expectedCampCategory);
        $data->setIsFeatured(true);

        $camp = new Camp('', '', 0, 0, '', '', '', '');
        $dataTransfer->fillEntity($data, $camp);

        $this->assertSame($expectedName, $camp->getName());
        $this->assertSame($expectedUrlName, $camp->getUrlName());
        $this->assertSame($expectedAgeMin, $camp->getAgeMin());
        $this->assertSame($expectedAgeMax, $camp->getAgeMax());
        $this->assertSame($expectedStreet, $camp->getStreet());
        $this->assertSame($expectedTown, $camp->getTown());
        $this->assertSame($expectedZip, $camp->getZip());
        $this->assertSame($expectedCountry, $camp->getCountry());
        $this->assertSame($expectedDescriptionShort, $camp->getDescriptionShort());
        $this->assertSame($expectedDescriptionLong, $camp->getDescriptionLong());
        $this->assertSame($expectedFeaturedPriority, $camp->getPriority());
        $this->assertSame($expectedCampCategory, $camp->getCampCategory());
        $this->assertTrue($camp->isFeatured());
    }

    private function getCampDataTransfer(): CampDataTransfer
    {
        $container = static::getContainer();

        /** @var CampDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CampDataTransfer::class);

        return $dataTransfer;
    }
}