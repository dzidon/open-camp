<?php

namespace App\Tests\Model\Event\Admin\Role;

use App\Library\Data\Admin\RoleData;
use App\Model\Entity\Role;
use App\Model\Event\Admin\Role\RoleUpdateEvent;
use PHPUnit\Framework\TestCase;

class RoleUpdateEventTest extends TestCase
{
    private Role $entity;

    private RoleData $data;

    private RoleUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getRoleData());

        $newData = new RoleData();
        $this->event->setRoleData($newData);
        $this->assertSame($newData, $this->event->getRoleData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getRole());

        $newEntity = new Role('Role new');
        $this->event->setRole($newEntity);
        $this->assertSame($newEntity, $this->event->getRole());
    }

    protected function setUp(): void
    {
        $this->entity = new Role('Role');
        $this->data = new RoleData();
        $this->event = new RoleUpdateEvent($this->data, $this->entity);
    }
}