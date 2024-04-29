<?php

namespace App\Service\Command;

use App\Model\Event\Admin\TextContent\TextContentsCreateEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Creates text contents.
 */
#[AsCommand(name: 'app:create-text-contents')]
class CreateTextContentsCommand extends Command
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
        $event = new TextContentsCreateEvent();
        $this->eventDispatcher->dispatch($event, $event::NAME);

        $output->writeln('Text contents created.');

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('Creates editable text contents in the application.');
    }
}