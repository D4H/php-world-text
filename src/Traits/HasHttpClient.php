<?php

namespace WorldText\Traits;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack as GuzzleHandlerStack;

trait HasHttpClient
{
    /**
     * @var ClientInterface
     */
    private $http;

    /**
     * @var GuzzleHandlerStack
     */
    private $handlerStack;

    /**
     * @param ClientInterface $http
     * @param GuzzleHandlerStack $handlerStack
     */
    private function __construct(ClientInterface $http, GuzzleHandlerStack $handlerStack)
    {
        $this->http = $http;
        $this->handlerStack = $handlerStack;
    }

    /**
     * @return GuzzleHandlerStack
     */
    public function getHandlerStack()
    {
        return $this->handlerStack;
    }
}
