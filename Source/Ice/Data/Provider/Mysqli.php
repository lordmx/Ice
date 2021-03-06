<?php
/**
 * Ice data provider implementation mysqli class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use Ice\Core\Data_Provider;
use Ice\Core\Exception;

/**
 * Class Mysqli
 *
 * Data provider for Mysql connection
 *
 * @see Ice\Core\Data_Provider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Data_Provider
 *
 * @version 0.0
 * @since 0.0
 */
class Mysqli extends Data_Provider
{
    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * Get data from data provider by key
     *
     * table:field/value or table
     *
     * @param string  $key
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function get($key = null)
    {
        if (empty($key)) {
            return null;
        }

        $sql = '';

        foreach ((array)$key as $value) {
            if (strpos($value, ':')) {
                list($table, $field) = explode(':', $value);
                list($field, $value) = explode('/', $field);

                $sql .= empty($sql)
                    ? $table . ' WHERE '
                    : ' AND ';

                $sql .= '`' . $field . '`="' . $value . '"';
            } else {
                $sql .= $value;
                break;
            }
        }

        $result = $this->getConnection()->query('SELECT * FROM ' . $sql, MYSQLI_USE_RESULT);

        if ($this->getConnection()->errno) {
            Mysqli::getLogger()->error(['mysql - #' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error], __FILE__, __LINE__);
            return [];
        }

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $result->close();

        return $data;
    }

    /**
     * Get instance connection of data provider
     * @return \Mysqli
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     * @throws Exception
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function set($key, $value, $ttl = null)
    {
        throw new Exception('Implement set() method.');
    }

    /**
     * Delete from data provider by key
     *
     * @param string $key
     * @param bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function dec($key, $step = 1)
    {
        throw new Exception('Implement dec() method.');
    }

    /**
     * Flush all stored data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function flushAll()
    {
    }

    /**
     * Switch to new scheme name
     *
     * @param string $scheme
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function setScheme($scheme)
    {
        if (!$this->getConnection()->select_db($scheme)) {
            Mysqli::getLogger()->fatal(['mysql - #' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error], __FILE__, __LINE__);
        }

        parent::setScheme($scheme);
    }

    /**
     * Connect to data provider
     *
     * @param $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function connect(&$connection)
    {
        $options = $this->getOptions(__CLASS__);

        $connection = mysqli_init();
        $isConnected = $connection->real_connect($options['host'], $options['username'], $options['password'], null, $options['port']);
        if (!$isConnected) {
            Mysqli::getLogger()->fatal(['mysql - #' . $connection->errno . ': {$0}', $connection->error], __FILE__, __LINE__);
        }

        $connection->set_charset($options['charset']);

        return $isConnected;
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function close(&$connection)
    {
        $connection->close();
        $connection = null;
        return true;
    }

    /**
     * Return keys by pattern
     *
     * @param string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getKeys($pattern = null)
    {
        // TODO: Implement getKeys() method.
    }
}