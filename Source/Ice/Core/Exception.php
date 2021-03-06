<?php
/**
 * Ice core exception class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use ErrorException;
use Ice\Core;

/**
 * Class Exception
 *
 * Ice application exception
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
class Exception extends ErrorException
{
    use Core;

    /**
     * Error context data
     * @var array
     */
    private $errcontext = null;

    /**
     * Constructor exception of ice application
     *
     * Simple constructor for fast throws Exception
     *
     * @param string $message
     * @param array $errcontext context data of exception
     * @param \Exception $previous previous exception if exists
     * @param string $errfile filename where throw Exception
     * @param int $errline number of line where throws Exception
     * @param int $errno code of error exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function __construct($message, $errcontext = [], \Exception $previous = null, $errfile = null, $errline = null, $errno = 0)
    {
        $this->errcontext = $errcontext;

        $isExistsResourceClass = class_exists('Ice\Core\Resource', false);

        if (($errno == 0 || substr(Logger::$errorCodes[$errno], 0, 7) == 'E_USER_' || Logger::$errorCodes[$errno] == 'FATAL') && $isExistsResourceClass) {
            $params = null;
            $class = null;
            if (is_array($message)) {
                switch (count($message)) {
                    case 2:
                        list($message, $params) = $message;
                        break;
                    case 3:
                        list($message, $params, $class) = $message;
                        break;
                    default:
                        break;
                }
            }

            $message = self::getResource()->get($message, $params, $class);
        } else {
            if (is_array($message)) {
                if (!$isExistsResourceClass && !empty($message[1])) {
                    $message[0] = str_replace(
                        array_map(
                            function ($var) {
                                return '{$' . $var . '}';
                            }, array_keys((array)$message[1])
                        ), array_values((array)$message[1]),
                        $message[0]);
                }

                $message = reset($message);
            }
        }

        if (!$errfile) {
            $debug = debug_backtrace();

            if (!empty($debug)) {
                /** @var Exception $exception */
                $exception = reset($debug)['object'];
                $errfile = $exception->getFile();
                $errline = $exception->getLine();
            }
        }

        parent::__construct($message, $errno, 1, $errfile, $errline, $previous);
    }

    /**
     * Return error context data
     *
     * Data in moment throws exception
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getErrContext()
    {
        return $this->errcontext;
    }
}