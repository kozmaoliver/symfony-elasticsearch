<?php

namespace App\DTO\Movie;

readonly class MovieDTO
{
    public function __construct(
        private string $id,
        private string $title,
        private int    $releaseYear,
        private array  $genres,
        private string $description
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getReleaseYear(): int
    {
        return $this->releaseYear;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
