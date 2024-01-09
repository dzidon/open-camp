<?php

namespace App\Service\Command;

use App\Model\Event\Admin\PaymentMethod\PaymentMethodsCreateEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Creates payment methods.
 */
#[AsCommand(name: 'app:create-payment-methods')]
class CreatePaymentMethodsCommand extends Command
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
        $event = new PaymentMethodsCreateEvent();
        $this->eventDispatcher->dispatch($event, $event::NAME);

        $output->writeln('Payment methods created.');

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setHelp('Creates payment methods used in the application.')
        ;
    }
}