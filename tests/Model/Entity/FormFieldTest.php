<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\Uid\UuidV4;

class FormFieldTest extends TestCase
{
    private const NAME = 'Field';
    private const TYPE = FormFieldTypeEnum::TEXT;
    private const LABEL = 'Field:';

    private FormField $formField;

    public function testId(): void
    {
        $id = $this->formField->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->formField->getName());

        $newName = 'New name';
        $this->formField->setName($newName);
        $this->assertSame($newName, $this->formField->getName());
    }

    public function testType(): void
    {
        $this->assertSame(self::TYPE, $this->formField->getType());

        $newType = FormFieldTypeEnum::NUMBER;
        $this->formField->setType($newType);
        $this->assertSame($newType, $this->formField->getType());
    }

    public function testLabel(): void
    {
        $this->assertSame(self::LABEL, $this->formField->getLabel());

        $newLabel = 'New label:';
        $this->formField->setLabel($newLabel);
        $this->assertSame($newLabel, $this->formField->getLabel());
    }

    public function testNonexistentOption(): void
    {
        $this->expectException(UndefinedOptionsException::class);
        $this->formField->setOption('nonexistent', true);
    }

    public function testOptions(): void
    {
        $this->assertSame([
            'length_min' => null,
            'length_max' => null,
            'regex'      => null,
        ], $this->formField->getOptions());

        $this->formField->setType(FormFieldTypeEnum::TEXT, [
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => 'abc',
        ]);

        $this->assertFalse($this->formField->hasOption('nonexistent'));
        $this->assertTrue($this->formField->hasOption('length_min'));
        $this->assertSame(1, $this->formField->getOption('length_min'));

        $this->formField->setOption('length_min', 2);
        $this->assertSame(2, $this->formField->getOption('length_min'));

        $this->formField->setOptions([
            'length_min' => 5,
            'length_max' => 6,
        ]);
        $this->assertSame(5, $this->formField->getOption('length_min'));
        $this->assertSame('abc', $this->formField->getOption('regex'));
    }

    public function testConstructorOptions(): void
    {
        $options = [
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => 'abc',
        ];

        $this->formField = new FormField(self::NAME, self::TYPE, self::LABEL, $options);

        $this->assertSame([
            'length_min' => 1,
            'length_max' => 2,
            'regex'      => 'abc',
        ], $this->formField->getOptions());
    }

    public function testIsRequired(): void
    {
        $this->assertFalse($this->formField->isRequired());

        $this->formField->setIsRequired(true);
        $this->assertTrue($this->formField->isRequired());
    }

    public function testHelp(): void
    {
        $this->assertNull($this->formField->getHelp());

        $this->formField->setHelp('text');
        $this->assertSame('text', $this->formField->getHelp());

        $this->formField->setHelp(null);
        $this->assertNull($this->formField->getHelp());
    }

    public function testIsGlobal(): void
    {
        $this->assertFalse($this->formField->isGlobal());

        $this->formField->setIsGlobal(true);
        $this->assertTrue($this->formField->isGlobal());

        $this->formField->setIsGlobal(false);
        $this->assertFalse($this->formField->isGlobal());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->formField->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->formField->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->formField = new FormField(self::NAME, self::TYPE, self::LABEL);
    }
}