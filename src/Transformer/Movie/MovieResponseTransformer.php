<?php

namespace App\Transformer\Movie;

use App\DTO\Movie\MovieDTO;

class MovieResponseTransformer
{
    public function __invoke(array $response): array
    {
        $hits = $response['hits']['hits'] ?? [];
        $movies = [];

        foreach ($hits as $hit) {
            $source = $hit['_source'];

            $movies[] = new MovieDTO(
                $hit['_id'],
                $source['title'],
                (int)$source['release_year'],
                $source['genres'] ?? [],
                $source['description']
            );
        }

        return $movies;
    }
}