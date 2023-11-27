<?php

namespace App\Tests\Model\Event\Admin\FormField;

use App\Library\Data\Admin\FormFieldData;
use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Event\Admin\FormField\FormFieldCreatedEvent;
use PHPUnit\Framework\TestCase;

class FormFieldCreatedEventTest extends TestCase
{
    private FormField $entity;

    private FormFieldData $data;

    private FormFieldCreatedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getFormFieldData());

        $newData = new FormFieldData();
        $this->event->setFormFieldData($newData);
        $this->assertSame($newData, $this->event->getFormFieldData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getFormField());

        $newEntity = new FormField('Field 2', FormFieldTypeEnum::TEXT, 'Field 2:');
        $this->event->setFormField($newEntity);
        $this->assertSame($newEntity, $this->event->getFormField());
    }

    protected function setUp(): void
    {
        $this->entity = new FormField('Field 1', FormFieldTypeEnum::TEXT, 'Field 1:');
        $this->data = new FormFieldData();
        $this->event = new FormFieldCreatedEvent($this->data, $this->entity);
    }
}