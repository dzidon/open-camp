<?php

namespace App\Tests\Model\Event\User\UserRegistration;

use App\Library\Data\User\RegistrationData;
use App\Model\Event\User\UserRegistration\UserRegistrationCreateEvent;
use PHPUnit\Framework\TestCase;

class UserRegistrationCreateEventTest extends TestCase
{
    private RegistrationData $data;

    private UserRegistrationCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getRegistrationData());

        $newData = new RegistrationData();
        $this->event->setRegistrationData($newData);
        $this->assertSame($newData, $this->event->getRegistrationData());
    }

    protected function setUp(): void
    {
        $this->data = new RegistrationData();
        $this->event = new UserRegistrationCreateEvent($this->data);
    }
}