<?php

namespace App\Tests\Model\Event\Admin\Permission;

use App\Model\Event\Admin\Permission\PermissionsAndGroupsCreatedEvent;
use App\Model\Library\Permission\PermissionsAndGroupsCreationResult;
use PHPUnit\Framework\TestCase;

class PermissionsAndGroupsCreatedEventTest extends TestCase
{
    private PermissionsAndGroupsCreationResult $result;

    private PermissionsAndGroupsCreatedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->result, $this->event->getPermissionsAndGroupsCreationResult());

        $newResult = new PermissionsAndGroupsCreationResult();
        $this->event->setPermissionsAndGroupsCreationResult($newResult);
        $this->assertSame($newResult, $this->event->getPermissionsAndGroupsCreationResult());
    }

    protected function setUp(): void
    {
        $this->result = new PermissionsAndGroupsCreationResult();
        $this->event = new PermissionsAndGroupsCreatedEvent($this->result);
    }
}