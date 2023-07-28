<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\PermissionGroup;
use App\Model\Repository\PermissionGroupRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the PermissionGroup repository.
 */
class PermissionGroupRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $repository = $this->getPermissionGroupRepository();

        $group = new PermissionGroup('new_group', 'New group', 1);
        $repository->savePermissionGroup($group, true);
        $id = $group->getId();

        $loadedGroup = $repository->find($id);
        $this->assertNotNull($loadedGroup);
        $this->assertSame($group->getId(), $loadedGroup->getId());

        $repository->removePermissionGroup($group, true);
        $loadedGroup = $repository->find($id);
        $this->assertNull($loadedGroup);
    }

    public function testCreate(): void
    {
        $repository = $this->getPermissionGroupRepository();
        $group = $repository->createPermissionGroup('new_group', 'New group', 1);

        $this->assertSame('new_group', $group->getName());
        $this->assertSame('New group', $group->getLabel());
        $this->assertSame(1, $group->getPriority());
    }

    public function testFindAll(): void
    {
        $repository = $this->getPermissionGroupRepository();
        $groups = $repository->findAll();

        $names = [];
        foreach ($groups as $group)
        {
            $names[] = $group->getName();
        }

        $this->assertCount(2, $names);
        $this->assertContains('group1', $names);
        $this->assertContains('group2', $names);
    }

    private function getPermissionGroupRepository(): PermissionGroupRepository
    {
        $container = static::getContainer();

        /** @var PermissionGroupRepository $repository */
        $repository = $container->get(PermissionGroupRepository::class);

        return $repository;
    }
}