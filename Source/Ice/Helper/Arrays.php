<?php
/**
 * Ice helper arrays class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;

/**
 * Class Arrays
 *
 * Helper for arrays
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
 */
class Arrays
{
    /**
     * Filter array by filter scheme
     *
     *  $filterScheme = [
     *      ['name', 'Petya', '='],
     *      ['age', 18, '>'],
     *      ['surname', 'Iv%', 'like']
     *  ];
     *
     * @param array $rows
     * @param $filterScheme
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function filter(array $rows, $filterScheme)
    {
        $filterFunction = function ($filterSchemes) {
            return function ($row) use ($filterSchemes) {
                foreach ($filterSchemes as $filterScheme) {
                    list($field, $value, $comparsion) = $filterScheme;
                    $field = trim($field);
                    $value = trim($value);
                    switch ($comparsion) {
                        case '<=':
                            if ($row[$field] > $value) {
                                return false;
                            }
                            break;
                        case '>=':
                            if ($row[$field] < $value) {
                                return false;
                            }
                            break;
                        case '<>':
                            if ($row[$field] == $value) {
                                return false;
                            }
                            break;
                        case '=':
                            if ($row[$field] != $value) {
                                return false;
                            }
                            break;
                        case '<':
                            if ($row[$field] >= $value) {
                                return false;
                            }
                            break;
                        case '>':
                            if ($row[$field] <= $value) {
                                return false;
                            }
                            break;
                        default:
                            throw new Exception('Unknown comparsion operator');
                    };
                }
                return true;
            };
        };

        return array_filter($rows, $filterFunction((array)$filterScheme));
    }

    /**
     * Group array by known column
     *
     * @param $array
     * @param $column
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function group($array, $column)
    {
        $group = [];

        foreach ($array as $key => $item) {
            $index = $item[$column];
            unset($item[$column]);

            $group[$index][$key] = $item;
        }

        return $group;
    }

    /**
     * This file is part of the array_column library
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     *
     * @copyright Copyright (c) 2013 Ben Ramsey <http://benramsey.com>
     * @license http://opensource.org/licenses/MIT MIT
     *
     *
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     * a column of values.
     * @param mixed $columnKey The column(s) of values to return. This value may be
     * null, 0, array or any string
     * may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     * the returned array. This value may be null, '', or any string
     * of the column, or it may be the string key name.
     * @return array
     */
    public static function column($input, $columnKey = null, $indexKey = null)
    {
        if (!is_array($input)) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($input) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($columnKey)
            && !is_float($columnKey)
            && !is_string($columnKey)
            && !is_array($columnKey)
            && $columnKey !== null
            && !(is_object($columnKey) && method_exists($columnKey, '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($indexKey)
            && !is_int($indexKey)
            && !is_float($indexKey)
            && !is_string($indexKey)
            && !(is_object($indexKey) && method_exists($indexKey, '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $indexKey = (int)$params[2];
            } else {
                $indexKey = (string)$params[2];
            }
        }

        $resultArray = array();

        foreach ($input as $key => $row) {
            $value = null;
            $valueSet = false;

            if ($indexKey !== null && array_key_exists($indexKey, $row)) {
                $key = (string)$row[$indexKey];
            } else if ($indexKey === '') {
                $key = '';
            }

            if ($columnKey === null) {
                $valueSet = true;
                $value = $row;
            } else if ($columnKey === 0) {
                $valueSet = true;
                $value = reset($row);
            } else {
                if (!is_array($row)) {
                    continue;
                }

                if (is_array($columnKey)) {
                    $values = array_intersect_key($row, array_flip($columnKey));
                    if (!empty($values)) {
                        $valueSet = true;
                        $value = $values;
                    }
                } else {
                    if (array_key_exists($columnKey, $row)) {
                        $valueSet = true;
                        $value = $row[$columnKey];
                    }
                }
            }

            if ($valueSet) {
                if ($key === '') {
                    $resultArray[] = $value;
                } else {
                    $resultArray[$key] = $value;
                }

            }
        }

        return $resultArray;
    }

    /**
     * Ice array diff
     *
     * Return array of added, deleted and other rows
     *
     * @param $old
     * @param $new
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function diff($old, $new)
    {
        $diff = [
            'added' => array_diff_key($new, $old),
            'deleted' => array_diff_key($old, $new)
        ];

        $diff['other'] = array_diff_key($new, $diff['added']);

        return $diff;
    }

    public static function defaults($data, array $defaults = array())
    {
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = is_callable($value) ? $value($key) : $value;
            }
        }

        return $data;
    }

    public static function validate($data, array $validators = array())
    {

        return false;
    }

    public static function convert($data, array $converters = array())
    {
        foreach ($converters as $key => $value) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = null;
            }

            $data[$key] = $value($data[$key]);
        }

        return $data;
    }
}