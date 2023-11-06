<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\FormFieldData;
use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Repository\FormFieldRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormFieldDataTest extends KernelTestCase
{
    public function testFormField(): void
    {
        $data = new FormFieldData();
        $this->assertNull($data->getFormField());

        $formField = new FormField('Field', FormFieldTypeEnum::TEXT, 'Field:');
        $data = new FormFieldData($formField);
        $this->assertSame($formField, $data->getFormField());
    }

    public function testName(): void
    {
        $data = new FormFieldData();
        $this->assertNull($data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertNull($data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FormFieldData();
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName('');
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(null);
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'name');
        $this->assertEmpty($result); // valid

        $data->setName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid
    }

    public function testType(): void
    {
        $data = new FormFieldData();
        $this->assertNull($data->getType());

        $data->setType(FormFieldTypeEnum::NUMBER);
        $this->assertSame(FormFieldTypeEnum::NUMBER, $data->getType());

        $data->setType(null);
        $this->assertNull($data->getType());
    }

    public function testTypeValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FormFieldData();
        $result = $validator->validateProperty($data, 'type');
        $this->assertNotEmpty($result); // invalid

        $data->setType(null);
        $result = $validator->validateProperty($data, 'type');
        $this->assertNotEmpty($result); // invalid

        $data->setType(FormFieldTypeEnum::TEXT);
        $result = $validator->validateProperty($data, 'type');
        $this->assertEmpty($result); // valid
    }

    public function testLabel(): void
    {
        $data = new FormFieldData();
        $this->assertNull($data->getLabel());

        $data->setLabel('text');
        $this->assertSame('text', $data->getLabel());

        $data->setLabel(null);
        $this->assertNull($data->getLabel());
    }

    public function testLabelValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FormFieldData();
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel('');
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel(null);
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'label');
        $this->assertEmpty($result); // valid

        $data->setLabel(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid
    }

    public function testHelp(): void
    {
        $data = new FormFieldData();
        $this->assertNull($data->getHelp());

        $data->setHelp('text');
        $this->assertSame('text', $data->getHelp());

        $data->setHelp(null);
        $this->assertNull($data->getHelp());
    }

    public function testHelpValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FormFieldData();
        $result = $validator->validateProperty($data, 'help');
        $this->assertEmpty($result); // valid

        $data->setHelp('');
        $result = $validator->validateProperty($data, 'help');
        $this->assertEmpty($result); // valid

        $data->setHelp(null);
        $result = $validator->validateProperty($data, 'help');
        $this->assertEmpty($result); // valid

        $data->setHelp(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'help');
        $this->assertEmpty($result); // valid

        $data->setHelp(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'help');
        $this->assertNotEmpty($result); // invalid
    }

    public function testNullTypeOptions(): void
    {
        $data = new FormFieldData();
        $this->assertEmpty($data->getOptions());

        $data->setType(null);
        $this->assertEmpty($data->getOptions());
    }

    public function testNonexistentOption(): void
    {
        $data = new FormFieldData();

        $this->expectException(UndefinedOptionsException::class);
        $data->setOption('nonexistent', true);
    }

    public function testOptions(): void
    {
        $data = new FormFieldData();
        $data->setType(FormFieldTypeEnum::TEXT, [
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => 'abc',
        ]);

        $this->assertFalse($data->hasOption('nonexistent'));
        $this->assertTrue($data->hasOption('length_min'));
        $this->assertSame(1, $data->getOption('length_min'));

        $data->setOption('length_min', 2);
        $this->assertSame(2, $data->getOption('length_min'));

        $data->setOptions([
            'length_min' => 5,
            'length_max' => 6,
        ]);
        $this->assertSame(5, $data->getOption('length_min'));
        $this->assertSame('abc', $data->getOption('regex'));
    }

    public function testTextOptionsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FormFieldData();
        $data->setType(FormFieldTypeEnum::TEXT);

        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'length_min' => null,
            'length_max' => null,
            'regex'      => null,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'length_min' => 0,
            'length_max' => 1,
            'regex'      => 'abc',
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'length_min' => 1,
            'length_max' => 1,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'length_min' => -1,
            'length_max' => 1,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid

        $data->setOptions([
            'length_min' => 0,
            'length_max' => 0,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid

        $data->setOptions([
            'length_min' => 5,
            'length_max' => 4,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid
    }

    public function testTextAreaOptionsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FormFieldData();
        $data->setType(FormFieldTypeEnum::TEXT_AREA);

        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'length_min' => null,
            'length_max' => null,
            'regex'      => null,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'length_min' => 0,
            'length_max' => 1,
            'regex'      => 'abc',
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'length_min' => 1,
            'length_max' => 1,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'length_min' => -1,
            'length_max' => 1,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid

        $data->setOptions([
            'length_min' => 0,
            'length_max' => 0,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid

        $data->setOptions([
            'length_min' => 5,
            'length_max' => 4,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid
    }

    public function testNumberOptionsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FormFieldData();
        $data->setType(FormFieldTypeEnum::NUMBER);

        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'min'     => null,
            'max'     => null,
            'decimal' => false,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOptions([
            'min'     => 2.5,
            'max'     => 3.5,
            'decimal' => true,
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOption('min', 3.5);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid

        $data->setOption('min', 4.5);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid
    }

    public function testChoiceOptionsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FormFieldData();
        $data->setType(FormFieldTypeEnum::CHOICE);

        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid

        $data->setOptions([
            'multiple' => false,
            'expanded' => false,
            'items'    => [],
        ]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid

        $data->setOption('items', ['']);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid

        $data->setOption('items', [str_repeat('x', 256)]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertNotEmpty($result); // invalid

        $data->setOption('items', [str_repeat('x', 255)]);
        $result = $validator->validateProperty($data, 'options');
        $this->assertEmpty($result); // valid
    }

    public function testIsRequired(): void
    {
        $data = new FormFieldData();
        $this->assertFalse($data->isRequired());

        $data->setIsRequired(true);
        $this->assertTrue($data->isRequired());

        $data->setIsRequired(false);
        $this->assertFalse($data->isRequired());
    }

    public function testIsGlobal(): void
    {
        $data = new FormFieldData();
        $this->assertFalse($data->isGlobal());

        $data->setIsGlobal(true);
        $this->assertTrue($data->isGlobal());

        $data->setIsGlobal(false);
        $this->assertFalse($data->isGlobal());
    }

    public function testDisableChoiceItemsValidation(): void
    {
        $data = new FormFieldData();
        $this->assertFalse($data->isDisableChoiceItemsValidation());

        $data = new FormFieldData(null, false);
        $this->assertFalse($data->isDisableChoiceItemsValidation());

        $data = new FormFieldData(null, true);
        $this->assertTrue($data->isDisableChoiceItemsValidation());
    }

    public function testUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FormFieldData();
        $data->setType(FormFieldTypeEnum::TEXT);
        $data->setLabel('Label');
        $data->setName('Field 1');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setName('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $formFieldRepository = $this->getFormFieldRepository();
        $formField = $formFieldRepository->findOneByName('Field 2');
        $data = new FormFieldData($formField);
        $data->setType(FormFieldTypeEnum::TEXT);
        $data->setLabel('Label');
        $data->setName('Field 2');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setName('Field 1');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setName('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    private function getFormFieldRepository(): FormFieldRepositoryInterface
    {
        $container = static::getContainer();

        /** @var FormFieldRepositoryInterface $repository */
        $repository = $container->get(FormFieldRepositoryInterface::class);

        return $repository;
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}