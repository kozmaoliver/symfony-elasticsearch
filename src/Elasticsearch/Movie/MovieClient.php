<?php

namespace App\Elasticsearch\Movie;

use App\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Http\Promise\Promise;

class MovieClient extends Client
{
    public const INDEX = 'movie';

    public function add(array $data): void
    {
        $params = [
            'index' => self::INDEX,
            'body' => $data,
        ];

        $this->client->index($params);
    }

    public function search(?string $keyword = null, int $page = 1, int $pageSize = 10): array
    {
        $params = [
            'index' => self::INDEX,
            'from' => ($page - 1)  * $pageSize,
            'size' => $pageSize,
        ];

        if ($keyword) {
            $params['body']['query'] = [
                'match' => [
                    'title' => $keyword,
                ]
            ];
        }

        $response = $this->client->search($params);

        if ($response instanceof Promise) {
            $response->then(function ($response) {
                return $response->asArray();
            });
        }

        return $response->asArray();
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
                        'release_year' => ['type' => 'integer'],
                        'genres' => ['type' => 'keyword'],
                        'description' => ['type' => 'text'],
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