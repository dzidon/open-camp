<?php

namespace App\Tests\Model\Event\Admin\User;

use App\Library\Data\Admin\UserData;
use App\Model\Event\Admin\User\UserCreateEvent;
use PHPUnit\Framework\TestCase;

class UserCreateEventTest extends TestCase
{
    private UserData $data;

    private UserCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getUserData());

        $newData = new UserData(true);
        $this->event->setUserData($newData);
        $this->assertSame($newData, $this->event->getUserData());
    }

    protected function setUp(): void
    {
        $this->data = new UserData(true);
        $this->event = new UserCreateEvent($this->data);
    }
}