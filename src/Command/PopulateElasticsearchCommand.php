<?php

namespace App\Command;

use App\Elasticsearch\Genre\GenreClient;
use App\Elasticsearch\Movie\MovieClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand('database:fixture')]
class DatabaseFixtureCommand
{

    public function __construct(
        private GenreClient $genreClient,
        private MovieClient $movieClient,
    )
    {
    }

    public function execute(): int
    {


        return Command::SUCCESS;
    }
}