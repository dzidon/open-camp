<?php

namespace App\Model\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AbstractModelEvent extends Event
{
    private bool $isFlush = true;

    public function isFlush(): bool
    {
        return $this->isFlush;
    }

    public function setIsFlush(bool $isFlush): self
    {
        $this->isFlush = $isFlush;

        return $this;
    }
}