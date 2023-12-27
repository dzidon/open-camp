<?php

namespace App\Tests\Model\Event\Admin\FormField;

use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Event\Admin\FormField\FormFieldDeleteEvent;
use PHPUnit\Framework\TestCase;

class FormFieldDeleteEventTest extends TestCase
{
    private FormField $entity;

    private FormFieldDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getFormField());

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
        $this->entity = new FormField('Field 1', FormFieldTypeEnum::TEXT, 'Field 1:');
        $this->event = new FormFieldDeleteEvent($this->entity);
    }
}