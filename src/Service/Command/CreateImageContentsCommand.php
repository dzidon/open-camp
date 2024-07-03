<?php

namespace App\Service\Command;

use App\Model\Event\Admin\ImageContent\ImageContentsCreateEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Creates image contents.
 */
#[AsCommand(name: 'app:create-image-contents')]
class CreateImageContentsCommand extends Command
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
        $event = new ImageContentsCreateEvent();
        $this->eventDispatcher->dispatch($event, $event::NAME);

        $output->writeln('Image contents created.');

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setHelp('Creates editable image contents in the application.');
    }
}