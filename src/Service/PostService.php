<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PostService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;        
    }

   public function fetchPostInformation(): array
    {
        $response = $this->client->request(
            'GET',
            'https://jsonplaceholder.typicode.com/posts'
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new \Exception("Erreur API : code $statusCode");
        }

        return $response->toArray();
    }

}
