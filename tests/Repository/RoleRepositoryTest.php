<?php

namespace App\Tests\Repository;

use App\Entity\Role;
use App\Enum\Search\Data\Admin\RoleSortEnum;
use App\Form\DataTransfer\Data\Admin\RoleSearchData;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the Role repository.
 */
class RoleRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $repository = $this->getRoleRepository();

        $role = new Role('New role');
        $repository->saveRole($role, true);
        $id = $role->getId();

        $loadedRole = $repository->find($id);
        $this->assertNotNull($loadedRole);
        $this->assertSame($role->getId(), $loadedRole->getId());

        $repository->removeRole($role, true);
        $loadedRole = $repository->find($id);
        $this->assertNull($loadedRole);
    }

    public function testCreate(): void
    {
        $repository = $this->getRoleRepository();
        $role = $repository->createRole('New role');

        $this->assertSame('New role', $role->getLabel());
    }

    public function testFindOneById(): void
    {
        $repository = $this->getRoleRepository();

        $loadedRole = $repository->findOneById(-10000);
        $this->assertNull($loadedRole);

        $role = new Role('New role');
        $repository->saveRole($role, true);

        $loadedRole = $repository->findOneById($role->getId());
        $this->assertSame($role->getId(), $loadedRole->getId());
    }

    public function testGetAdminPaginator(): void
    {
        $roleRepository = $this->getRoleRepository();

        $data = new RoleSearchData();
        $paginator = $roleRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame($paginator->getTotalItems(), 2);
        $this->assertSame($paginator->getPagesCount(), 1);
        $this->assertSame($paginator->getCurrentPage(), 1);
        $this->assertSame($paginator->getPageSize(), 2);

        $labels = $this->getRoleLabels($paginator->getCurrentPageItems());
        $this->assertSame(['Admin', 'Super admin'], $labels);
    }

    public function testGetAdminPaginatorWithLabel(): void
    {
        $roleRepository = $this->getRoleRepository();

        $data = new RoleSearchData();
        $data->setLabel('Super');

        $paginator = $roleRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame($paginator->getTotalItems(), 1);
        $this->assertSame($paginator->getPagesCount(), 1);
        $this->assertSame($paginator->getCurrentPage(), 1);
        $this->assertSame($paginator->getPageSize(), 2);

        $labels = $this->getRoleLabels($paginator->getCurrentPageItems());
        $this->assertSame(['Super admin'], $labels);
    }

    public function testGetAdminPaginatorSortByIdDesc(): void
    {
        $roleRepository = $this->getRoleRepository();

        $data = new RoleSearchData();
        $data->setSortBy(RoleSortEnum::ID_DESC);

        $paginator = $roleRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame($paginator->getTotalItems(), 2);
        $this->assertSame($paginator->getPagesCount(), 1);
        $this->assertSame($paginator->getCurrentPage(), 1);
        $this->assertSame($paginator->getPageSize(), 2);

        $labels = $this->getRoleLabels($paginator->getCurrentPageItems());
        $this->assertSame(['Admin', 'Super admin'], $labels);
    }

    public function testGetAdminPaginatorSortByIdAsc(): void
    {
        $roleRepository = $this->getRoleRepository();

        $data = new RoleSearchData();
        $data->setSortBy(RoleSortEnum::ID_ASC);

        $paginator = $roleRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame($paginator->getTotalItems(), 2);
        $this->assertSame($paginator->getPagesCount(), 1);
        $this->assertSame($paginator->getCurrentPage(), 1);
        $this->assertSame($paginator->getPageSize(), 2);

        $labels = $this->getRoleLabels($paginator->getCurrentPageItems());
        $this->assertSame(['Super admin', 'Admin'], $labels);
    }

    private function getRoleLabels(array $roles): array
    {
        $labels = [];
        /** @var Role $role */
        foreach ($roles as $role)
        {
            $labels[] = $role->getLabel();
        }

        return $labels;
    }

    private function getRoleRepository(): RoleRepository
    {
        $container = static::getContainer();

        /** @var RoleRepository $repository */
        $repository = $container->get(RoleRepository::class);

        return $repository;
    }
}