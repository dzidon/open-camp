<?php

namespace App\Command;

use App\Model\Entity\Role;
use App\Model\Repository\PermissionRepositoryInterface;
use App\Model\Repository\RoleRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates permissions and permission groups.
 */
#[AsCommand(name: 'app:create-role-super-admin')]
class CreateSuperAdminRole extends Command
{
    private PermissionRepositoryInterface $permissionRepository;
    private RoleRepositoryInterface $roleRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository, RoleRepositoryInterface $roleRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $permissions = $this->permissionRepository->findAll();
        $role = new Role('Super admin');

        foreach ($permissions as $permission)
        {
            $role->addPermission($permission);
        }

        $this->roleRepository->saveRole($role, true);
        $output->writeln('Super admin role created.');

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setHelp('Creates a super admin role with all permissions.')
        ;
    }
}