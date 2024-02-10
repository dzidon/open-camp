<?php

namespace App\Model\Event\User\User;

use App\Library\Data\User\BillingData;
use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class UserBillingUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.user.billing_update';

    private BillingData $data;

    private User $entity;

    public function __construct(BillingData $data, User $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getBillingData(): BillingData
    {
        return $this->data;
    }

    public function setBillingData(BillingData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getUser(): User
    {
        return $this->entity;
    }

    public function setUser(User $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}