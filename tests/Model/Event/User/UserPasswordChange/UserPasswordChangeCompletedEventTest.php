<?php

namespace App\Tests\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\UserPasswordChange;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCompletedEvent;
use App\Model\Library\UserPasswordChange\UserPasswordChangeCompletionResult;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserPasswordChangeCompletedEventTest extends TestCase
{
    private PlainPasswordData $data;

    private UserPasswordChange $entity;

    private UserPasswordChangeCompletionResult $result;

    private UserPasswordChangeCompletedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPlainPasswordData());

        $newData = new PlainPasswordData();
        $this->event->setPlainPasswordData($newData);
        $this->assertSame($newData, $this->event->getPlainPasswordData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getUserPasswordChange());

        $newEntity = new UserPasswordChange(new DateTimeImmutable(), 'bar', '321');
        $this->event->setUserPasswordChange($newEntity);
        $this->assertSame($newEntity, $this->event->getUserPasswordChange());
    }

    public function testResult(): void
    {
        $this->assertSame($this->result, $this->event->getUserPasswordChangeCompletionResult());

        $newResult = new UserPasswordChangeCompletionResult();
        $this->event->setUserPasswordChangeCompletionResult($newResult);
        $this->assertSame($newResult, $this->event->getUserPasswordChangeCompletionResult());
    }

    protected function setUp(): void
    {
        $this->data = new PlainPasswordData();
        $this->entity = new UserPasswordChange(new DateTimeImmutable(), 'foo', '123');
        $this->result = new UserPasswordChangeCompletionResult();
        $this->event = new UserPasswordChangeCompletedEvent($this->data, $this->entity, $this->result);
    }
}