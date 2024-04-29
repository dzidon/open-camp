<?php

namespace App\Service\Command;

use App\Model\Event\Admin\Permission\PermissionsAndGroupsCreateEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Creates permissions and permission groups.
 */
#[AsCommand(name: 'app:create-permissions')]
class CreatePermissionsCommand extends Command
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
        $event = new PermissionsAndGroupsCreateEvent();
        $this->eventDispatcher->dispatch($event, $event::NAME);

        $output->writeln('Permissions and permission groups created.');

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('Creates permissions and permission groups used in the application.');
    }
}