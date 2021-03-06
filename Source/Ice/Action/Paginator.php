<?php
/**
 * Ice action paginator class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Data;

/**
 * Class Paginator
 *
 * Default ice paginator
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since 0.0
 */
class Paginator extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standart|file
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'viewRenderClassName' => 'Ice:Php',
        'inputDefaults' => [
            'fastStep' => 5,
            'params' => []
        ],
        'inputValidators' => [
            'data' => 'Ice:Is_Data',
            'actionName' => 'Ice:Not_Null'
        ],
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        /** @var Data $data */
        $data = $input['data'];

        $page = $data->getPage();

        $output = [];

        if ($page > 1) {
            $output['first'] = 1;
        }

        if ($page - $input['fastStep'] >= 1) {
            $output['fastPrev'] = $page - $input['fastStep'];
        }

        if ($page - 2 >= 1) {
            $output['before2'] = $page - 2;
        }

        if ($page - 1 >= 1) {
            $output['prev'] = $page - 1;
            $output['before1'] = $page - 1;
        }

        $output['curr'] = $page;

        $output['foundRows'] = $data->getFoundRows();
        $output['limit'] = $data->getLimit();

        $pageCount = intval($output['foundRows'] / $output['limit']) + 1;

        if ($page == $pageCount) {
            $output['limit'] = $output['foundRows'] - ($pageCount - 1) * $output['limit'];
        }

        if ($page + 1 <= $pageCount) {
            $output['next'] = $page + 1;
            $output['after1'] = $page + 1;
        }

        if ($page + 2 <= $pageCount) {
            $output['after2'] = $page + 2;
        }

        if ($page + $input['fastStep'] <= $pageCount) {
            $output['fastNext'] = $page + $input['fastStep'];
        }

        if ($page < $pageCount) {
            $output['last'] = $pageCount;
        }

        $output['actionName'] = $input['actionName'];

        array_walk(
            $input['params'], function (&$item, $key) {
                $item = $key . ': ' . $item;
            }
        );

        $output['params'] = '{' . implode(', ', $input['params']) . '}';

        return $output;
    }
}