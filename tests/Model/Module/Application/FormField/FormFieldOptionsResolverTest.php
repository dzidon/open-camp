<?php

namespace App\Tests\Model\Module\Application\FormField;

use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Module\Application\FormField\FormFieldOptionsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

class FormFieldOptionsResolverTest extends TestCase
{
    private FormFieldOptionsResolver $resolver;

    public function testInvalidOption(): void
    {
        $this->expectException(UndefinedOptionsException::class);
        $this->resolver->resolve(FormFieldTypeEnum::TEXT, ['decimal' => true]);
    }

    public function testTextDefaults(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::TEXT, []);

        $this->assertSame([
            'length_min' => null,
            'length_max' => null,
            'regex'      => null,
        ], $options);
    }

    public function testTextTypeChanges(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::TEXT, [
            'length_min' => '1',
            'length_max' => '2',
            'regex'      => 123,
        ]);

        $this->assertSame([
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => '123',
        ], $options);
    }

    public function testTextCorrectTypes(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::TEXT, [
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => '123',
        ]);

        $this->assertSame([
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => '123',
        ], $options);
    }

    public function testTextAreaDefaults(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::TEXT_AREA, []);

        $this->assertSame([
            'length_min' => null,
            'length_max' => null,
            'regex'      => null,
        ], $options);
    }

    public function testTextAreaTypeChanges(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::TEXT_AREA, [
            'length_min' => '1',
            'length_max' => '2',
            'regex'      => 123,
        ]);

        $this->assertSame([
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => '123',
        ], $options);
    }

    public function testTextAreaCorrectTypes(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::TEXT_AREA, [
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => '123',
        ]);

        $this->assertSame([
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => '123',
        ], $options);
    }

    public function testNumberDefaults(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::NUMBER, []);

        $this->assertSame([
            'min'     => null,
            'max'     => null,
            'decimal' => false,
        ], $options);
    }

    public function testNumberTypeChanges(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::NUMBER, [
            'min'     => '1',
            'max'     => '2',
            'decimal' => '1',
        ]);

        $this->assertSame([
            'min'     => 1.0,
            'max'     => 2.0,
            'decimal' => true,
        ], $options);
    }

    public function testNumberCorrectTypes(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::NUMBER, [
            'min'     => 1,
            'max'     => 2,
            'decimal' => true,
        ]);

        $this->assertSame([
            'min'     => 1,
            'max'     => 2,
            'decimal' => true,
        ], $options);
    }

    public function testChoiceDefaults(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::CHOICE, []);

        $this->assertSame([
            'multiple' => false,
            'expanded' => false,
            'items'    => [],
        ], $options);
    }

    public function testChoiceTypeChanges(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::CHOICE, [
            'multiple' => '1',
            'expanded' => '1',
            'items'    => 123,
        ]);

        $this->assertSame([
            'multiple' => true,
            'expanded' => true,
            'items'    => ['123'],
        ], $options);
    }

    public function testChoiceTypeCorrectTypes(): void
    {
        $options = $this->resolver->resolve(FormFieldTypeEnum::CHOICE, [
            'multiple' => true,
            'expanded' => true,
            'items'    => ['item 1', 'item 2'],
        ]);

        $this->assertSame([
            'multiple' => true,
            'expanded' => true,
            'items'    => ['item 1', 'item 2'],
        ], $options);
    }

    protected function setUp(): void
    {
        $this->resolver = new FormFieldOptionsResolver();
    }
}