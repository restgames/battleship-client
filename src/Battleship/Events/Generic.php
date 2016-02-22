<?php

namespace Battleship\Events;

use Ddd\Domain\DomainEvent;

class Generic implements DomainEvent
{
    private $message;
    private $occurredOn;

    /**
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * @return \DateTime
     */
    public function occurredOn()
    {
        return $this->occurredOn;
    }
}
