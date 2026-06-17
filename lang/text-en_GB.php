<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Пакет английской (британской) локализации.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    '{name}'        => 'Partition Bar',
    '{description}' => 'Grouped components by functionality into Partition Bars',
    '{permissions}' => [
        'any'       => ['Full access', 'View and make changes to the Partition bar'],
        'view'      => ['View', 'Viewing Partition Bars'],
        'read'      => ['Reading', 'Reading Partition Bars'],
        'add'       => ['Adding', 'Adding Partition Bars'],
        'edit'      => ['Editing', 'Editing Partition Bars'],
        'delete'    => ['Deleting', 'Deleting Partition Bars'],
        'clear'     => ['Clear', 'Deleting all Partition Bars'],
        'interface' => ['Interface', 'Accessing the Partition Bars interface']
    ],

    // Form
    '{form.title}' => 'Partition creation',
    '{form.titleTpl}' => 'Update partition "{name}"',
    // Form: поля
    'Partition belongs' => 'Partition belongs',
    'Font glyph' => 'CSS font glyph "Font Awesome"',
    'CSS-class' => 'CSS glyph "Font Awesome" in tag fill',
    'FCSS-class' => 'CSS glyph "Font Awesome"in the control',
    'Code glyph' => 'Unicode',
    'Image' => 'Image',
    '[ main partition ]' => '[ main partition ]',
    'visible' => 'visible',

    // Grid: панель инструментов
    'Edit record' => 'Edit record',
    'Filter' => 'Filter',
    'Update' => 'Update',
    // Grid: Поля
    'Partition' => 'Partition',
    'Parent partition' => 'Parent partition',
    'Index number' => 'Index number',
    'Index' => 'Index',
    'Icon' => 'Icon',
    'Icon / Image' => 'Icon / Image',
    'Name' => 'Name',
    'Code' => 'Code',
    'Yes' => 'yes',
    'No' => 'non',
    'Visible' => 'Visible',
    'Subpartitions' => 'Subpartitions',
    'Subpartition count' => 'Subpartition count',
    'Roles' => 'Roles',
    'Modules' => 'Modules',
    'Extensions' => 'Extensions',
    'Number of items in a section' => 'Number of items in a section',
    'Show / hide element' => 'Show / hide element',
    // Grid: сообщения
    'Partition element {0} - hide' => 'Partition element "<b>{0}</b>" - <b>hide</b>.',
    'Partition element {0} - show' => 'Partition element "<b>{0}</b>" - <b>show</b>.',
    'partition element {0} is hidden' => 'partition element "<b>{0}</b>"  is hidden',
    'partition element {0} is shown' => 'partition element "<b>{0}</b>" is shown',
    'Incorrect partition panel' => 'Incorrect partition panel',
    'opening a window for viewing partition modules {0}' => 'opening a window for viewing partition modules "<b>{0}</b>"',
    'viewing partition modules {0}' => 'viewing partition modules "<b>{0}</b>"',
    // Grid: сообщения / заголовки
    'Show' => 'Show',
    'Hide' => 'Hide',

    // ModulesGrid: модули раздела
    '{items.title}' => 'Partition modules "{0}"',
    // ModulesGrid: столбцы
    'Partition modules' => 'Partition modules',
    'Visibility in the menu section' => 'Visibility in the menu section',
    // ModulesGrid: сообщения
    'Partition element for module {0} - enabled' => 'Partition element for module "<b>{0}</b>" - <b>enabled</b>.',
    'Partition element for module {0} - disabled' => 'Partition element for module "<b>{0}</b>" - <b>disabled</b>.',
    'the module {0} to partition is added' => 'the module "<b>{0}</b>" to partition is added',
    'the module {0} to partition is deleted' => 'the module "<b>{0}</b>" to partition is deleted',
    // ModulesGrid: сообщения / заголовки
    'Module in partition' => 'Module in partition',

    // ExtensionGrid: расширения раздела
    '{extensions.title}' => 'Partition extension "{0}"',
    // ExtensionGrid: столбцы
    'Partition extensions' => 'Partition extensions',
    'Module name' => 'Module name',
    'Extension name' => 'Extension name',
    // ExtensionGrid: сообщения
    'Partition element for extension {0} - enabled' => 'Partition element for extension "<b>{0}</b>" - <b>enabled</b>.',
    'Partition element for extension {0} - disabled' => 'Partition element for extension "<b>{0}</b>" - <b>disabled</b>.',
    'the extension {0} to partition is added' => 'the extension "<b>{0}</b>" to partition is added',
    'the extension {0} to partition is deleted' => 'the extension "<b>{0}</b>" to partition is deleted',
    // ExtensionGrid: сообщения / заголовки
    'Extension in partition' => 'Extension in partition',

    // RolesGrid: доступ к разделу
    '{roles.title}' => 'Access to partition "{0}" for user roles',
    // RolesGrid: столбцы
    'Partition roles' => 'Partition roles',
    'User role availability' => 'User role availability',
    // RolesGrid: сообщения
    'Partition element for user role {0} - enabled' => 'Partition element for user role "<b>{0}</b>" - <b>enabled</b>.',
    'Partition element for user role {0} - disabled' => 'Partition element for user role "<b>{0}</b>" - <b>disabled</b>.',
    'partition element for user role {0} is enabled' => 'partition element for user role "<b>{0}</b>" is enabled',
    'partition element for user role {0} is disabled' => 'partition element for user role "<b>{0}</b>" is disabled',
    'opening a window for viewing user roles available to the partition {0}' 
        => 'opening a window for viewing user roles available to the partition "<b>{0}</b>"',
    'viewing user roles available to the partition {0}' => 'viewing user roles available to the partition "<b>{0}</b>"',
    // RolesGrid: сообщения / заголовки
    'Access to the partition' => 'Access to the partition',

    // Workspace\Panel: для пунктов меню и заголовков по умолчанию (с символа "#")
    'Configuration' => 'Configuration',
    'Users and permissions' => 'Users and permissions',
    'Workspace' => 'Workspace',
    'Debug' => 'Debug',
    'Tools' => 'Tools',
    'Region and languages' => 'Region and languages',
    'Other settings' => 'Other settings',
    'Logging and errors' => 'Logging and errors',
    'Appearance' => 'Appearance',
    'System' => 'System',
    'Proactive defense' => 'Proactive defense',
    'Guide' => 'Guide',
    'Site' => 'Site',
    'Marketplace' => 'Marketplace'
];
