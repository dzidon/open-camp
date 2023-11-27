<?php

namespace App\Tests\Model\Event\Admin\Role;

use App\Library\Data\Admin\RoleData;
use App\Model\Event\Admin\Role\RoleCreateEvent;
use PHPUnit\Framework\TestCase;

class RoleCreateEventTest extends TestCase
{
    private RoleData $data;

    private RoleCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getRoleData());

        $newData = new RoleData();
        $this->event->setRoleData($newData);
        $this->assertSame($newData, $this->event->getRoleData());
    }

    protected function setUp(): void
    {
        $this->data = new RoleData();
        $this->event = new RoleCreateEvent($this->data);
    }
}