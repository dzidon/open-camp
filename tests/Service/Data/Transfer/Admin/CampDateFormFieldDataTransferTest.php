<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateFormFieldData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateFormField;
use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Service\Data\Transfer\Admin\CampDateFormFieldDataTransfer;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampDateFormFieldDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCampDateFormFieldDataTransfer();

        $expectedPriority = 100;
        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000.0, 10, $camp);
        $formField = new FormField('Field', FormFieldTypeEnum::TEXT, 'Field:');
        $campDateFormField = new CampDateFormField($campDate, $formField, $expectedPriority);

        $data = new CampDateFormFieldData();
        $dataTransfer->fillData($data, $campDateFormField);

        $this->assertSame($formField, $data->getFormField());
        $this->assertSame($expectedPriority, $data->getPriority());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampDateFormFieldDataTransfer();

        $expectedPriority = 100;
        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000.0, 10, $camp);
        $formField = new FormField('Field', FormFieldTypeEnum::TEXT, 'Field:');
        $campDateFormField = new CampDateFormField($campDate, $formField, $expectedPriority);

        $data = new CampDateFormFieldData();
        $data->setPriority($expectedPriority);
        $data->setFormField($formField);
        $dataTransfer->fillEntity($data, $campDateFormField);

        $this->assertSame($formField, $campDateFormField->getFormField());
        $this->assertSame($expectedPriority, $campDateFormField->getPriority());
    }

    private function getCampDateFormFieldDataTransfer(): CampDateFormFieldDataTransfer
    {
        $container = static::getContainer();

        /** @var CampDateFormFieldDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CampDateFormFieldDataTransfer::class);

        return $dataTransfer;
    }
}