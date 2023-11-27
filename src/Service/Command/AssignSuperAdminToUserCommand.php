<?php

namespace App\Service\Command;

use App\Model\Event\Admin\Role\SuperAdminRoleAssignEvent;
use App\Model\Repository\RoleRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Assigns the super admin role to a user.
 */
#[AsCommand(name: 'app:assign-super-admin-to-user')]
class AssignSuperAdminToUserCommand extends Command
{
    private RoleRepositoryInterface $roleRepository;

    private UserRepositoryInterface $userRepository;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(RoleRepositoryInterface  $roleRepository,
                                UserRepositoryInterface  $userRepository,
                                EventDispatcherInterface $eventDispatcher)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;

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

        $event = new SuperAdminRoleAssignEvent($user, $role);
        $this->eventDispatcher->dispatch($event, $event::NAME);

        $output->writeln(sprintf('Super admin role successfully assigned to user "%s".', $email));

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setHelp('Assigns the "Super admin" role to a user.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
        ;
    }
}