<?php

namespace WorldText\Contracts;

interface GroupClient
{
    /**
     * Returns the new group id.
     *
     * @param string $name
     * @param string $sourceAddress
     * @param string $pin
     *
     * @return string
     */
    public function make($name, $sourceAddress, $pin = '0000');

    /**
     * @param string $id
     *
     * @return bool
     */
    public function destroy($id);

    /**
     * @param string $id
     *
     * @return array
     */
    public function details($id);

    /**
     * Returns number of entries removed
     *
     * @param string $id
     *
     * @return int
     */
    public function clear($id);

    /**
     * @param string $id
     * @param string $number
     *
     * @return bool
     */
    public function remove($id, $number);

    /**
     * @param string $id
     * @param array $list
     *
     * @return array
     */
    public function addMany($id, array $list);

    /**
     * @param string $id
     * @param string $name
     * @param string $number
     *
     * @return bool
     */
    public function add($id, $name, $number);

    /**
     * @return array
     */
    public function all();

    /**
     * @param string $id
     *
     * @return array
     */
    public function numbers($id);

    /**
     * Returns group id or null.
     *
     * @param string $name
     *
     * @return int|null
     */
    public function find($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function exists($name);
}
