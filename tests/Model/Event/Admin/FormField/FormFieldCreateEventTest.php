<?php

namespace App\Tests\Model\Event\Admin\FormField;

use App\Library\Data\Admin\FormFieldData;
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

    protected function setUp(): void
    {
        $this->data = new FormFieldData();
        $this->event = new FormFieldCreateEvent($this->data);
    }
}