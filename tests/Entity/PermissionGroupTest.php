<?php

namespace App\Tests\Entity;

use App\Entity\PermissionGroup;
use PHPUnit\Framework\TestCase;

class PermissionGroupTest extends TestCase
{
    private const NAME = 'name';
    private const LABEL = 'Label';
    private const PRIORITY = 1;

    private PermissionGroup $group;

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->group->getName());

        $newName = 'new_name';
        $this->group->setName($newName);
        $this->assertSame($newName, $this->group->getName());
    }

    public function testLabel(): void
    {
        $this->assertSame(self::LABEL, $this->group->getLabel());

        $newLabel = 'New label';
        $this->group->setLabel($newLabel);
        $this->assertSame($newLabel, $this->group->getLabel());
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->group->getPriority());

        $newPriority = 321;
        $this->group->setPriority($newPriority);
        $this->assertSame($newPriority, $this->group->getPriority());
    }

    protected function setUp(): void
    {
        $this->group = new PermissionGroup(self::NAME, self::LABEL, self::PRIORITY);
    }
}