<?php

namespace WorldText;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use WorldText\Contracts\AdminClient as Contract;
use WorldText\Traits\CanCreateHandlerStack;
use WorldText\Traits\HasHttpClient;

class AdminClient implements Contract
{
    use HasHttpClient, CanCreateHandlerStack;

    const VERSION = '3.0.0';

    /**
     * @param string $accountId
     * @param string $apiKey
     *
     * @return AdminClient
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
     * @inheritDoc
     */
    public function ping()
    {
        try {
            $this->http->request('GET', 'admin/ping');
        } catch (ClientException $exception) {
            // Unauthorized.
            if ($exception->getResponse()->getStatusCode() === 401) {
                return false;
            }

            throw $exception;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function credits()
    {
        $response = $this->http->request('GET', 'admin/credits');

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        return intval($data['credits']);
    }
}
