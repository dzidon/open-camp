<?php

namespace App\Tests\Model\Event\User\UserRegistration;

use App\Library\Data\User\RegistrationData;
use App\Model\Entity\UserRegistration;
use App\Model\Event\User\UserRegistration\UserRegistrationCreatedEvent;
use App\Model\Library\UserRegistration\UserRegistrationResult;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserRegistrationCreatedEventTest extends TestCase
{
    private RegistrationData $data;

    private UserRegistrationResult $result;

    private UserRegistrationCreatedEvent $event;

    public function testResult(): void
    {
        $this->assertSame($this->result, $this->event->getUserRegistrationResult());

        $newResult = new UserRegistrationResult($this->entity, '321', false);
        $this->event->setUserRegistrationResult($newResult);
        $this->assertSame($newResult, $this->event->getUserRegistrationResult());
    }

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
        $this->entity = new UserRegistration('bob@gmail.com', new DateTimeImmutable(), 'foo', '123');
        $this->result = new UserRegistrationResult($this->entity, '123', false);
        $this->event = new UserRegistrationCreatedEvent($this->data, $this->result);
    }
}