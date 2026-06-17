<?php
/**
 * Модуль веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Partitionbar;

/**
 * Модуль Панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar
 * @since 1.0
 */
class Module extends \Ge\Panel\Module\Module
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'rg.be.partitionbar';

    /**
     * {@inheritdoc}
     */
    public function controllerMap(): array
    {
        return [
            'roles'      => 'RolesGrid', // роли пользователей раздела
            'modules'    => 'ModulesGrid', // модули раздела
            'extensions' => 'ExtensionsGrid', // расширения раздела
        ];
    }
}
