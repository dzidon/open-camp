<?php

namespace App\Tests\Form\DataTransfer\Transfer\Admin;

use App\Form\DataTransfer\Data\Admin\CampCategoryData;
use App\Form\DataTransfer\Transfer\Admin\CampCategoryDataTransfer;
use App\Model\Entity\CampCategory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampCategoryDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCampCategoryDataTransfer();

        $expectedName = 'Name';
        $expectedUrlName = 'name';
        $expectedParent = new CampCategory('Parent', 'parent');

        $campCategory = new CampCategory($expectedName, $expectedUrlName);
        $campCategory->setParent($expectedParent);

        $data = new CampCategoryData();
        $dataTransfer->fillData($data, $campCategory);

        $this->assertSame($campCategory->getId(), $data->getId());
        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedUrlName, $data->getUrlName());
        $this->assertSame($expectedParent, $data->getParent());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampCategoryDataTransfer();

        $expectedName = 'Name';
        $expectedUrlName = 'name';
        $expectedParent = new CampCategory('Parent', 'parent');

        $data = new CampCategoryData();
        $data->setName($expectedName);
        $data->setUrlName($expectedUrlName);
        $data->setParent($expectedParent);

        $campCategory = new CampCategory($expectedName, $expectedUrlName);
        $dataTransfer->fillEntity($data, $campCategory);

        $this->assertSame($expectedName, $campCategory->getName());
        $this->assertSame($expectedUrlName, $campCategory->getUrlName());
        $this->assertSame($expectedParent, $campCategory->getParent());
    }

    private function getCampCategoryDataTransfer(): CampCategoryDataTransfer
    {
        $container = static::getContainer();

        /** @var CampCategoryDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CampCategoryDataTransfer::class);

        return $dataTransfer;
    }
}