Battleship Command Line
=======================

[![Build Status](https://travis-ci.org/restgames/battleship-client.svg)](https://travis-ci.org/restgames/battleship-client)

http://www.hasbro.com/common/instruct/Battleship.PDF

## Installation

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install

## Start a game

    ./bin/battleship play http://restgames.org/battleship http://restgames.org/battleship

## How to create your own battleship engine

You can code your own REST service in any language. However, we have developed some projects to use as skeletons:

* [PHP Silex Skeleton](https://github.com/restgames/battleship-rest-silex-skeleton)
