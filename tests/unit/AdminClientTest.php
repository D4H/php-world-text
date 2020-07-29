<?php

namespace WorldText\Unit;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use WorldText\AdminClient;

class AdminClientTest extends TestCase
{
    /**
     * @var AdminClient
     */
    private $client;

    /**
     * Setup
     */
    public function setup()
    {
        // Arrange
        $this->client = AdminClient::create('id', 'key');
    }

    public function testCredits()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"credits": 37}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $credits = $this->client->credits();

        // Assert
        $this->assertEquals(37, $credits);
        $this->assertEquals(
            'https://sms.world-text.com/v2.0/admin/credits?id=id&key=key',
            (string) $handler->getLastRequest()->getUri()
        );
    }

    public function testPing()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
            new Response(401, ['Content-Type' => 'application/json'], '{"desc": "foo", "error": "foo"}'),
            new Response(400, ['Content-Type' => 'application/json'], '{"desc": "foo", "error": "foo"}'),
        ]);
        $stack->setHandler($handler);

        // First call
        $this->assertTrue($this->client->ping());
        $this->assertEquals(
            'https://sms.world-text.com/v2.0/admin/ping?id=id&key=key',
            (string) $handler->getLastRequest()->getUri()
        );

        // Second call
        $this->assertFalse($this->client->ping());

        // Third call
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);
        $this->client->ping();
    }
}
