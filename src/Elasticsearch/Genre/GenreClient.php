<?php

namespace App\Elasticsearch\Genre;

use App\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Http\Promise\Promise;

class GenreClient extends Client
{
    public const INDEX = 'genre';

    public function findAll(): array
    {
        $response = $this->client->search([
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ]);

        if ($response instanceof Promise) {
            $response->then(function ($response) {
                return $response->asArray();
            });
        }

        $rawGenres = $response->asArray()['hits']['hits'];

        $genres = [];
        foreach ($rawGenres as $rawGenre) {
            $genres[$rawGenre['_source']['title']] = $rawGenre['_id'];
        }

        return $genres;
    }

    public function get(string $id): array
    {
        $params = [
            'index' => self::INDEX,
            'id' => $id,
        ];

        $response = $this->client->get($params);

        if ($response instanceof Promise) {
            $response->then(function ($response) {
                return $response->asArray();
            });
        }

        return $response->asArray();
    }

    public function add(array $data, bool $refresh = false): void
    {
        $params = [
            'index' => self::INDEX,
            'body' => $data,
            'refresh' => $refresh
        ];

        $this->client->index($params);
    }

    public function update(string $id, array $data): void
    {
        $params = [
            'index' => self::INDEX,
            'id' => $id,
            'body' => [
                'doc' => $data
            ]
        ];

        $this->client->update($params);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function createIndex(): void
    {
        $params = [
            'index' => self::INDEX,
            'body' => [
                'mappings' => [
                    'properties' => [
                        'title' => ['type' => 'text'],
                    ],
                ],
            ],
        ];

        $this->client->indices()->create($params);
    }

    public function purge(): void
    {
        $params = [
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass() // Deletes all documents
                ]
            ]
        ];

        $this->client->deleteByQuery($params);
    }

}