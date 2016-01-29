<?php

namespace Battleship;

use Battleship\Player\Player;
use Battleship\Player\PlayerId;

class Referee
{
    /**
     * @var PlayerId
     */
    private $currentPlayerId;

    /**
     * @var PlayerId
     */
    private $opponentPlayerId;

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

    /**
     * @var int
     */
    private $sleepTime;

    public function __construct(Player $playerA, Player $playerB, $sleepTime = 0)
    {
        $this->currentPlayerId = PlayerId::fromA();
        $this->opponentPlayerId = $this->currentPlayerId->opponent();

        $this->players = [
            $this->currentPlayerId->value() => $playerA,
            $this->opponentPlayerId->value() => $playerB,
        ];

        $this->sleepTime = $sleepTime;
        $this->turn = 1;
        $this->winner = null;
        $this->reason = null;
    }

    public function play()
    {
        try {
            $this->tryToStartTheGameOnPlayer($this->currentPlayerId);
            $this->tryToStartTheGameOnPlayer($this->opponentPlayerId);

            while (!$this->isGameFinished()) {
                $this->playTurn();
            }
        } catch (\Exception $e) {
            $this->reason = $e->getMessage();
        } finally {
            $this->tryToFinishGameOnPlayer($this->currentPlayerId);
            $this->tryToFinishGameOnPlayer($this->opponentPlayerId);
        }

        return new GameResult(
            $this->winner,
            $this->reason,
            $this->turn
        );
    }

    /**
     * @param $player
     *
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
        $this->players[$playerId->value()]->startGame();
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
        for ($step = 0; $step < 1; ++$step) {
            $shot = $this->tryToAskForNextShotToPlayer($this->currentPlayerId, $this->opponentPlayerId);
            $shotResult = $this->tryToShootToPlayer($this->currentPlayerId, $this->opponentPlayerId, $shot);
            $this->checkIfShotResultIsCorrect($shot, $shotResult);
            $this->tryToInformLastShotResult($shotResult);

            if ($this->isOpponentDefeated()) {
                $this->declareWinner($this->currentPlayerId, 'You destroy all your enemy\'s ships!');
                break;
            }

            $this->swapTurns();
            $this->sleepCall();
        }
    }

    /**
     * @param $currentPlayerId
     * @param $nextPlayerId
     *
     * @return Hole
     *
     * @throws \Exception
     */
    private function tryToAskForNextShotToPlayer($currentPlayerId, $nextPlayerId)
    {
        $currentPlayerIdValue = $currentPlayerId->value();
        try {
            return $this->players[$currentPlayerIdValue]->fire();
        } catch (\Exception $e) {
            $this->declareWinner($nextPlayerId, 'Your opponent did not fire properly!');
            throw $e;
        }
    }

    /**
     * @param $currentPlayerId
     * @param $nextPlayerId
     * @param $shot
     *
     * @return array
     *
     * @throws \Exception
     */
    private function tryToShootToPlayer($currentPlayerId, $nextPlayerId, $shot)
    {
        try {
            return $this->players[$nextPlayerId->value()]->shotAt($shot);
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
        return $this->sleepTime ? sleep($this->sleepTime) : null;
    }

    /**
     * @param Hole $shot
     * @param $shotResult
     *
     * @return bool
     *
     * @throws AllShipsAreNotPlacedException
     * @throws \Exception
     */
    private function checkIfShotResultIsCorrect($shot, $shotResult)
    {
        $refereeShotResult = $this->players[$this->opponentPlayerId->value()]->shot($shot);
        if ($refereeShotResult !== $shotResult) {
            $this->declareWinner($this->currentPlayerId, 'Your opponent did not respond the correct result to the shot you send it!');
            throw new \Exception($this->reason);
        }
    }

    /**
     * @param $shotResult
     */
    private function tryToInformLastShotResult($shotResult)
    {
        try {
            $this->players[$this->currentPlayerId->value()]->lastShotResult($shotResult);
        } catch (\Exception $e) {
        }
    }

    private function swapTurns()
    {
        $this->currentPlayerId = $this->opponentPlayerId;
        $this->opponentPlayerId = $this->currentPlayerId->opponent();
        ++$this->turn;
    }

    /**
     * @return bool
     */
    protected function isOpponentDefeated()
    {
        return $this->players[$this->opponentPlayerId->value()]->areAllShipsSunk();
    }

    private function tryToFinishGameOnPlayer(PlayerId $playerId)
    {
        try {
            $this->players[$playerId->value()]->finishGame();
        } catch (\Exception $e) {
        }
    }
}
