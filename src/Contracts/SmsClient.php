<?php

namespace WorldText\Contracts;

interface SmsClient
{
    /**
     * @param bool $enable
     *
     * @return self
     */
    public function simulate($enable = true);

    /**
     * @param string $messageId
     *
     * @return array
     */
    public function query($messageId);

    /**
     * @param string $number
     * @param string $message
     * @param array $options
     *
     * @return array
     */
    public function send($number, $message, array $options = []);

    /**
     * @param int $groupId
     * @param string $message
     * @param array $options
     *
     * @return array
     */
    public function sendGroup($groupId, $message, array $options = []);

    /**
     * @param string $number
     *
     * @return int
     */
    public function cost($number);

    /**
     * @param int $groupId
     *
     * @return int
     */
    public function groupCost($groupId);
}
