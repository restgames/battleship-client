<?php

namespace Battleship;

class GameResult
{
    private $winner;
    private $reason;
    private $turns;

    /**
     * @param string $winner
     * @param string $reason
     */
    public function __construct($winner, $reason, $turns)
    {
        $this->winner = $winner;
        $this->reason = $reason;
        $this->turns = $turns;
    }

    /**
     * @return string
     */
    public function winner()
    {
        return (string) $this->winner;
    }

    /**
     * @return string
     */
    public function reason()
    {
        return (string) $this->reason;
    }

    /**
     * @return int
     */
    public function turns()
    {
        return (int) $this->turns;
    }
}
