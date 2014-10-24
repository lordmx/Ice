<?php
/**
 * Ice action form submit class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Logger;

/**
 * Class Form_Submit
 *
 * Action submit model form
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Action
 *
 * @version stable_0
 * @since stable_0
 */
class Form_Submit extends Action
{
    /**  public static $config = [
     *      'staticActions' => [],          // actions
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
        'template' => '',
        'inputValidators' => [
            'formClass' => 'Ice:Not_Empty',
            'formKey' => 'Ice:Not_Empty'
        ],
        'inputDefaults' => [
            'filterFields' => ''
        ]
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        /** @var Form $formClass */
        $formClass = $input['formClass'];
        unset($input['formClass']);

        $formKey = $input['formKey'];
        unset($input['formKey']);

        try {
            $formClass::getInstance($formKey)
                ->addFilterFields(explode(',', $input['filterFields']))
                ->bind($input)
                ->submit();

            return [
                'success' => Form_Submit::getLogger()->info('Row applied successfully', Logger::SUCCESS)
            ];
        } catch (\Exception $e) {
            $message = ['Row apply failed: {$0}', $e->getMessage()];

            Form_Submit::getLogger()->error($message, __FILE__, __LINE__, $e);

            return [
                'error' => Form_Submit::getLogger()->info($message, Logger::DANGER)
            ];
        }
    }
}