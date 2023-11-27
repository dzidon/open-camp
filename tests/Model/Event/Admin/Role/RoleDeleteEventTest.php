<?php

namespace App\Tests\Model\Event\Admin\Role;

use App\Model\Entity\Role;
use App\Model\Event\Admin\Role\RoleDeleteEvent;
use PHPUnit\Framework\TestCase;

class RoleDeleteEventTest extends TestCase
{
    private Role $entity;

    private RoleDeleteEvent $event;

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
        $this->event = new RoleDeleteEvent($this->entity);
    }
}