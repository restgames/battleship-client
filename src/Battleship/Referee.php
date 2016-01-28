<?php

namespace Battleship;

use Battleship\Player\Player;
use Battleship\Player\PlayerId;

class Referee
{
    private $currentPlayerId;
    private $opponentPlayerId;

    /**
     * @var Game[]
     */
    private $games;

    /**
     * @var Grid[]
     */
    private $grids;

    /**
     * @var Player[]
     */
    private $players;

    /**
     * @var int
     */
    private $turn;

    /**
     * @var PlayerId
     */
    private $winner;

    public function __construct(Player $playerA, Player $playerB)
    {
        $this->currentPlayerId = PlayerId::fromA();
        $this->opponentPlayerId = $this->currentPlayerId->opponent();

        $this->games = [];
        $this->grids = [];
        $this->players = [
            $this->currentPlayerId->value() => $playerA,
            $this->opponentPlayerId->value() => $playerB
        ];

        $this->turn = 1;
        $this->winner = null;
        $this->reason = 'You destroy all your enemy\'s ships!';
    }

    public function play()
    {
        try {
            $this->tryToStartTheGameOnPlayer($this->currentPlayerId);
            $this->tryToStartTheGameOnPlayer($this->opponentPlayerId);

            while (!$this->isGameFinished()) {
                $this->playTurn();
            }
        } catch(\Exception $e) {
            $this->reason = $e->getMessage();
        }

        return new GameResult(
            $this->winner,
            $this->reason,
            $this->turn
        );
   }

    /**
     * @param $player
     * @throws \Exception
     */
    private function tryToStartTheGameOnPlayer(PlayerId $player)
    {
        try {
            $this->startGameOnPlayer($player);
        } catch (\InvalidArgumentException $e) {
            $this->declareWinner($player->opponent(), 'Your opponent did not return a valid board');
            throw $e;
        } catch (\Exception $e) {
            $this->declareWinner($player->opponent(), 'It was impossible to start the game with your opponent');
            throw $e;
        }
    }

    private function startGameOnPlayer(PlayerId $playerId)
    {
        $this->games[$playerId->value()] = $this->players[$playerId->value()]->startGame();
        $this->grids[$playerId->value()] = $this->games[$playerId->value()];
    }

    /**
     * @param $winner
     * @param $reason
     */
    private function declareWinner($winner, $reason)
    {
        $this->winner = $winner;
        $this->reason = $reason;
    }

    /**
     * @return bool
     */
    private function isGameFinished()
    {
        return $this->winner !== null && $this->turn < 100 * 2;
    }

    /**
     * @throws \Exception
     */
    private function playTurn()
    {
        for ($step = 0; $step < 1; $step++) {
            $shot = $this->tryToAskForNextShotToPlayer($this->currentPlayerId, $this->opponentPlayerId);
            $shotResult = $this->tryToShootToPlayer($this->currentPlayerId, $this->opponentPlayerId, $shot);
            $refereeShotResult = $this->checkIfShotResultIsCorrect($this->currentPlayerId, $this->opponentPlayerId, $shot, $shotResult);

            $this->players[$this->currentPlayerId->value()]->lastShotResult($this->games[$this->currentPlayerId->value()]->gameId(), $refereeShotResult);

            if ($this->grids[$this->opponentPlayerId->value()]->areAllShipsSunk()) {
                $this->winner = $this->currentPlayerId;
                throw new \Exception($this->reason);
            }

            $this->currentPlayerId = $this->opponentPlayerId;
            $this->opponentPlayerId = $this->currentPlayerId->opponent();
        }

        $this->sleepCall();
        $this->turn++;
    }

    /**
     * @param $currentPlayerId
     * @param $nextPlayerId
     * @return Hole
     * @throws \Exception
     */
    private function tryToAskForNextShotToPlayer($currentPlayerId, $nextPlayerId)
    {
        try {
            return $this->players[$currentPlayerId->value()]->fire($this->games[$currentPlayerId->value()]->gameId());
        } catch (\Exception $e) {
            $this->declareWinner($nextPlayerId, 'Your opponent did not fire properly!');
            throw $e;
        }
    }

    /**
     * @param $currentPlayerId
     * @param $nextPlayerId
     * @param $shot
     * @return array
     * @throws \Exception
     */
    private function tryToShootToPlayer($currentPlayerId, $nextPlayerId, $shot)
    {
        try {
            return $this->players[$nextPlayerId->value()]->shotAt($this->games[$nextPlayerId->value()]->gameId(), $shot);
        } catch (\Exception $e) {
            $this->declareWinner($currentPlayerId, 'Your opponent did not respond to the shot you send it!');
            throw $e;
        }
    }

    /**
     * @return int
     */
    protected function sleepCall()
    {
        sleep(1);
    }

    /**
     * @param $currentPlayerId
     * @param $nextPlayerId
     * @param $shot
     * @param $shotResult
     * @return int
     * @throws AllShipsAreNotPlacedException
     * @throws \Exception
     */
    private function checkIfShotResultIsCorrect($currentPlayerId, $nextPlayerId, $shot, $shotResult)
    {
        $refereeShotResult = $this->grids[$nextPlayerId->value()]->shot($shot);
        if ($refereeShotResult !== $shotResult) {
            $this->declareWinner($currentPlayerId, 'Your opponent did not respond the correct result to the shot you send it!');
            throw new \Exception($this->reason);
        }
        return $refereeShotResult;
    }
}