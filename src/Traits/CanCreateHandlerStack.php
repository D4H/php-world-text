<?php

namespace WorldText\Traits;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
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
            return $request->withAddedHeader('User-Agent', 'world-text-php/' . self::VERSION);
        }), 'world_text_auth');

        // Enhance client exceptions to have accessors to the error data from world text
        $stack->push(function (callable $next) {
            return function (RequestInterface $request, array $options) use ($next) {
                try {
                    return $next($request, $options);
                } catch (ClientException $exception) {
                    throw Exception::createFromParent($exception);
                }
            };
        }, 'exception_wrapping');

        return $stack;
    }
}
