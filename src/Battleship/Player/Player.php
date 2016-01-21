<?php

namespace Battleship\Player;

use Battleship\Game;
use Battleship\Hole;

interface Player
{
    /**
     * @return Game
     */
    public function startGame();

    /**
     * @param string $gameId
     * @return Hole
     */
    public function fire($gameId);

    /**
     * @param string $gameId
     * @param Hole $hole
     * @return mixed
     */
    public function shotAt($gameId, Hole $hole);

    /**
     * @param string $gameId
     */
    public function finishGame($gameId);
}