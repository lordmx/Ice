<?php
/**
 * Ice helper memory class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Memory
 *
 * Helper memory usage
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
 */
class Memory
{
    /**
     * Return memory size of variable
     *
     * @param mixed $var Variable
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getVarSize($var)
    {
        $start_memory = memory_get_usage();
        $tmp = unserialize(serialize($var));
        return memory_get_usage() - $start_memory;
    }

    /**
     * Return info about current memory usage and peak memory usage
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function memoryGetUsagePeak()
    {
        $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

        $size = memory_get_usage(true);
        $memoryUsage = @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];

        $size = memory_get_peak_usage(true);
        $peakUsage = @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
        return 'memory_usage: ' . $memoryUsage . ' (peak: ' . $peakUsage . ')';
    }
} 