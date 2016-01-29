<?php

namespace Battleship\Player;

use Battleship\Game;
use Battleship\Hole;

abstract class Player
{
    /**
     * @return Game
     */
    abstract public function startGame();

    /**
     * @return Hole
     */
    abstract public function fire();

    /**
     * @param int $result
     * @return int
     */
    abstract public function lastShotResult($result);

    /**
     * @param Hole $hole
     * @return int
     */
    abstract public function shotAt(Hole $hole);

    abstract public function finishGame();

    /**
     * @return Game
     */
    abstract public function game();

    public function areAllShipsSunk()
    {
        return $this->game()->grid()->areAllShipsSunk();
    }

    public function shot($hole)
    {
        return $this->game()->grid()->shot($hole);
    }
}