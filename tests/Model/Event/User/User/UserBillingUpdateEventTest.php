<?php

namespace App\Tests\Model\Event\User\User;

use App\Library\Data\User\BillingData;
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

        $newData = new BillingData(true);
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

    protected function setUp(): void
    {
        $this->entity = new User('bob@gmail.com');
        $this->data = new BillingData(true);
        $this->event = new UserBillingUpdateEvent($this->data, $this->entity);
    }
}