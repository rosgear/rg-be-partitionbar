<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Файл конфигурации Карты SQL-запросов.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    'drop'   => [
        '{{panel_partitionbar}}', 
        '{{panel_partitionbar_extensions}}', 
        '{{panel_partitionbar_modules}}', 
        '{{panel_partitionbar_roles}}'
    ],

    'create' => [
        '{{panel_partitionbar}}' => function () {
            return "CREATE TABLE `{{panel_partitionbar}}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `parent_id` int(11) unsigned DEFAULT NULL,
                `count` int(11) unsigned DEFAULT NULL,
                `index` int(11) unsigned DEFAULT '1',
                `code` varchar(100) DEFAULT NULL,
                `icon` varchar(255) DEFAULT NULL,
                `icon_type` varchar(15) DEFAULT NULL,
                `name` varchar(255) DEFAULT NULL,
                `visible` tinyint(1) unsigned DEFAULT '1',
                PRIMARY KEY (`id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        },

        '{{panel_partitionbar_extensions}}' => function () {
            return "CREATE TABLE `{{panel_partitionbar_extensions}}` (
                `partition_id` int(11) unsigned NOT NULL,
                `extension_id` int(11) unsigned NOT NULL,
                PRIMARY KEY (`partition_id`,`extension_id`),
                KEY `module` (`extension_id`),
                KEY `partition_and_module` (`partition_id`,`extension_id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        },

        '{{panel_partitionbar_modules}}' => function () {
            return "CREATE TABLE `{{panel_partitionbar_modules}}` (
                `partition_id` int(11) unsigned NOT NULL,
                `module_id` int(11) unsigned NOT NULL,
                PRIMARY KEY (`partition_id`,`module_id`),
                KEY `module` (`module_id`),
                KEY `partition_and_module` (`partition_id`,`module_id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        },

        '{{panel_partitionbar_roles}}' => function () {
            return "CREATE TABLE `{{panel_partitionbar_roles}}` (
                `partition_id` int(11) unsigned NOT NULL,
                `role_id` int(11) unsigned NOT NULL,
                PRIMARY KEY (`partition_id`,`role_id`),
                KEY `partition_and_role` (`partition_id`,`role_id`),
                KEY `role` (`role_id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        }
    ],

    'run' => [
        'install'   => ['drop', 'create'],
        'uninstall' => ['drop']
    ]
];