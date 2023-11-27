<?php

namespace App\Tests\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PasswordChangeData;
use App\Model\Entity\UserPasswordChange;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCreatedEvent;
use App\Model\Library\UserPasswordChange\UserPasswordChangeResult;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserPasswordChangeCreatedEventTest extends TestCase
{
    private PasswordChangeData $data;

    private UserPasswordChangeResult $result;

    private UserPasswordChangeCreatedEvent $event;

    public function testResult(): void
    {
        $this->assertSame($this->result, $this->event->getUserPasswordChangeResult());

        $newResult = new UserPasswordChangeResult($this->entity, '321', false);
        $this->event->setUserPasswordChangeResult($newResult);
        $this->assertSame($newResult, $this->event->getUserPasswordChangeResult());
    }

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
        $this->entity = new UserPasswordChange(new DateTimeImmutable(), 'foo', '123');
        $this->result = new UserPasswordChangeResult($this->entity, '123', false);
        $this->event = new UserPasswordChangeCreatedEvent($this->data, $this->result);
    }
}