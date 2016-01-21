<?php

namespace Battleship;

use Battleship\Player\Player;

class Referee
{
    const PLAYER_2 = 'Player #2';
    const PLAYER_1 = 'Player #1';
    /**
     * @var Player
     */
    private $playerA;

    /**
     * @var Player
     */
    private $playerB;

    /**
     * @var Grid
     */
    private $gridA;

    /**
     * @var Grid
     */
    private $gridB;

    public function __construct(Player $playerA, Player $playerB)
    {
        $this->playerA = $playerA;
        $this->playerB = $playerB;
        $this->gridA = null;
        $this->gridB = null;
        $this->gameA = null;
        $this->gameB = null;
    }

    public function play()
    {
        $winner = null;
        $reason = 'You destroy all your enemy\'s ships!';
        $turn = 1;

        try {
            try {
                $this->gameA = $this->playerA->startGame();
                $this->gridA = $this->gameA->grid();
            } catch(\InvalidArgumentException $e) {
                $winner = self::PLAYER_2;
            }

            try {
                $this->gameB = $this->playerB->startGame();
                $this->gridB = $this->gameB->grid();
            } catch(\InvalidArgumentException $e) {
                $winner = self::PLAYER_1;
            }

            while ($winner === null || $turn < 64 * 2) {
                echo '.';
                $shot = $this->playerA->fire($this->gameA->gameId());
                $shotResult = $this->playerB->shotAt($this->gameB->gameId(), $shot);
                $refereeShotResult = $this->gridB->shot($shot);

                if ($refereeShotResult !== $shotResult) {
                    $winner = self::PLAYER_1;
                    throw new \Exception('Shot result should be '.$refereeShotResult);
                    break;
                }

                if ($this->gridB->areAllShipsSunk()) {
                    $winner = self::PLAYER_1;
                    break;
                }

                $shot = $this->playerB->fire($this->gameB->gameId());
                $shotResult = $this->playerA->shotAt($this->gameA->gameId(), $shot);
                $refereeShotResult = $this->gridA->shot($shot);

                if ($refereeShotResult !== $shotResult) {
                    $winner = self::PLAYER_2;
                    throw new \Exception('Shot result should be '.$refereeShotResult);
                    break;
                }

                if ($this->gridA->areAllShipsSunk()) {
                    $winner = self::PLAYER_2;
                    break;
                }

                sleep(1);

                $turn++;
            }
        } catch(\Exception $e) {
            $reason = $e->getMessage();
        }

        return new GameResult($winner, $reason, $turn);
   }
}