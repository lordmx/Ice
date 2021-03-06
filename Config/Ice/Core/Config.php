<?php
/**
 * @file Ice main config
 *
 * Sets default config params for ice application components
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.0
 * @since 0.0
 */

return [
    'Ice\Core\Model' => [
        'prefixes' => [
            'ice' => 'Ice',
        ]
    ],
    'Ice\Core\Environment' => [
        'environments' => [
            '/localhost/' => 'development',
            '/.*/' => 'production'
        ]
    ],
    'Ice\View\Render\Cli' => [],
    'Ice\View\Render\Json' => [],
    'Ice\View\Render\Php' => [],
    'Ice\View\Render\Replace' => [],
    'Ice\View\Render\Smarty' => [
        'dataProviderKey' => 'Ice:Registry/view_render',
        'vendor' => 'smarty/smarty',
        'templates_c' => 'smarty/templates_c/',
        'plugins' => [ICE_DIR . 'Vendor/Smarty/plugins']
    ],
    'Ice\View\Render\Twig' => [
        'vendor' => 'twig/twig',
        'cache' => 'twig/cache/',
    ],
    'Ice\Core\View' => [
        'layout' => null,
        'defaultViewRenderClassName' => 'Ice:Php'
    ],
    'Ice\Core\Action' => [
        'layoutActionName' => 'Ice:Layout_Main',
    ],
    'Ice\Core\Request' => [
        'locale' => 'en',
        'multilocale' => true
    ],
];