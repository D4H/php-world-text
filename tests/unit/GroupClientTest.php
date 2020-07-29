<?php

namespace WorldText\Unit;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use WorldText\GroupClient;

class GroupClientTest extends TestCase
{
    /**
     * @var GroupClient
     */
    private $client;

    /**
     * Setup
     */
    public function setup()
    {
        // Arrange
        $this->client = GroupClient::create('id', 'key');
    }

    public function testMake()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(201, ['Content-Type' => 'application/json'], '{"groupid": 37}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $id = $this->client->make('test', '+123', '0001');

        // Assert
        $this->assertEquals(37, $id);
        $this->assertEquals('PUT', $handler->getLastRequest()->getMethod());
        $this->assertEquals('https://sms.world-text.com/v2.0/group/create?name=test&srcaddr=%2B123&pin=0001&id=id&key=key', (string) $handler->getLastRequest()->getUri());
    }

    public function testDestroy()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->destroy(37);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals('DELETE', $handler->getLastRequest()->getMethod());
        $this->assertEquals('https://sms.world-text.com/v2.0/group/destroy?grpid=37&id=id&key=key', (string) $handler->getLastRequest()->getUri());
    }

    public function testDetails()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"entry": {"id": 37}}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $details = $this->client->details(37);

        // Assert
        $this->assertEquals(['id' => 37], $details);
        $this->assertEquals('GET', $handler->getLastRequest()->getMethod());
        $this->assertEquals('https://sms.world-text.com/v2.0/group/details?grpid=37&id=id&key=key', (string) $handler->getLastRequest()->getUri());
    }

    public function testClear()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"rows": 100}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $rows = $this->client->clear(37);

        // Assert
        $this->assertEquals(100, $rows);
        $this->assertEquals('DELETE', $handler->getLastRequest()->getMethod());
        $this->assertEquals('https://sms.world-text.com/v2.0/group/contents?grpid=37&id=id&key=key', (string) $handler->getLastRequest()->getUri());
    }

    public function testRemove()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"rows": 100}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->remove(37, '+123');

        // Assert
        $this->assertTrue($result);
        $this->assertEquals('DELETE', $handler->getLastRequest()->getMethod());
        $this->assertEquals('https://sms.world-text.com/v2.0/group/entry?grpid=37&dstaddr=%2B123&id=id&key=key', (string) $handler->getLastRequest()->getUri());
    }

    public function testAddMany()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"test": "foo"}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->addMany(37, ['+123' => 'john', '+456' => 'jane']);

        // Assert
        $this->assertEquals(['test' => 'foo'], $result);
        $this->assertEquals('PUT', $handler->getLastRequest()->getMethod());
        $this->assertEquals('https://sms.world-text.com/v2.0/group/entries?grpid=37&members=%2B123%3Ajohn%2C%2B456%3Ajane&id=id&key=key', (string) $handler->getLastRequest()->getUri());
    }

    public function testAdd()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"test": "foo"}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->add(37, 'jane', '+456');

        // Assert
        $this->assertTrue($result);
        $this->assertEquals('PUT', $handler->getLastRequest()->getMethod());
        $this->assertEquals('https://sms.world-text.com/v2.0/group/entry?grpid=37&name=jane&dstaddr=%2B456&id=id&key=key', (string) $handler->getLastRequest()->getUri());
    }

    public function testNumbers()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"entry": []}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->numbers(37);

        // Assert
        $this->assertEquals([], $result);
        $this->assertEquals('GET', $handler->getLastRequest()->getMethod());
        $this->assertEquals('https://sms.world-text.com/v2.0/group/numbers?grpid=37&id=id&key=key', (string) $handler->getLastRequest()->getUri());
    }

    public function testAll()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"group":[{"id": 1,"name": "test"}]}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $groups = $this->client->all();

        // Assert
        $this->assertEquals([['id' => 1, 'name' => 'test']], $groups);
        $this->assertEquals('GET', $handler->getLastRequest()->getMethod());
        $this->assertEquals('https://sms.world-text.com/v2.0/group/list?id=id&key=key', (string) $handler->getLastRequest()->getUri());
    }

    public function testFind()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"group":[{"id": 1,"name": "test"}]}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"group":[{"id": 1,"name": "test"}]}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $id = $this->client->find('test');

        // Assert
        $this->assertEquals(1, $id);

        // Act
        $id = $this->client->find('foo');

        // Assert
        $this->assertNull($id);
    }

    public function testExists()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"group":[{"id": 1,"name": "test"}]}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"group":[{"id": 1,"name": "test"}]}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $exists = $this->client->exists('test');

        // Assert
        $this->assertTrue($exists);

        // Act
        $exists = $this->client->exists('bar');

        // Assert
        $this->assertFalse($exists);
    }
}
