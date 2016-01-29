<?php

namespace Battleship\Client;

use Battleship\Game;
use Battleship\Grid;
use Battleship\Hole;
use Battleship\Player\LocalPlayer;
use Battleship\Referee;

class RefereeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function givenPlayer1WithInvalidBoardWhenStartingTheGameThenWinnerIsPlayer2()
    {
        $this->checkWinnerIs('Player #2', $this->playGameWithRefereeAndPlayers(
            $this->playerWithInvalidGrid(),
            $this->playerWithInvalidGrid()
        ));
    }

    private function playerWithInvalidGrid()
    {
        return $this->playerOnStartGameWillThrow(new \InvalidArgumentException());
    }

    /**
     * @param $exception
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function playerOnStartGameWillThrow($exception)
    {
        $player = $this->getMockBuilder('Battleship\Player\Player')->getMock();
        $player->method('startGame')->will(
            $this->throwException($exception)
        );

        return $player;
    }

    /**
     * @param $playerA
     * @param $playerB
     * @return \Battleship\GameResult
     */
    private function playGameWithRefereeAndPlayers($playerA, $playerB)
    {
        return (new Referee(
            $playerA,
            $playerB
        ))->play();
    }

    private function checkWinnerIs($winner, $gameResult)
    {
        $this->assertSame(
            $winner,
            $gameResult->winner()
        );
    }

    /**
     * @test
     */
    public function givenPlayer1WithConnectionErrorsWhenStartingTheGameThenWinnerIsPlayer2()
    {
        $this->checkWinnerIs('Player #2', $this->playGameWithRefereeAndPlayers(
            $this->playerWithConnectivityIssue(),
            $this->playerWithConnectivityIssue()
        ));
    }

    private function playerWithConnectivityIssue()
    {
        return $this->playerOnStartGameWillThrow(new \Exception());
    }

    /**
     * @test
     */
    public function givenPlayer2WithInvalidBoardWhenStartingTheGameThenWinnerIsPlayer1()
    {
        $this->checkWinnerIs('Player #1', $this->playGameWithRefereeAndPlayers(
            $this->playerWithValidGrid(),
            $this->playerWithInvalidGrid()
        ));
    }

    /**
     * @test
     */
    public function givenPlayer2WithConnectionErrorsWhenStartingTheGameThenWinnerIsPlayer1()
    {
        $this->checkWinnerIs('Player #1', $this->playGameWithRefereeAndPlayers(
            $this->playerWithValidGrid(),
            $this->playerWithConnectivityIssue()
        ));
    }

    private function playerWithValidGrid()
    {
        $battleshipGrid = Grid::fromString(
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
        );

        $player = $this->getMockBuilder('Battleship\Player\Player')->getMock();
        $player->method('startGame')->will(
            $this->returnValue(new Game(
                1,
                $battleshipGrid
            ))
        );

        return $player;
    }

    /**
     * @test
     */
    public function givenPlayersWhenPlayer1ShootsInvalidPositionThenWinnerIsPlayer2()
    {
        $player = $this->playerWithValidGrid();
        $player->method('fire')->will(
            $this->throwException(new \Exception())
        );

        $this->checkWinnerIs('Player #2', $this->playGameWithRefereeAndPlayers(
            $player,
            $this->playerWithValidGrid()
        ));
    }

    /**
     * @test
     */
    public function givenPlayersWhenPlayer2FailsAnsweringToShotThenWinnerIsPlayer1()
    {
        $player1 = $this->playerWithValidGrid();
        $player1->method('fire')->will(
            $this->returnValue(new Hole('A', 1))
        );

        $player2 = $this->playerWithValidGrid();
        $player2->method('shotAt')->will(
            $this->throwException(new \Exception())
        );

        $this->checkWinnerIs('Player #1', $this->playGameWithRefereeAndPlayers(
            $player1,
            $player2
        ));
    }

    /**
     * @test
     */
    public function givenPlayersWhenPlayer2ReturnsDifferentValueAnsweringToShotThenWinnerIsPlayer1()
    {
        $player1 = $this->playerWithValidGrid();
        $player1->method('fire')->will(
            $this->returnValue(new Hole('A', 1))
        );

        $player2 = $this->playerWithValidGrid();
        $player2->method('shotAt')->will(
            $this->returnValue(1)
        );

        $this->checkWinnerIs('Player #1', $this->playGameWithRefereeAndPlayers(
            $player1,
            $player2
        ));
    }

    /**
     * @test
     */
    public function givenSamePlayersWithSameStrategyWhenPlayingThenWinnerIsPlayer1()
    {
        $this->checkWinnerIs('Player #1', $this->playGameWithRefereeAndPlayers(
            new LocalPlayer(),
            new LocalPlayer()
        ));
    }

    /**
     * @test
     */
    public function givenDifferentPlayersAndPlayer2BetterThanPlayer1WhenPlayingThenWinnerIsPlayer2()
    {
        $this->checkWinnerIs('Player #2', $this->playGameWithRefereeAndPlayers(
            new LocalPlayer(-1),
            new LocalPlayer()
        ));
    }
}
