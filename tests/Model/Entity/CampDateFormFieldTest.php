<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateFormField;
use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class CampDateFormFieldTest extends TestCase
{
    private const PRIORITY = 100;

    private Camp $camp;
    private CampDate $campDate;
    private FormField $formField;
    private CampDateFormField $campDateFormField;

    public function testId(): void
    {
        $id = $this->campDateFormField->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testCampDate(): void
    {
        $this->assertSame($this->campDate, $this->campDateFormField->getCampDate());
        $this->assertContains($this->campDateFormField, $this->campDate->getCampDateFormFields());

        $campDateNew = new CampDate(new DateTimeImmutable('2000-01-02'), new DateTimeImmutable('2000-01-08'), 2000.0, 200.0, 20, $this->camp);
        $this->campDateFormField->setCampDate($campDateNew);

        $this->assertSame($campDateNew, $this->campDateFormField->getCampDate());
        $this->assertContains($this->campDateFormField, $campDateNew->getCampDateFormFields());
        $this->assertNotContains($this->campDateFormField, $this->campDate->getCampDateFormFields());
    }

    public function testFormField(): void
    {
        $this->assertSame($this->formField, $this->campDateFormField->getFormField());

        $formFieldNew = new FormField('Field new', FormFieldTypeEnum::TEXT, 'Field new:');
        $this->campDateFormField->setFormField($formFieldNew);
        $this->assertSame($formFieldNew, $this->campDateFormField->getFormField());
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->campDateFormField->getPriority());

        $newPriority = 200;
        $this->campDateFormField->setPriority($newPriority);
        $this->assertSame($newPriority, $this->campDateFormField->getPriority());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->campDateFormField->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->campDateFormField->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 321);
        $this->campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000.0, 100.0, 10, $this->camp);
        $this->formField = new FormField('Field', FormFieldTypeEnum::TEXT, 'Field:');
        $this->campDateFormField = new CampDateFormField($this->campDate, $this->formField, 100);
    }
}