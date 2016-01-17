<?php

namespace Battleship\Console\Command;

use Battleship\Client\Referee;
use Battleship\Client\RestApiPlayer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PlayCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('play')
            ->setDescription('Start a battleship war between two players')
            ->addArgument(
                'player1',
                InputArgument::REQUIRED,
                'Player #1 API endpoint URI (http://restgame.org/battleship)'
            )
            ->addArgument(
                'player2',
                InputArgument::REQUIRED,
                'Player #2 API endpoint URI (http://restgame.org/battleship)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $player1 = $input->getArgument('player1');
        $player2 = $input->getArgument('player2');

        $referee = new Referee(
            new RestApiPlayer($player1),
            new RestApiPlayer($player2)
        );

        $winner = $referee->play();

        $output->writeln($winner);
    }
}