<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\FormFieldData;
use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Service\Data\Transfer\Admin\FormFieldDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormFieldDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getFormFieldDataTransfer();

        $expectedName = 'Name';
        $expectedType = FormFieldTypeEnum::NUMBER;
        $expectedLabel = 'Label';
        $expectedOptions = [
            'min'     => 1,
            'max'     => 2,
            'decimal' => true,
        ];
        $expectedHelp = 'Help';

        $formField = new FormField($expectedName, $expectedType, $expectedLabel, $expectedOptions);
        $formField->setHelp($expectedHelp);
        $formField->setIsRequired(true);

        $data = new FormFieldData();
        $dataTransfer->fillData($data, $formField);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedType, $data->getType());
        $this->assertSame($expectedLabel, $data->getLabel());
        $this->assertSame($expectedOptions, $data->getOptions());
        $this->assertSame($expectedHelp, $data->getHelp());
        $this->assertTrue($data->isRequired());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getFormFieldDataTransfer();

        $expectedName = 'Name';
        $expectedType = FormFieldTypeEnum::NUMBER;
        $expectedLabel = 'Label';
        $expectedOptions = [
            'min'     => 1,
            'max'     => 2,
            'decimal' => true,
        ];
        $expectedHelp = 'Help';

        $formField = new FormField('', FormFieldTypeEnum::TEXT, '');

        $data = new FormFieldData();
        $data->setName($expectedName);
        $data->setType($expectedType);
        $data->setLabel($expectedLabel);
        $data->setOptions($expectedOptions);
        $data->setHelp($expectedHelp);
        $data->setIsRequired(true);
        $dataTransfer->fillEntity($data, $formField);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedType, $data->getType());
        $this->assertSame($expectedLabel, $data->getLabel());
        $this->assertSame($expectedOptions, $data->getOptions());
        $this->assertSame($expectedHelp, $data->getHelp());
        $this->assertTrue($data->isRequired());
    }

    private function getFormFieldDataTransfer(): FormFieldDataTransfer
    {
        $container = static::getContainer();

        /** @var FormFieldDataTransfer $dataTransfer */
        $dataTransfer = $container->get(FormFieldDataTransfer::class);

        return $dataTransfer;
    }
}