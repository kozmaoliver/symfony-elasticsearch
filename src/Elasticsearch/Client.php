<?php

namespace App\Elasticsearch;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class Client
{
    protected \Elastic\Elasticsearch\Client $client;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        // Build the Elasticsearch client
        $this->client = ClientBuilder::create()
            ->setHosts(['https://elasticsearch.webstack.localhost'])
            ->setApiKey('')
            ->build();
    }

    public function getClient(): \Elastic\Elasticsearch\Client
    {
        return $this->client;
    }
}
