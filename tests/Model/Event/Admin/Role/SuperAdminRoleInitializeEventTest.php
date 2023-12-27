<?php

namespace App\Tests\Model\Event\Admin\Role;

use App\Model\Entity\Role;
use App\Model\Event\Admin\Role\SuperAdminRoleInitializeEvent;
use PHPUnit\Framework\TestCase;

class SuperAdminRoleInitializeEventTest extends TestCase
{
    private SuperAdminRoleInitializeEvent $event;

    public function testRole(): void
    {
        $this->assertNull($this->event->getRole());

        $newEntity = new Role('Role new');
        $this->event->setRole($newEntity);
        $this->assertSame($newEntity, $this->event->getRole());
    }

    protected function setUp(): void
    {
        $this->event = new SuperAdminRoleInitializeEvent();
    }
}