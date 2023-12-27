<?php

namespace App\Tests\Model\Event\Admin\FormField;

use App\Library\Data\Admin\FormFieldData;
use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Event\Admin\FormField\FormFieldCreateEvent;
use PHPUnit\Framework\TestCase;

class FormFieldCreateEventTest extends TestCase
{
    private FormFieldData $data;

    private FormFieldCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getFormFieldData());

        $newData = new FormFieldData();
        $this->event->setFormFieldData($newData);
        $this->assertSame($newData, $this->event->getFormFieldData());
    }

    public function testEntity(): void
    {
        $this->assertNull($this->event->getFormField());

        $newEntity = new FormField('Field 2', FormFieldTypeEnum::TEXT, 'Field 2:');
        $this->event->setFormField($newEntity);
        $this->assertSame($newEntity, $this->event->getFormField());
    }

    public function testIsFlush(): void
    {
        $this->assertTrue($this->event->isFlush());

        $this->event->setIsFlush(false);
        $this->assertFalse($this->event->isFlush());

        $this->event->setIsFlush(true);
        $this->assertTrue($this->event->isFlush());
    }

    protected function setUp(): void
    {
        $this->data = new FormFieldData();
        $this->event = new FormFieldCreateEvent($this->data);
    }
}