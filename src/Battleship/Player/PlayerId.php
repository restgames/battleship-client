<?php

namespace Battleship\Player;

class PlayerId
{
    private $value;

    private function __construct($letter)
    {
        $this->value = $letter;
    }

    public static function fromA()
    {
        return new self(0);
    }

    public function opponent()
    {
        return new self(($this->value + 1) % 2);
    }

    public function __toString()
    {
        return $this->value();
    }

    public function value()
    {
        return 'Player #'.($this->value + 1);
    }
}