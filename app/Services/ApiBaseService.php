<?php

namespace App\Services;

use GuzzleHttp\Client;

class ApiBaseService
{
    protected $client;

    public function __construct($baseUri)
    {
        $this->client = new Client(['base_uri' => $baseUri]);
    }

    /**
     * base request for all API
     * @param $method
     * @param $path
     * @param array $options
     * @return \Illuminate\Support\Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $path, $options = [])
    {
        if (!isset($options['query'])) {
            $options['query'] = [];
        }

        $response = $this->client->request($method, $path, $options);
        if ($response->getBody()) {
            return collect(json_decode($response->getBody(), true));
        }
    }
}
