<?php

namespace App\Tests\Model\Repository;

use App\Enum\Search\Data\Admin\RoleSortEnum;
use App\Form\DataTransfer\Data\Admin\RoleSearchData;
use App\Model\Entity\Role;
use App\Model\Repository\RoleRepository;
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

    public function testFindAll(): void
    {
        $repository = $this->getRoleRepository();

        $roles = $repository->findAll();
        $this->assertSame(['Super admin', 'Admin'], $this->getRoleLabels($roles));
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

    public function testFindOneByLabel(): void
    {
        $repository = $this->getRoleRepository();

        $role = $repository->findOneByLabel('Super admin');
        $this->assertSame('Super admin', $role->getLabel());
    }

    public function testGetAdminPaginator(): void
    {
        $roleRepository = $this->getRoleRepository();

        $data = new RoleSearchData();
        $paginator = $roleRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $labels = $this->getRoleLabels($paginator->getCurrentPageItems());
        $this->assertSame(['Admin', 'Super admin'], $labels);
    }

    public function testGetAdminPaginatorWithLabel(): void
    {
        $roleRepository = $this->getRoleRepository();

        $data = new RoleSearchData();
        $data->setPhrase('Super');

        $paginator = $roleRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $labels = $this->getRoleLabels($paginator->getCurrentPageItems());
        $this->assertSame(['Super admin'], $labels);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $roleRepository = $this->getRoleRepository();

        $data = new RoleSearchData();
        $data->setSortBy(RoleSortEnum::CREATED_AT_DESC);

        $paginator = $roleRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $labels = $this->getRoleLabels($paginator->getCurrentPageItems());
        $this->assertSame(['Admin', 'Super admin'], $labels);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $roleRepository = $this->getRoleRepository();

        $data = new RoleSearchData();
        $data->setSortBy(RoleSortEnum::CREATED_AT_ASC);

        $paginator = $roleRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

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