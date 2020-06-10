<?php

namespace WorldText;

use GuzzleHttp\Client;
use WorldText\Contracts\GroupClient as Contract;
use WorldText\Traits\CanCreateHandlerStack;
use WorldText\Traits\HasHttpClient;

class GroupClient implements Contract
{
    use HasHttpClient, CanCreateHandlerStack;

    const VERSION = '3.0.0';

    /**
     * @param string $accountId
     * @param string $apiKey
     *
     * @return GroupClient
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
    public function make($name, $sourceAddress, $pin = '0000')
    {
        $query = [
            'name' => $name,
            'srcaddr' => $sourceAddress,
            'pin' => $pin,
        ];

        $response = $this->http->request('PUT', 'group/create', ['query' => $query]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        return intval($data['groupid']);
    }

    /**
     * @inheritDoc
     */
    public function destroy($id)
    {
        $this->http->request('DELETE', 'group/destroy', ['query' => ['grpid' => $id]]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function details($id)
    {
        $response = $this->http->request('GET', 'group/details', ['query' => ['grpid' => $id]]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        unset($data['status']);

        return $data['entry'];
    }

    /**
     * @inheritDoc
     */
    public function clear($id)
    {
        $response = $this->http->request('DELETE', 'group/contents', ['query' => ['grpid' => $id]]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        unset($data['status']);

        return intval($data['rows']);
    }

    /**
     * @inheritDoc
     */
    public function remove($id, $number)
    {
        $query = [
            'grpid' => $id,
            'dstaddr' => $number,
        ];

        $this->http->request('DELETE', 'group/entry', ['query' => $query]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function addMany($id, array $list)
    {
        $members = '';

        foreach ($list as $number => $name) {
            $members .= $number . ':' . $name . ',';
        }

        // Remove trailing comma.
        $members = mb_substr($members, 0, -1);

        $query = [
            'grpid' => $id,
            'members' => $members,
        ];

        $response = $this->http->request('PUT', 'group/entries', ['query' => $query]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        unset($data['status']);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function add($id, $name, $number)
    {
        $query = [
            'grpid' => $id,
            'name' => $name,
            'dstaddr' => $number,
        ];

        $response = $this->http->request('PUT', 'group/entry', ['query' => $query]);

        json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        $response = $this->http->request('GET', 'group/list');

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        unset($data['status']);

        return $data['group'];
    }

    /**
     * @inheritDoc
     */
    public function numbers($id)
    {
        $response = $this->http->request('GET', 'group/numbers', ['query' => ['grpid' => $id]]);

        $data = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        unset($data['status']);

        return $data['entry'];
    }

    /**
     * @inheritDoc
     */
    public function find($name)
    {
        $groups = $this->all();

        foreach ($groups as $group) {
            if ($group['name'] === $name) {
                return intval($group['id']);
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function exists($name)
    {
        return !is_null($this->find($name));
    }
}
