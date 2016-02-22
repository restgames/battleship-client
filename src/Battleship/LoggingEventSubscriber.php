<?php

namespace Battleship;

use Ddd\Domain\DomainEvent;
use Ddd\Domain\DomainEventSubscriber;
use Symfony\Component\Console\Output\OutputInterface;

class LoggingEventSubscriber implements DomainEventSubscriber
{
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct($output)
    {
        $this->output = $output;
    }

    /**
     * @param DomainEvent $aDomainEvent
     */
    public function handle($aDomainEvent)
    {
        $this->output->writeln($aDomainEvent->message());
    }

    /**
     * @param DomainEvent $aDomainEvent
     * @return bool
     */
    public function isSubscribedTo($aDomainEvent)
    {
        return true;
    }
}
