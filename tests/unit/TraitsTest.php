<?php

namespace WorldText\Unit;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use WorldText\Exception;
use WorldText\Unit\Stub\DummyClient;

class TraitsTest extends TestCase
{
    /**
     * @var DummyClient
     */
    private $client;

    /**
     * Setup
     */
    public function setup()
    {
        // Arrange
        $this->client = DummyClient::create('id', 'key');
    }

    public function testFailure()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(400, ['Content-Type' => 'application/json'], '{"desc": "foo", "error": "foo"}'),
        ]);
        $stack->setHandler($handler);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionCode(400);

        // Act
        $this->client->hit();
    }

    public function testUserAgent()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"test": "foo"}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->hit();

        // Assert
        $this->assertEquals(['test' => 'foo'], $result);
        $this->assertEquals('world-text-php/' . DummyClient::VERSION, $handler->getLastRequest()->getHeaderLine('User-Agent'));
    }

    public function testQueryString()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"test": "foo"}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->hit();

        // Assert
        $this->assertEquals(['test' => 'foo'], $result);
        $this->assertEquals('id=id&key=key', $handler->getLastRequest()->getUri()->getQuery());
    }
}
