<?php
/**
 * Ice exception file not found class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Exception;

use Ice\Core\Exception;

/**
 * Class File_Not_Found
 *
 * Implements file not found exception
 *
 * @see Ice\Core\Exception
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Exception
 *
 * @version 0.0
 * @since 0.0
 */
class File_Not_Found extends Exception
{
    /**
     * Constrinctor for file not found exception
     *
     * @param string $errstr
     * @param array $errcontext
     * @param null $previous
     * @param null $errfile
     * @param null $errline
     * @param int $errno
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function __construct($errstr, $errcontext = [], $previous = null, $errfile = null, $errline = null, $errno = 0)
    {
        parent::__construct($errstr, $errcontext, $previous, $errfile, $errline, E_USER_ERROR);
    }
}