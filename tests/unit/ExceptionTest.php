<?php

namespace unit;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use WorldText\Exception;

class ExceptionTest extends TestCase
{
    public function testFactoryMethod()
    {
        // Arrange
        $request = new Request('GET', '/');
        $response = new Response();
        $body = $response->getBody();
        $body->write('{"error": 1000, "desc": "Authorisation Failure"}');
        $response = $response->withBody($body);
        $clientException = new ClientException('Message', $request, $response);

        // Act
        $exception = Exception::createFromParent($clientException);

        // Assert
        $this->assertEquals($clientException->getMessage(), $exception->getMessage());
        $this->assertEquals($clientException->getRequest(), $exception->getRequest());
        $this->assertEquals($clientException->getResponse(), $exception->getResponse());
        $this->assertEquals($clientException->getPrevious(), $exception->getPrevious());
        $this->assertEquals($clientException->getHandlerContext(), $exception->getHandlerContext());
        $this->assertEquals(1000, $exception->getError());
        $this->assertEquals('Authorisation Failure', $exception->getDescription());
    }
}
