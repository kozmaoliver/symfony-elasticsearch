<?php

namespace App\Command;

use App\Elasticsearch\Genre\GenreClient;
use App\Elasticsearch\Movie\MovieClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Factory as Faker;

#[AsCommand(
    name: 'app:populate-database',
    description: 'Populates Elasticsearch with random genres and movies.'
)]
class PopulateElasticsearchCommand extends Command
{

    public function __construct(
        private readonly GenreClient $genreClient,
        private readonly MovieClient $movieClient,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('movieCount', InputArgument::OPTIONAL, 'The number of movies to generate', 100)
            ->addOption('purge', 'p', InputOption::VALUE_NONE, 'Purge the indices before populating');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('purge')) {
            $output->writeln('Purging Elasticsearch indices...');

            $this->genreClient->purge();
            $this->movieClient->purge();

            $output->writeln('Indices purged.');
        }

        $faker = Faker::create();

        $genres = [];
        for ($i = 0; $i < 25; $i++) {
            $genreTitle = $faker->unique()->word;
            $genres[$genreTitle] = [
                'title' => ucfirst($genreTitle)
            ];
            $this->genreClient->add($genres[$genreTitle], true);
        }

        $output->writeln('25 genres added.');

        $genreIds = $this->genreClient->findAll();

        $numMovies = $input->getArgument('movieCount');
        for ($i = 0; $i < $numMovies; $i++) {
            $randomGenres = array_rand($genreIds, mt_rand(1, 3));
            if (is_string($randomGenres)) {
                $randomGenres = [$randomGenres];
            }
            $movie = [
                'title' => $faker->sentence(3),
                'release_year' => $faker->year,
                'genres' => array_values(array_intersect_key($genreIds, array_flip($randomGenres))),
                'description' => $faker->paragraph
            ];
            $this->movieClient->add($movie);
        }

        $output->writeln("$numMovies movies added.");

        return Command::SUCCESS;
    }

}