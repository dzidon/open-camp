<?php

namespace App\Tests\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\UserPasswordChange;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCompleteEvent;
use App\Model\Library\UserPasswordChange\UserPasswordChangeCompletionResult;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserPasswordChangeCompleteEventTest extends TestCase
{
    private PlainPasswordData $data;

    private UserPasswordChange $entity;

    private UserPasswordChangeCompleteEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPlainPasswordData());

        $newData = new PlainPasswordData();
        $this->event->setPlainPasswordData($newData);
        $this->assertSame($newData, $this->event->getPlainPasswordData());
    }

    public function testUserPasswordChange(): void
    {
        $this->assertSame($this->entity, $this->event->getUserPasswordChange());

        $newEntity = new UserPasswordChange(new DateTimeImmutable(), 'bar', '321');
        $this->event->setUserPasswordChange($newEntity);
        $this->assertSame($newEntity, $this->event->getUserPasswordChange());
    }

    public function testResult(): void
    {
        $this->assertNull($this->event->getUserPasswordChangeCompletionResult());

        $newResult = new UserPasswordChangeCompletionResult();
        $this->event->setUserPasswordChangeCompletionResult($newResult);
        $this->assertSame($newResult, $this->event->getUserPasswordChangeCompletionResult());
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
        $this->data = new PlainPasswordData();
        $this->entity = new UserPasswordChange(new DateTimeImmutable(), 'foo', '123');
        $this->event = new UserPasswordChangeCompleteEvent($this->data, $this->entity);
    }
}