<?php

namespace App\Service\Command;

use App\Model\Event\Admin\Role\SuperAdminRoleInitializeEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Initializes a super admin role with all permissions.
 */
#[AsCommand(name: 'app:initialize-super-admin-role')]
class InitializeSuperAdminRoleCommand extends Command
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = new SuperAdminRoleInitializeEvent();
        $this->eventDispatcher->dispatch($event, $event::NAME);

        $output->writeln('Super admin role with all permissions initialized.');

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('Initializes a super admin role with all permissions.');
    }
}