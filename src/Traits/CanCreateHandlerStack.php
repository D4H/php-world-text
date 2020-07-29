<?php

namespace WorldText\Traits;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WorldText\Exception;
use function GuzzleHttp\Psr7\parse_query;

trait CanCreateHandlerStack
{
    /**
     * @param $accountId
     * @param $apiKey
     *
     * @return HandlerStack
     */
    private static function createHandlerStack($accountId, $apiKey)
    {
        $stack = HandlerStack::create();

        // Add query params to add authentication
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) use ($accountId, $apiKey) {
            $query = parse_query($request->getUri()->getQuery());
            $query['id'] = $accountId;
            $query['key'] = $apiKey;

            return $request->withUri($request->getUri()->withQuery(http_build_query($query)));
        }), 'world_text_auth');

        // Add user agent header
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) use ($accountId, $apiKey) {
            return $request->withHeader('User-Agent', 'world-text-php/' . self::VERSION);
        }), 'world_text_user_agent');

        // Enhance client exceptions to have accessors to the error data from world text
        $stack->before('http_errors', function (callable $next) {
            return function (RequestInterface $request, array $options) use ($next) {
                /** @var Promise $promise */
                $promise = $next($request, $options);
                return $promise->then(function (ResponseInterface $response) {
                    return $response;
                }, function (GuzzleException $exception) {
                    if ($exception instanceof ClientException) {
                        throw Exception::createFromParent($exception);
                    }

                    throw $exception;
                });
            };
        }, 'exception_wrapping');

        return $stack;
    }
}
