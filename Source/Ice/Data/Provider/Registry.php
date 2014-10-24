<?php
/**
 * Ice data provider implementation registry class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use ArrayObject;
use Ice\Core\Data_Provider;
use Ice\Core\Exception;
use Ice\Core\Registry as Core_Registry;

/**
 * Class Registry
 *
 * Data provider for registry data
 *
 * @see Ice\Core\Data_Provider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Data_Provider
 *
 * @version stable_0
 * @since stable_0
 */
class Registry extends Data_Provider
{
    /**
     * Return default data provider key
     *
     * @return string
     */
    protected static function getDefaultKey()
    {
        return Core_Registry::DEFAULT_DATA_PROVIDER_KEY;
    }

    /**
     * Get data from data provider by key
     *
     * @param string  $key
     * @return mixed
     */
    public function get($key = null)
    {
        $keyPrefix = $this->getKeyPrefix();

        if (!isset($this->getConnection()->$keyPrefix)) {
            return null;
        }

        $data = $this->getConnection()->$keyPrefix;

        if (empty($key)) {
            return $data;
        }

        return isset($data[$key]) ? $data[$key] : null;
    }

    /**
     * Return registry connection
     *
     * @return ArrayObject
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Set data to data provider
     *
     * @param string $key
     * @param $value
     * @param null $ttl
     * @return mixed setted value
     */
    public function set($key, $value, $ttl = null)
    {
        if ($ttl == -1) {
            return $value;
        }

        if (is_array($key)) {
            foreach ($key as $k => $item) {
                $this->set($key, $item, $ttl);
            }

            return $value;
        }

        $keyPrefix = $this->getKeyPrefix();

        if (!isset($this->getConnection()->$keyPrefix)) {
            $this->getConnection()->$keyPrefix = [$key => $value];
            return $value;
        }

        $data = $this->getConnection()->$keyPrefix;
        $data[$key] = $value;
        $this->getConnection()->$keyPrefix = $data;

        return $value;
    }

    /**
     * Delete from data provider by key
     *
     * @param string $key
     * @param bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     */
    public function delete($key, $force = true)
    {
        throw new Exception('Implement delete() method.');
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @throws Exception
     * @return mixed new value
     */
    public function inc($key, $step = 1)
    {
        throw new Exception('Implement inc() method.');
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @throws Exception
     * @return mixed new value
     */
    public function dec($key, $step = 1)
    {
        throw new Exception('Implement dec() method.');
    }

    /**
     * Flush all stored data
     */
    public function flushAll()
    {
        $keyPrefix = $this->getKeyPrefix();

        File::getLogger()->info(['Trying remove {$0}', $keyPrefix]);

        if (isset($this->getConnection()->$keyPrefix)) {
            unset($this->getConnection()->$keyPrefix);
        }

    }

    /**
     * Connect to data provider
     *
     * @param $connection
     * @return boolean
     */
    protected function connect(&$connection)
    {
        $connection = new ArrayObject();
        return true;
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     * @return boolean
     */
    protected function close(&$connection)
    {
        $connection = null;
        return true;
    }

    /**
     * Return keys by pattern
     *
     * @param string $pattern
     * @return array
     */
    public function getKeys($pattern = null)
    {
        // TODO: Implement getKeys() method.
    }
}