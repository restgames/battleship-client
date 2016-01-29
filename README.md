# Battleship Referee

[![Build Status](https://travis-ci.org/restgames/battleship-client.svg)](https://travis-ci.org/restgames/battleship-client)

This is the Referee for making two player play Battleship.

### What's REST Games?

Welcome to REST Games! Our goal is to provide you some coding challenges that go beyond the katas. You will implement a small JSON REST API that will play a well known game. The cool part comes when two mates develop the same JSON REST API and a _Referee_ can make them play one against the other. Cool, isn't it?

## Battleship rules

The rules are the official ones from the Hasbro original board game. You can find then here: http://www.hasbro.com/common/instruct/Battleship.PDF. A part of the original rules, if your REST APIs does not work properly (connectivity issues, returning wrong values, etc.) you will loose the game.

## How to create your own battleship engine

You can code your own REST service in any language. However, in order to help you start faster, we have developed for you some skeleton projects in different languages:

* [PHP Silex Skeleton](https://github.com/restgames/battleship-rest-silex-skeleton)
* Ruby Sinatra Skeleton waiting for contributions...
* Scala Play Skeleton waiting for contributions...

If you want to create yours, just choose your preferred language, any REST framework, any sort of persistence or AI mechanism that implement the following REST API interface.

## API Details

There are 6 REST API methods to implement. As a tip, check the skeletons for a good starting point.

#### Start Game

The referee will call this method to you and your opponent in order to start a game. You will return a game id so you can identify the game for all the next calls (you can be playing different games at the same time). You'll also return a string representing where you have placed your ships following the guide below.

You need to place 5 ships:
  - 1 x Carrier (5 holes and id #1)
  - 1 x Battleship (4 holes and id #2)
  - 1 x Cruiser (3 holes and id #3)
  - 1 x Submarine (3 holes and id #4)
  - 1 x Destroyer (2 holes and id #5)

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

This way, referee can check that nobody is cheating. You know, fair play ;)

Request:

    POST /battleship/game

Response:

    {
        gameId: "550e8400-e29b-41d4-a716-446655440000",
        grid: "0300222200030000000003100000000010005000001000500000100444000010000000000000000000000000000000000000"
    }

- **gameId**: a string with any id that identify the game so you can find them on the next calls. Something such as an UUID should work.
- **grid**: a 100 length string representing a battleship grid (10x10) with all ships placed.

#### Call your shot!

The referee will call this method to ask you where you want to shoot to your opponent. You must return a letter and a number representing the hole to shot at.

Request:

    POST /battleship/game/:gameId/shot
    (for example, /battleship/game/550e8400-e29b-41d4-a716-446655440000/shot)

Response:

    {
        letter: "A",
        number: 2
    }

- **letter**: uppercase letter from A to J (10 rows)
- **number**: integer number from 1 to 10 (10 columns)

#### Receive result of the shot

After giving a hole to shot to your opponent's grid, you need feedback about the result of the shot. With this information, you can build your strategy about where your next shot should be.

Request:

    POST /battleship/game/:gameId/shot-result/:shot-result
    (for example, /battleship/game/550e8400-e29b-41d4-a716-446655440000/shot-result/1)

- **shot-result**: uppercase letter from A to J (10 rows)

Response:

    {}

You can return whatever you want, the referee does not care about the response of this call.

#### Receive a shot from opponents

Life is not just shooting, you will also receive shots from your opponent. So you must return if the result was miss, hit or sunk.

Request:

    POST /battleship/game/:gameId/receive-shot/:letter/:number
    (for example, /battleship/game/550e8400-e29b-41d4-a716-446655440000/receive-shot/A/1)

Response:

    {
        result: 0
    }

Result must be one of the following values:
- 0: Miss
- 1: Hit
- 2: Sunk!

Referee will check that your result is correct, so don't cheat. **If you receive a shot at a already sunk ship, you should return sunk.**

#### Finishing a game

Referee will call this method after the game has ended. You can use this call for performing cleaning up tasks, such as removing the game from any persistence mechanism.

Request:

    DELETE /battleship/game/:gameId
    (for example, /battleship/game/550e8400-e29b-41d4-a716-446655440000)

Response:

    {}

You can return whatever you want, the referee does not care about the response of this call.

### Contribute

We are more than happy to receive contributions about new games, new skeletons, issues, etc. Have fun!
