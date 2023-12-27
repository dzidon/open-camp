<?php

namespace App\Tests\Model\Event\User\UserRegistration;

use App\Library\Data\User\RegistrationData;
use App\Model\Entity\UserRegistration;
use App\Model\Event\User\UserRegistration\UserRegistrationCreateEvent;
use App\Model\Library\UserRegistration\UserRegistrationResult;
use DateTimeImmutable;
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

    public function testResult(): void
    {
        $this->assertNull($this->event->getUserRegistrationResult());

        $userRegistration = new UserRegistration('bob@gmail.com', new DateTimeImmutable(), 'abc', '123');
        $newResult = new UserRegistrationResult($userRegistration, '321', false);
        $this->event->setUserRegistrationResult($newResult);
        $this->assertSame($newResult, $this->event->getUserRegistrationResult());
    }

    public function testIsFlush(): void
    {
        $this->assertTrue($this->event->isFlush());

        $this->event->setIsFlush(false);
        $this->assertFalse($this->event->isFlush());

        $this->event->setIsFlush(true);
        $this->assertTrue($this->event->isFlush());
    }

    protected function setUp(): void
    {
        $this->data = new RegistrationData();
        $this->event = new UserRegistrationCreateEvent($this->data);
    }
}