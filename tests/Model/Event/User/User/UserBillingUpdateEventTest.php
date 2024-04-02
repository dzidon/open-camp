<?php

namespace App\Tests\Model\Event\User\User;

use App\Library\Data\Common\BillingData;
use App\Model\Entity\User;
use App\Model\Event\User\User\UserBillingUpdateEvent;
use PHPUnit\Framework\TestCase;

class UserBillingUpdateEventTest extends TestCase
{
    private User $entity;

    private BillingData $data;

    private UserBillingUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getBillingData());

        $newData = new BillingData(false, true);
        $this->event->setBillingData($newData);
        $this->assertSame($newData, $this->event->getBillingData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getUser());

        $newUser = new User('bob.new@gmail.com');
        $this->event->setUser($newUser);
        $this->assertSame($newUser, $this->event->getUser());
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
        $this->entity = new User('bob@gmail.com');
        $this->data = new BillingData(false, true);
        $this->event = new UserBillingUpdateEvent($this->data, $this->entity);
    }
}