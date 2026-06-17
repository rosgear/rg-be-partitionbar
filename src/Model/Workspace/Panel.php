<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Partitionbar\Model\Workspace;

use Ge;
use Ge\Exception;
use Ge\Helper\Json;
use Ge\Panel\Helper\Ext;
use Ge\Mvc\Module\BaseModule;
use Ge\Data\Model\BaseModel as DataModel;

/**
 * Модель данных элементов панели разделов рабочего пространства пользователя.
 * 
 * Каждый элемент панели разделов может иметь ссылки на модули (таблица {{partitionbar_items}}) или 
 * на плагины (таблица {{gear_plugin}}).
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model\Workspace
 * @since 1.0
 */
class Panel extends DataModel
{
    /**
     * @var BaseModule|\Rg\Backend\Partitionbar\Module
     */
    public BaseModule $module;

    /**
     * Проверяет, должна ли отображаться панель.
     * 
     * @return bool
     */
    public function isVisible(): bool
    {
        static $visible = null;

        if ($visible === null) {
            $workspace = Ge::$app->unifiedConfig->get('workspace');
            if ($workspace)
                $visible = $workspace['partitionbarVisible'] ?? false;
            else
                $visible = true;
            $visible = $this->module->getPermission()->isAllow('any', 'interface') && $visible;
        }
        return $visible;
    }

    /**
     * Возвращает настройки панели разделов.
     * 
     * @param bool $json Если `true`, результат будет представлен в JSON формате (по умолчанию `true`).
     * 
     * @return string|array|null
     */
    public function getSettings(bool $json = true): string|array|null
    {
        if (!$this->isVisible()) {
            return $json ? 'null' : null;
        }
        $items = $this->getItems();
        if (empty($items)) {
            return $json ? 'null' : null;
        }
        $desktop  = Ge::$app->unifiedConfig->get('workspace');
        $position = $desktop['partitionbarPosition'] ?? 'west';
        $settings = [
            'position' => $position,
            'width'    => $position == 'west' || $position == 'east' ? 52 : 0,
            'height'   => $position == 'north' || $position == 'south' ? 52 : 0,
            'items'    => [
                'xtype' => 'toolbar',
                'cls'   => 'g-partitionbar-toolbar',
                'style' => 'padding:0',
                'dock'  => $position == 'north' || $position == 'south' ? 'top' : 'left', 
                'items' => $items
            ]
        ];
        if ($json) {
            $settings = Json::encode($settings, true, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            if ($error = Json::error()) {
                throw new Exception\JsonFormatException($error);
            }
        }
        return $settings;
    }

    /**
     * Возвращает элементы панели разделов c сылками на модули и плагины, доступными 
     * для текущей роли пользователя.
     * 
     * Результат:
     * ```php
     * return [
     *     [
     *        'кнопка раздела 1',
     *        'menu' => [
     *            'items' => [
     *                ['пункт меню 1'],
     *                ['пункт меню 2'],
     *                ...
     *            ]
     *        ]
     *     ],
     *     ...
     * ];
     * ```
     * @return array
     */
    public function getItems(): array
    {
        /** @var \Rg\Backend\Partitionbar\Model\Partitionbar $partitionbar */
        $partitionbar = $this->module->getModel('Partitionbar');

        // все доступные модули сгруппированные по идентификатору элемента панели разделов 
        $modules = Ge::tempGet('pbarModules', function () use ($partitionbar) {
            return $partitionbar->getModules(true, true); 
        });
        // все доступные расширения модулей сгруппированные по идентификаторам элементов панели разделов 
        $extensions = Ge::tempGet('pbarExtensions', function () use ($partitionbar) {
            return $partitionbar->getExtensions(true, true); 
        });

        $partitions = $items = [];
        foreach ($partitionbar->getAll(false) as $item) {
            // идент. ({{panel_partitionbar}}.id) элемента панели разделов
            $id = $item['id'];
            // идент. родителя ({{panel_partitionbar}}.parent_id) элемента панели разделов
            $parentId = $item['parentId']; 
            // меню кнопки
            $menuItems = [];
            // если подпункт меню имеет ссылки на модули
            if (!empty($modules[$id])) {
                // добавляем подпункты
                foreach ($modules[$id] as $module) {
                    $menuItems[] = [
                        'text'        => $module['name'],
                        'description' => $module['description'],
                        'icon'        => $module['smallIcon'],
                        'handler'     => 'loadWidget',
                        'handlerArgs' => ['route' => '@backend/'. $module['route']]
                    ];
                }
            }
            // если подпункт меню имеет ссылки на модули
            if (!empty($extensions[$id])) {
                // добавляем подпункты
                foreach ($extensions[$id] as $extension) {
                    $menuItems[] = [
                        'text'        => $extension['name'],
                        'description' => $extension['description'],
                        'icon'        => $extension['smallIcon'],
                        'handler'     => 'loadWidget',
                        'handlerArgs' => ['route' => '@backend/'. $extension['baseRoute']]
                    ];
                }
            }
            // если подпункт меню, т.к. указан родитель
            if ($item['parentId'] > 0) {
                $partitionItem = [
                    'text' => $this->module->tH($item['name']),
                ];
                if ($menuItems) {
                    $partitionItem['menu'] = [
                        'mouseLeaveDelay' => 0,
                        'title'      => $this->module->tH($item['name']),
                        'titleAlign' => 'center',
                        'items'      => $menuItems
                    ];
                } else
                    continue;
                Ext::buttonIcon($partitionItem, $item['iconType'], $item['icon']);

                if (!isset($items[$parentId])) {
                    $items[$parentId] = [];
                }
                $items[$parentId][] = $partitionItem;
            // если пункт меню
            } else {
                $partition = [
                    'cls'        => 'g-partitionbar-btn',
                    'arrowAlign' => 'none',
                    'tooltip'    => $this->module->tH($item['name']),
                    'menuAlign'  => 'tl-tr?',
                    'margin'     => 0,
                    'width'      => 45,
                    'height'     => 54
                ];
                if ($menuItems) {
                    $partition['menu'] = [
                        'mouseLeaveDelay' => 0,
                        'title'      => $this->module->tH($item['name']),
                        'titleAlign' => 'center',
                        'items'      => $menuItems
                    ];
                }
                Ext::buttonIcon($partition, $item['iconType'], $item['icon']);
                $partitions[$id] = $partition;
            }
        }

        // обход всех разделов и добавление пунктов меню
        $result = [];
        foreach($partitions as $id => $partition) {
            if (isset($items[$id])) {
                if (isset($partition['menu']['items'])) {
                    $partition['menu']['items'] = array_merge($items[$id], $partition['menu']['items']);
                } else {
                    $partition['menu'] = [
                        'mouseLeaveDelay' => 0,
                        'title'      => $this->module->tH($partition['tooltip']),
                        'titleAlign' => 'center',
                        'items'      => $items[$id]
                    ];
                }
            }
            // добавлять кнопку раздела в том случаи если в ёё меню есть пункты
            if (!empty($partition['menu']))
                $result[] = $partition;
        }
        return $result;
    }
}
