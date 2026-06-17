<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Пакет русской локализации.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    '{name}'        => 'Панель разделов',
    '{description}' => 'Сгруппированные компоненты по функционалу в Панель разделов',
    '{permissions}' => [
        'any'       => ['Полный доступ', 'Просмотр и внесение изменений в Панель разделов'],
        'view'      => ['Просмотр', 'Просмотр разделов панели'],
        'read'      => ['Чтение', 'Чтение разделов панели'],
        'add'       => ['Добавление', 'Добавление разделов панели'],
        'edit'      => ['Изменение', 'Изменение разделов панели'],
        'delete'    => ['Удаление', 'Удаление разделов панели'],
        'clear'     => ['Очистка', 'Удаление всех разделов панели'],
        'interface' => ['Интерфейс', 'Доступ к интерфейсу Панели разделов']
    ],

    // Form
    '{form.title}' => 'Создание раздела',
    '{form.titleTpl}' => 'Изменение раздела "{name}"',
    // Form: поля
    'Partition belongs' => 'Находится в',
    'Font glyph' => 'CSS-класс глифа шрифта "Font Awesome"',
    'CSS-class' => 'CSS-класс глифа шрифта "Font Awesome" в заливке тега',
    'FCSS-class' => 'CSS-класс глифа шрифта "Font Awesome" в элементе управления',
    'Code glyph' => 'Числовой код Unicode',
    'Image' => 'Изображение',
    '[ main partition ]' => '[ главный раздел ]',
    'visible' => 'видимый',

    // Grid: панель инструментов
    'Edit record' => 'Редактировать',
    // Grid: контекстное меню записи
    'Filter' => 'Фильтр',
    'Update' => 'Изменить',
    // Grid: Поля
    'Partition' => 'Раздел',
    'Parent partition' => 'Раздел на уровень выше',
    'Index number' => 'Порядковый номер',
    'Index' => 'Порядок',
    'Icon' => 'Значок',
    'Icon / Image' => 'Значок / Изображение',
    'Name' => 'Название',
    'Code' => 'Код',
    'Yes' => 'да',
    'No' => 'нет',
    'Visible' => 'Видимый',
    'Subpartitions' => 'Подразделов',
    'Subpartition count' => 'Количество элементов в разделе',
    'Roles' => 'Роли пользователей',
    'Modules' => 'Модули',
    'Extensions' => 'Расширения',
    'Number of items in a section' => 'Количество элементов в разделе',
    'Show / hide element' => 'Показать / скрыть элемент',
    // Grid: сообщения
    'Partition element {0} - hide' => 'Раздел "<b>{0}</b>" панели - <b>скрыт</b>.',
    'Partition element {0} - show' => 'Раздел "<b>{0}</b>" панели  - <b>отображен</b>.',
    'partition element {0} is hidden' => 'скрытие раздела "<b>{0}</b>" панели',
    'partition element {0} is shown' => 'отображение раздела "<b>{0}</b>" панели',
    'Incorrect partition panel' => 'Неверно выбрана панель раздела',
    'opening a window for viewing partition modules {0}' => 'открытие окна для просмотра модулей раздела "<b>{0}</b>"',
    'viewing partition modules {0}' => 'просмотр модулей раздела "<b>{0}</b>"',
    // Grid: сообщения / заголовки
    'Show' => 'Отобразить',
    'Hide' => 'Скрыть',

    // ModulesGrid: модули раздела
    '{items.title}' => 'Модули раздела "{0}"',
    // ModulesGrid: столбцы
    'Partition modules' => 'Модули раздела',
    'Visibility in the menu section' => 'Видимость в разделе меню',
    // ModulesGrid: сообщения
    'Partition element for module {0} - enabled' => 'Модуль "<b>{0}</b>" для раздела панели - <b>добавлен</b>.',
    'Partition element for module {0} - disabled' => 'Модуль "<b>{0}</b>" для раздела панели - <b>удалён</b>.',
    'the module {0} to partition is added' => 'добавил(а) модуль "<b>{0}</b>" в раздел панели',
    'the module {0} to partition is deleted' => 'удалил(а) модуль "<b>{0}</b>" из раздела панели',
    // ModulesGrid: сообщения / заголовки
    'Module in partition' => 'Модуль раздела',

    // ExtensionGrid: расширения раздела
    '{extensions.title}' => 'Расширения раздела "{0}"',
    // ExtensionGrid: столбцы
    'Partition extensions' => 'Расширения раздела',
    'Module name' => 'Имя модуля',
    'Extension name' => 'Имя расширения',
    // ExtensionGrid: сообщения
    'Partition element for extension {0} - enabled' => 'Расширение "<b>{0}</b>" для раздела панели - <b>добавлено</b>.',
    'Partition element for extension {0} - disabled' => 'Расширение "<b>{0}</b>" для раздела панели - <b>удалёно</b>.',
    'the extension {0} to partition is added' => 'добавил(а) расширение "<b>{0}</b>" в раздел панели',
    'the extension {0} to partition is deleted' => 'удалил(а) расширение "<b>{0}</b>" из раздела панели',
    // ExtensionGrid: сообщения / заголовки
    'Extension in partition' => 'Расширение модуля раздела',

    // RolesGrid: доступ к разделу
    '{roles.title}' => 'Доступ к разделу "{0}" для ролей пользователя',
    // RolesGrid: столбцы
    'Partition roles' => 'Раздел для ролей пользователя',
    'User role availability' => 'Доступность для роли пользователя',
    // RolesGrid: сообщения
    'Partition element for user role {0} - enabled' => 'Для роли пользователя "<b>{0}</b>" раздел панели - <b>доступен</b>.',
    'Partition element for user role {0} - disabled' => 'Для роли пользователя "<b>{0}</b>" раздел панели - <b>не доступен</b>.',
    'partition element for user role {0} is enabled' => '<b>добавил(а)</b> для роли пользователя "<b>{0}</b>" раздел панели',
    'partition element for user role {0} is disabled' => '<b>убрал(а)</b> для роли пользователя "<b>{0}</b>" раздел панели',
    'opening a window for viewing user roles available to the partition {0}' 
        => 'открытие окна для просмотра ролей пользователей доступных разделу "<b>{0}</b>"',
    'viewing user roles available to the partition {0}' => 'просмотр ролей пользователей доступных разделу "<b>{0}</b>"',
    // RolesGrid: сообщения / заголовки
    'Access to the partition' => 'Доступ к разделу',

    // Workspace\Panel: для пунктов меню и заголовков по умолчанию (с символа "#")
    'Configuration' => 'Конфигурация',
    'Users and permissions' => 'Пользователи и права доступа',
    'Workspace' => 'Интерфейс',
    'Debug' => 'Отладка',
    'Tools' => 'Инструменты',
    'Region and languages' => 'Регион и языки',
    'Other settings' => 'Прочие настройки',
    'Logging and errors' => 'Логирование и ошибки',
    'Appearance' => 'Оформление',
    'System' => 'Система',
    'Proactive defense' => 'Проактивная защита',
    'Guide' => 'Справка',
    'Site' => 'Сайт',
    'Marketplace' => 'Маркетплейс'
];
