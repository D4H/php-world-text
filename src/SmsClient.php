<?php

namespace WorldText;

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use WorldText\Contracts\SmsClient as Contract;
use WorldText\Traits\CanCreateHandlerStack;
use WorldText\Traits\HasHttpClient;
use function GuzzleHttp\Psr7\parse_query;

class SmsClient implements Contract
{
    use HasHttpClient, CanCreateHandlerStack;

    const VERSION = '3.0.0';

    /**
     * @var bool
     */
    private $simulate;

    /**
     * @param string $accountId
     * @param string $apiKey
     *
     * @return SmsClient
     */
    public static function create($accountId, $apiKey)
    {
        $stack = self::createHandlerStack($accountId, $apiKey);

        $http = new Client([
            'handler' => $stack,
            'base_uri' => 'https://sms.world-text.com/v2.0/',
        ]);

        $client = new self($http, $stack);

        $stack->push(Middleware::mapRequest(function (RequestInterface $request) use ($client) {
            $query = parse_query($request->getUri()->getQuery());

            if ($client->simulate) {
                $query['sim'] = true;
            }

            return $request->withUri($request->getUri()->withQuery(http_build_query($query)));
        }));

        return $client;
    }

    /**
     * @param bool $enable
     *
     * @return self
     */
    public function simulate($enable = true)
    {
        $this->simulate = $enable;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function send($number, $message, array $options = [])
    {
        // required params
        $query = [
            'dstaddr' => $number,
            'txt' => $message,
        ];

        // filter options
        $allowed = ['clientref', 'data', 'dcs', 'delay', 'expiry', 'multipart', 'sendid', 'srcaddr'];
        $query += array_intersect_key($options, array_flip((array) $allowed));

        // set encoding
        if ($this->isUtf8($message)) {
            $query['enc'] = 'UnicodeBigUnmarked';
        }

        // execute request
        $response = $this->http->request('PUT', 'sms/send', ['query' => $query]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        return $data['message'];
    }

    /**
     * @inheritDoc
     */
    public function cost($number)
    {
        $response = $this->http->request('GET', 'sms/cost', ['query' => ['dstaddr' => $number]]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        return intval($data['credits']);
    }

    /**
     * @inheritDoc
     */
    public function query($messageId)
    {
        $response = $this->http->request('GET', 'sms/query', ['query' => ['msgid' => $messageId]]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        unset($data['status']);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function sendGroup($groupId, $message, array $options = [])
    {
        // required params
        $query = [
            'grpid' => $groupId,
            'txt' => $message,
        ];

        // filter options
        $allowed = [
            'clientref', 'data', 'dcs', 'multipart', 'rdelay', 'repeats', 'sendid', 'srcaddr', 'subs', 'templates'
        ];
        $query += array_intersect_key($options, array_flip((array) $allowed));

        // set encoding
        if ($this->isUtf8($message)) {
            $query['enc'] = 'UnicodeBigUnmarked';
        }

        // execute request
        $response = $this->http->request('PUT', 'group/send', ['query' => $query]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        return $data['message'];
    }

    /**
     * @inheritDoc
     */
    public function groupCost($groupId)
    {
        $response = $this->http->request('GET', 'group/cost', ['query' => ['grpid' => $groupId]]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        return intval($data['credits']);
    }

    /**
     * @param $string
     *
     * @return false
     */
    private function isUtf8($string)
    {
        return preg_match('/(?:
        [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )+/xs', $string);
    }
}
