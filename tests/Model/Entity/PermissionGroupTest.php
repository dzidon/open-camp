<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\PermissionGroup;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class PermissionGroupTest extends TestCase
{
    private const NAME = 'name';
    private const LABEL = 'Label';
    private const PRIORITY = 1;

    private PermissionGroup $group;

    public function testId(): void
    {
        $id = $this->group->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

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

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->group->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->group->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->group = new PermissionGroup(self::NAME, self::LABEL, self::PRIORITY);
    }
}