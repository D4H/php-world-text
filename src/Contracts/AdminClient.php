<?php

namespace WorldText\Contracts;

use GuzzleHttp\Exception\BadResponseException;

interface AdminClient
{
    /**
     * @return bool
     */
    public function ping();

    /**
     * @return int
     */
    public function credits();
}
