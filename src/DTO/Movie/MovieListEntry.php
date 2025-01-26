<?php

namespace App\DTO\Movie;

readonly class MovieListEntry
{
    public function __construct(
        private string $id,
        private string $title,
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
}