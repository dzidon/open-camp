<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampDateFormFieldData;
use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampDateFormFieldDataTest extends KernelTestCase
{
    public function testFormField(): void
    {
        $data = new CampDateFormFieldData();
        $this->assertNull($data->getFormField());

        $formField = new FormField('Form field', FormFieldTypeEnum::TEXT, 'Field:');
        $data->setFormField($formField);
        $this->assertSame($formField, $data->getFormField());

        $data->setFormField(null);
        $this->assertNull($data->getFormField());
    }

    public function testFormFieldValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateFormFieldData();
        $result = $validator->validateProperty($data, 'formField');
        $this->assertNotEmpty($result); // invalid

        $formField = new FormField('Form field', FormFieldTypeEnum::TEXT, 'Field:');
        $data->setFormField($formField);
        $result = $validator->validateProperty($data, 'formField');
        $this->assertEmpty($result); // valid

        $data->setFormField(null);
        $result = $validator->validateProperty($data, 'formField');
        $this->assertNotEmpty($result); // invalid
    }

    public function testPriority(): void
    {
        $data = new CampDateFormFieldData();
        $this->assertSame(0, $data->getPriority());

        $data->setPriority(100);
        $this->assertSame(100, $data->getPriority());

        $data->setPriority(null);
        $this->assertNull($data->getPriority());
    }

    public function testPriorityValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateFormFieldData();
        $result = $validator->validateProperty($data, 'priority');
        $this->assertEmpty($result); // valid

        $data->setPriority(100);
        $result = $validator->validateProperty($data, 'priority');
        $this->assertEmpty($result); // valid

        $data->setPriority(null);
        $result = $validator->validateProperty($data, 'priority');
        $this->assertNotEmpty($result); // invalid
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}