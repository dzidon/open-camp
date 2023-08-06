<?php

namespace App\Service\Command;

use App\Model\Repository\RoleRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Assigns the super admin role to a user.
 */
#[AsCommand(name: 'app:assign-super-admin-to-user')]
class AssignSuperAdminToUser extends Command
{
    private RoleRepositoryInterface $roleRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(RoleRepositoryInterface $roleRepository, UserRepositoryInterface $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $user = $this->userRepository->findOneByEmail($email);

        if ($user === null)
        {
            $output->writeln('There is no user with the given email.');

            return Command::FAILURE;
        }

        $role = $this->roleRepository->findOneByLabel('Super admin');

        if ($role === null)
        {
            $output->writeln('There is no Super admin role.');

            return Command::FAILURE;
        }

        $user->setRole($role);
        $this->userRepository->saveUser($user, true);

        $output->writeln(sprintf('Super admin role successfully assigned to user "%s".', $email));

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setHelp('Assigns the super admin role to a user.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
        ;
    }
}