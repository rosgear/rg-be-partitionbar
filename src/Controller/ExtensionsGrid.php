<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Partitionbar\Controller;

use Ge;
use Ge\Panel\Helper\ExtGrid;
use Ge\Panel\Widget\GridDialog;
use Ge\Panel\Data\Model\FormModel;
use Ge\Panel\Controller\DialogGridController;

/**
 * Контроллер списка расширений модулей панели раздела.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Controller
 * @since 1.0
 */
class ExtensionsGrid extends DialogGridController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'ExtensionsGrid';

    /**
     * Идентификатора выбранного раздела.
     * 
     * @var int
     */
    protected int $identifier;

    /**
     * {@inheritdoc}
     */
    public function translateAction(mixed $params, string $default = null): ?string
    {
        switch ($this->actionName) {
            // вывод интерфейса
            case 'view':
                $model = $this->getModel($this->defaultModel);
                return $this->module->t('opening a window for viewing partition modules {0}', [$model->partition['name']]);

            // вывод записей
            case 'data':
                $model = $this->getModel($this->defaultModel);
                return $this->module->t('viewing partition modules {0}', [$model->partition['name']]);

            // изменение записи по указанному идентификатору
            case 'update':
                /** @var FormModel $model */
                $model = $this->lastDataModel;
                if ($model instanceof FormModel) {
                    // если выбранный модуль входит в раздел
                    $available = (int) $model->available;
                    return $this->module->t(
                        'the module {0} to partition is ' . ($available > 0 ? 'added' : 'deleted'), [$model->name]
                    );
                }

            default:
                return parent::translateAction($params, $default);
        }
    }

    /**
     * Возвращает идентификатор выбранного раздела.
     * 
     * @return int
     */
    public function getPartitionIdentifier(): int
    {
        if (!isset($this->identifier)) {
            $this->identifier = (int) Ge::$app->router->get('id');
        }
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): GridDialog
    {
        /** @var \Rg\Backend\Partitionbar\Model\Partitionbar $partitionbar */
        $partitionbar = $this->module->getModel('Partitionbar');
        // информация о выбранном разделе
        $partition = $partitionbar->get($this->getPartitionIdentifier());
        if ($partition === null)
            return $this->getResponse()->error($this->t('Incorrect partition panel'));

        // информацию в хранилище модуля
        $store = $this->module->getStorage();
        $store->partition = $partition->getAttributes();

        /** @var GridDialog $window Окно с Сеткей данных (Ge.view.grid.Grid GeJS) */
        $window = parent::createWidget();

        $title = $partition->name ?? '';
        if (strncmp($title, '#', 1) === 0) {
            $title = $this->module->t(ltrim($title, '#')) . ' (' . $partition->name . ')';
        }

        // виджет окна (Ext.window.Window Sencha ExtJS)
        $window->width = 700;
        $window->height = '90%';
        $window->ui     = 'light';
        $window->layout = 'fit';
        $window->resizable = true;
        $window->iconCls = 'g-icon-svg g-icon_extension_small';
        $window->title = $this->module->t('{extensions.title}', [$title]);

        // столбцы (Ge.view.grid.Grid.columns GeJS)
        $window->grid->columns = [
            ExtGrid::columnNumberer(),
            [
                'text'      => '#Module name',
                'dataIndex' => 'module',
                'filter'    => ['type' => 'string'],
                'cellTip'   => '{module}',
                'width'     => 200,
                'hideable'  => false
            ],
            [
                'text'      => '#Extension name',
                'xtype'     => 'templatecolumn',
                'dataIndex' => 'name',
                'filter'    => ['type' => 'string'],
                'tpl'       => '<img src="{icon}" align="absmiddle"> {name}',
                'cellTip'   => '{name}',
                'width'     => 350,
                'hideable'  => false
            ],
            [
                'text'        => ExtGrid::columnIcon('g-icon-m_visible', 'svg'),
                'tooltip'     => '#Visibility in the menu section',
                'xtype'       => 'g-gridcolumn-switch',
                'sortable'    => false,
                'collectData' => ['name'],
                'dataIndex'   => 'available',
                'filter'    => ['type' => 'boolean'],
                'hideable'    => false
            ]
        ];

        // сортировка строк в сетке
        $window->grid->sorters = [
            ['property' => 'name', 'direction' => 'ASC']
        ];
        // количество строк в сетке
        $window->grid->store->pageSize = 1000;
        $window->grid->router->route = Ge::alias('@match', '/extensions');
        // локальная фильтрация и сортировка
        $window->grid->store->remoteFilter = false;
        $window->grid->store->remoteSort = false;
        // плагины сетки
        $window->grid->plugins = 'gridfilters';
        // убираем пагинацию страниц
        unset($window->grid->pagingtoolbar);
        return $window;
    }
}
