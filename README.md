Battleship Referee
==================

[![Build Status](https://travis-ci.org/restgames/battleship-client.svg)](https://travis-ci.org/restgames/battleship-client)

Use this tool when two mates have developed their own REST Service for playing Battleship and you want them to fight each other.

## Rules

The rules are the official ones from the Hasbro Board Game. You can find then here: http://www.hasbro.com/common/instruct/Battleship.PDF

## How to create your own battleship engine

You can code your own REST service in any language. However, we have developed some projects to use as skeletons:

* [PHP Silex Skeleton](https://github.com/restgames/battleship-rest-silex-skeleton)

If you want to create yours, just implement in any language with any technology the following interface.

## API Detail

There are 6 REST API methods to implement. For a full running example, check [PHP Silex Skeleton](https://github.com/restgames/battleship-rest-silex-skeleton).

#### New game

The referee will call this method to you and your opponent in order to check that all players are fair. You will return a game id in order to identify the game for all the calls, so you can play different games at the same time, and a string representing where you have placed your ships.

Request:

    POST /battleship/game

Response:

    {
        gameId: "550e8400-e29b-41d4-a716-446655440000",
        grid: "0300222200030000000003100000000010005000001000500000100444000010000000000000000000000000000000000000"
    }

- **gameId**: a string with any id that identify the game so you can find them on the next calls. Something such as an UUID should work.
- **grid**: a 100 length string representing a battleship grid (10x10) with all ships placed:
  - 1 x Carrier (5 holes and ID 1)
  - 1 x Battleship (4 holes and ID 2)
  - 1 x Cruiser (3 holes and ID 3)
  - 1 x Submarine (3 holes and ID 4)
  - 1 x Destroyer (2 holes and ID 5)

Example: The following string "0300222200030000000003100000000010005000001000500000100444000010000000000000000000000000000000000000" is the same as:

      1 2 3 4 5 6 7 8 9 10
    A 0 3 0 0 2 2 2 2 0 0
    B 0 3 0 0 0 0 0 0 0 0
    C 0 3 1 0 0 0 0 0 0 0
    D 0 0 1 0 0 0 5 0 0 0
    E 0 0 1 0 0 0 5 0 0 0
    F 0 0 1 0 0 4 4 4 0 0
    G 0 0 1 0 0 0 0 0 0 0
    H 0 0 0 0 0 0 0 0 0 0
    I 0 0 0 0 0 0 0 0 0 0
    J 0 0 0 0 0 0 0 0 0 0

#### Call your shot!

The referee will call this method to ask you where you want to shoot to your opponent. You must return a letter and a number.

Request:

    POST /battleship/game/:gameId/shot
    (for example, /battleship/game/550e8400-e29b-41d4-a716-446655440000/shot)

Response:

    {
        letter: "A",
        number: 3
    }

- **letter**: uppercase letter from A to J (10 rows)
- **number**: integer number from 1 to 10 (10 columns)

#### Receive result of the shot

The referee will give you the result of the last shot you send to your opponent. So, you can plan your next shots.

Request:

    POST /battleship/game/:gameId/shot-result/:result
    (for example, /battleship/game/550e8400-e29b-41d4-a716-446655440000/shot-result/1)

Response:

    {
        result: 0
    }

`result` can be what

#### Receive a shot from opponents

Request:

    POST /battleship/game/:gameId/fire/:letter/:number
    (for example, /battleship/game/550e8400-e29b-41d4-a716-446655440000/fire/A/1)
    
Response:

    {
        result: 0
    }

Result must be one of the following values:
- 0: Miss
- 1: Hit
- 2: Sunk!

#### Finishing a game

Referee will call this method after the game has ended. You can use this call for performing cleaning up tasks, such as removing the game from any persistence mechanism.

Request:

    DELETE /battleship/game/:gameId
    (for example, /battleship/game/550e8400-e29b-41d4-a716-446655440000)

Response:

    {
        result: 0
    }

`result` can be whatever you want.

## Run a war between two REST Services

You'll need PHP to run this referee.

#### Installation

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install

#### Start a game

    ./bin/battleship play http://localhost:8080 http://localhost:8080

#### Fighting a local service on friend's machine

If you want to make public a service running on your machine, you don't need a public webserver, just use a tool such as [Ngrok](https://ngrok.com/).

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
