<?php

namespace App\Tests\Model\Event\Admin\Camp;

use App\Library\Data\Admin\CampCreationData;
use App\Model\Event\Admin\Camp\CampCreateEvent;
use PHPUnit\Framework\TestCase;

class CampCreateEventTest extends TestCase
{
    private CampCreationData $data;

    private CampCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getCampCreationData());

        $newData = new CampCreationData();
        $this->event->setCampCreationData($newData);
        $this->assertSame($newData, $this->event->getCampCreationData());
    }

    protected function setUp(): void
    {
        $this->data = new CampCreationData();
        $this->event = new CampCreateEvent($this->data);
    }
}