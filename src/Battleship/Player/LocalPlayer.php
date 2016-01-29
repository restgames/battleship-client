<?php

namespace Battleship\Player;

use Battleship\Game;
use Battleship\Grid;
use Battleship\Hole;

class LocalPlayer extends Player
{
    /**
     * @var Game
     */
    private $game;
    private $nextShot;

    public function __construct($shootingDirection = 1)
    {
        $this->game = null;

        if ($shootingDirection > 0) {
            $this->nextShot = -1;
            $this->shootingDirection = 1;
        } else {
            $this->nextShot = 100;
            $this->shootingDirection = -1;
        }
    }

    /**
     * @return Game
     */
    public function startGame()
    {
        $this->game = new Game(
            1,
            Grid::fromString(
                '0300222200'.
                '0300000000'.
                '0310000000'.
                '0010005000'.
                '0010005000'.
                '0010044400'.
                '0010000000'.
                '0000000000'.
                '0000000000'.
                '0000000000'
            )
        );
    }

    /**
     * @return Hole
     */
    public function fire()
    {
        $this->nextShot += $this->shootingDirection;

        $letters = Grid::letters();
        $numbers = Grid::numbers();

        return new Hole(
            $letters[$this->nextShot / count($numbers)],
            $numbers[$this->nextShot % count($numbers)]
        );
    }

    /**
     * @param int $result
     *
     * @return int
     */
    public function lastShotResult($result)
    {
    }

    /**
     * @param Hole $hole
     *
     * @return int
     */
    public function shotAt(Hole $hole)
    {
        return $this->game->grid()->shot($hole);
    }

    public function finishGame()
    {
    }

    public function game()
    {
        return $this->game;
    }
}
