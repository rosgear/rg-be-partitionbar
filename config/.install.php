<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Файл конфигурации установки модуля.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    'use'         => BACKEND,
    'id'          => 'rg.be.partitionbar',
    'name'        => 'Partition Bar',
    'description' => 'Grouped modules by functionality into Partition Bars',
    'namespace'   => 'Rg\Backend\Partitionbar',
    'path'        => '/rg/rg.be.partitionbar',
    'route'       => 'partitionbar',
    'routes'      => [
        [
            'type'    => 'crudSegments',
            'options' => [
                'module'      => 'rg.be.partitionbar',
                'route'       => 'partitionbar',
                'prefix'      => BACKEND,
                'constraints' => ['id'],
                'defaults'    => [
                    'controller' => 'grid'
                ]
            ]
        ]
    ],
    'locales'     => ['ru_RU', 'en_GB'],
    'permissions' => ['any', 'view', 'read', 'add', 'edit', 'delete', 'clear', 'interface', 'settings', 'info'],
    'events'      => [],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'RG Workspace'],
        ['app', 'code' => 'RG CMS'],
        ['app', 'code' => 'RG CRM'],
    ]
];
