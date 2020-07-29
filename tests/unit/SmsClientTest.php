<?php

namespace WorldText\Unit;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use WorldText\SmsClient;

class SmsClientTest extends TestCase
{
    /**
     * @var SmsClient
     */
    private $client;

    /**
     * Setup
     */
    public function setup()
    {
        // Arrange
        $this->client = SmsClient::create('id', 'key');
    }

    public function testSimulateAndCost()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"credits": 37}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $cost = $this->client->simulate()->cost('+123');

        // Assert
        $this->assertEquals(37, $cost);
        $this->assertEquals('GET', $handler->getLastRequest()->getMethod());
        $this->assertEquals(
            'https://sms.world-text.com/v2.0/sms/cost?dstaddr=%2B123&id=id&key=key&sim=1',
            (string) $handler->getLastRequest()->getUri()
        );
    }

    public function testQuery()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status": "sent"}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->query('123');

        // Assert
        $this->assertEquals([], $result);
        $this->assertEquals('GET', $handler->getLastRequest()->getMethod());
        $this->assertEquals(
            'https://sms.world-text.com/v2.0/sms/query?msgid=123&id=id&key=key',
            (string) $handler->getLastRequest()->getUri()
        );
    }

    public function testGroupCost()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"credits": "555"}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $cost = $this->client->groupCost(37);

        // Assert
        $this->assertEquals(555, $cost);
        $this->assertEquals('GET', $handler->getLastRequest()->getMethod());
        $this->assertEquals(
            'https://sms.world-text.com/v2.0/group/cost?grpid=37&id=id&key=key',
            (string) $handler->getLastRequest()->getUri()
        );
    }

    public function testSend()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"message": {"id": 123,"status":"pending"}}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->send('+123', 'message', ['delay' => 60]);

        // Assert
        $this->assertEquals(['id' => 123, 'status' => 'pending'], $result);
        $this->assertEquals(
            'https://sms.world-text.com/v2.0/sms/send?dstaddr=%2B123&txt=message&delay=60&id=id&key=key',
            (string) $handler->getLastRequest()->getUri()
        );
    }

    public function testSendEncoded()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"message": {"id": 123,"status":"pending"}}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->send('+123', 'über');

        // Assert
        $this->assertEquals(['id' => 123, 'status' => 'pending'], $result);
        $this->assertEquals(
            'https://sms.world-text.com/v2.0/sms/send?dstaddr=%2B123&txt=%C3%BCber&enc=UnicodeBigUnmarked&id=id&key=key',
            (string) $handler->getLastRequest()->getUri()
        );
    }

    public function testSendToGroup()
    {
        // Arrange
        $stack = $this->client->getHandlerStack();
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"message": {"id": 123,"status":"pending"}}'),
        ]);
        $stack->setHandler($handler);

        // Act
        $result = $this->client->sendGroup(123, 'über', ['srcaddr' => '+123']);

        // Assert
        $this->assertEquals(['id' => 123, 'status' => 'pending'], $result);
        $this->assertEquals(
            'https://sms.world-text.com/v2.0/group/send?grpid=123&txt=%C3%BCber&srcaddr=%2B123&enc=UnicodeBigUnmarked&id=id&key=key',
            (string) $handler->getLastRequest()->getUri()
        );
    }
}
