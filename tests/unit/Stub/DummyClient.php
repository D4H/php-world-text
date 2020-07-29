<?php

namespace WorldText\Unit\Stub;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use WorldText\Traits\CanCreateHandlerStack;
use WorldText\Traits\HasHttpClient;

class DummyClient
{
    use CanCreateHandlerStack, HasHttpClient;

    const VERSION = '3.0.0';

    /**
     * @param $accountId
     * @param $apiKey
     *
     * @return static
     */
    public static function create($accountId, $apiKey)
    {
        $stack = self::createHandlerStack($accountId, $apiKey);

        $http = new Client([
            'handler' => $stack,
            'base_uri' => 'https://sms.world-text.com/v2.0/',
        ]);

        return new self($http, $stack);
    }

    /**
     * @throws GuzzleException
     *
     * @return mixed
     */
    public function hit()
    {
        $response = $this->http->request('GET', '/');

        return json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);
    }
}
