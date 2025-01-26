<?php

namespace App\Transformer\Movie;

use App\DTO\Movie\MovieDTO;
use App\DTO\Movie\MovieListEntry;

readonly class MovieListEntryTransformer
{
    public function __invoke(MovieDTO $movie): MovieListEntry
    {
        return new MovieListEntry(
            id: $movie->getId(),
            title: $movie->getTitle(),
        );
    }
}