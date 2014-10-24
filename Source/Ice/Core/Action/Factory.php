<?php
/**
 * Ice core action factory interface
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core\Action;

/**
 * Interface Factory
 *
 * Core action factory interface
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version stable_0
 * @since stable_0
 */
interface Factory
{
    /**
     * Get delegate by name
     *
     * @param $delegateName
     * @return mixed
     */
    public static function getDelegate($delegateName);
}