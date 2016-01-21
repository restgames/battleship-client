Battleship Referee
==================

[![Build Status](https://travis-ci.org/restgames/battleship-client.svg)](https://travis-ci.org/restgames/battleship-client)

Use this tool when two mates have developed their own REST Service for playing Battleship and you want them to fight each other.

There are 5 REST API methods to implement. For more information about the API, check the [API.md](/API.md).

## Rules

The rules are the official ones from the Hasbro Board Game. You can find then here: [http://www.hasbro.com/common/instruct/Battleship.PDF]()

## How to create your own battleship engine

You can code your own REST service in any language. However, we have developed some projects to use as skeletons:

* [PHP Silex Skeleton](https://github.com/restgames/battleship-rest-silex-skeleton)

If you want to create yours, just implement in any language with ahy technology the following interface.

## API Detail

#### New game

Request:

    POST /battleship/game

Response:

    {
        gameId: "",
        grid: "0300222200030000000003100000000010005000001000500000100444000010000000000000000000000000000000000000"
    }

#### Call your shot!

Request:

    POST /battleship/game/:gameId/shot

Response:

    {
        letter: "A",
        number: 3
    }

#### Receive a shot from opponents

Request:

    POST /battleship/game/:gameId/fire/:letter/:number

Response:

    {
        result: 0
    }

* 0: Miss
* 1: Hit
* 2: Sunk!

#### Finishing a game

Referee will call this method after the game has ended. You can use this call for performing cleaning up tasks, such as removing the game from any persistence mechanism.

Request:

    DELETE /battleship/game/:gameId

Response:

    {
        result: 0
    }


## Run a war between two REST Services

You'll need PHP to run this referee.

#### Installation

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install

#### Start a game

    ./bin/battleship play http://restgames.org http://restgames.org

#### Output

    REST Games: Battleship
    Play more games at: https://github.com/restgames
    Player #1 (http://localhost:8080) has declared war to Player #2 (http://localhost:8080)!
    War has started and bombs are flying over our heads...

    ..........
    ..........
    ..........
    ..........
    ..........
    ..........
    ...

    Player #1 is the winner! Congratulations!
    You destroy all your enemy's ships!
    You did it in 63 turns.