<?php

namespace App\Tests\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PasswordChangeData;
use App\Model\Entity\UserPasswordChange;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCreateEvent;
use App\Model\Library\UserPasswordChange\UserPasswordChangeResult;
use DateTimeImmutable;
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

    public function testUserPasswordChangeResult(): void
    {
        $this->assertNull($this->event->getUserPasswordChangeResult());

        $userPasswordChange = new UserPasswordChange(new DateTimeImmutable(), 'abc', '123');
        $newResult = new UserPasswordChangeResult($userPasswordChange, '321', false);
        $this->event->setUserPasswordChangeResult($newResult);
        $this->assertSame($newResult, $this->event->getUserPasswordChangeResult());
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
        $this->data = new PasswordChangeData();
        $this->event = new UserPasswordChangeCreateEvent($this->data);
    }
}