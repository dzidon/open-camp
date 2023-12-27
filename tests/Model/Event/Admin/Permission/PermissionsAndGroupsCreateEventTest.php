<?php

namespace App\Tests\Model\Event\Admin\Permission;

use App\Model\Event\Admin\Permission\PermissionsAndGroupsCreateEvent;
use App\Model\Library\Permission\PermissionsAndGroupsCreationResult;
use PHPUnit\Framework\TestCase;

class PermissionsAndGroupsCreateEventTest extends TestCase
{
    private PermissionsAndGroupsCreateEvent $event;

    public function testResult(): void
    {
        $this->assertNull($this->event->getPermissionsAndGroupsCreationResult());

        $newResult = new PermissionsAndGroupsCreationResult();
        $this->event->setPermissionsAndGroupsCreationResult($newResult);
        $this->assertSame($newResult, $this->event->getPermissionsAndGroupsCreationResult());
    }

    protected function setUp(): void
    {
        $this->event = new PermissionsAndGroupsCreateEvent();
    }
}