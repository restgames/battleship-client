<?php

namespace Battleship\Client;

class RefereeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function givenPlayer1WithInvalidBoardWinnerIsPlayerB()
    {
        $invalidGridString = new \stdClass();
        $invalidGridString->gameId = '1';
        $invalidGridString->grid = str_repeat('0', 100);

        $playerB = $this->getMockBuilder('Battleship\Player')->getMock();
        $stub->method('startGame')->will($this->returnArgument());


        $playerB = new InvalidGridPlayer();

        $this->assertSame($winner, $playerB);
    }
}
