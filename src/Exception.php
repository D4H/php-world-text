<?php

namespace WorldText;

use GuzzleHttp\Exception\ClientException;

class Exception extends ClientException
{
    /**
     * @var string
     */
    protected $description;

    /**
     * @var mixed
     */
    protected $error;

    /**
     * @param ClientException $exception
     *
     * @return Exception
     */
    public static function createFromParent(ClientException $exception)
    {
        $object = new self(
            $exception->getMessage(),
            $exception->getRequest(),
            $exception->getResponse(),
            $exception->getPrevious(),
            $exception->getHandlerContext()
        );

        $data = json_decode($exception->getResponse()->getBody()->__toString(), true);
        $object->description = $data['desc'];
        $object->error = $data['error'];

        return $object;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}
