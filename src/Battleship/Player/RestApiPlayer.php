<?php

namespace Battleship\Player;

use Battleship\Game;
use Battleship\Grid;
use Battleship\Hole;
use GuzzleHttp\Client;

class RestApiPlayer extends Player
{
    private $client;
    private $endpoint;

    /**
     * @var Game
     */
    private $game;
    private $gameId;

    /**
     * @param string $endpoint
     */
    public function __construct($endpoint)
    {
        $this->client = new Client();
        $this->endpoint = $endpoint;
        $this->game = null;
    }

    /**
     * @return Game
     */
    public function startGame()
    {
        $res = $this->client->request('POST', $this->endpoint.'/battleship/game');
        $response = json_decode($res->getBody());

        $this->game = new Game(
            $response->gameId,
            Grid::fromString($response->grid)
        );

        $this->gameId = $this->game()->gameId();

        return $this->game;
    }

    /**
     * @return Hole
     */
    public function fire()
    {
        $res = $this->client->request('POST', $this->endpoint.'/battleship/game/'.$this->gameId.'/shot');
        $response = json_decode($res->getBody());

        return new Hole(
            $response->letter,
            $response->number
        );
    }

    /**
     * @param int $result
     */
    public function lastShotResult($result)
    {
        $this->client->request('POST', $this->endpoint.'/battleship/game/'.$this->gameId.'/shot-result/'.$result);
    }

    /**
     * @param Hole $hole
     *
     * @return int
     */
    public function shotAt(Hole $hole)
    {
        $res = $this->client->request('POST', $this->endpoint.'/battleship/game/'.$this->gameId.'/receive-shot/'.$hole->letter().'/'.$hole->number());
        $response = json_decode($res->getBody());

        return $response->result;
    }

    public function finishGame()
    {
        $this->client->request('DELETE', $this->endpoint.'/battleship/game/'.$this->gameId);
    }

    /**
     * @return Game
     */
    public function game()
    {
        return $this->game;
    }
}
