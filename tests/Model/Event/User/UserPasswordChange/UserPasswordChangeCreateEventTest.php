<?php

namespace App\Tests\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PasswordChangeData;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCreateEvent;
use PHPUnit\Framework\TestCase;

class UserPasswordChangeCreateEventTest extends TestCase
{
    private PasswordChangeData $data;

    private UserPasswordChangeCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPasswordChangeData());

        $newData = new PasswordChangeData();
        $this->event->setPasswordChangeData($newData);
        $this->assertSame($newData, $this->event->getPasswordChangeData());
    }

    protected function setUp(): void
    {
        $this->data = new PasswordChangeData();
        $this->event = new UserPasswordChangeCreateEvent($this->data);
    }
}