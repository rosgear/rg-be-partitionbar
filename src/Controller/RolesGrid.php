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
 * Контроллер списка панели раздела.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Controller
 * @since 1.0
 */
class RolesGrid extends DialogGridController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'RolesGrid';

    /**
     * Идентификатора выбранного раздела.
     * 
     * @see RolesGrid::getPartitionIdentifier()
     * 
     * @var int
     */
    protected int $identifier;

    /**
     * {@inheritdoc}
     */
    public function translateAction(mixed $params, ?string $default = null): ?string
    {
        switch ($this->actionName) {
            // вывод интерфейса
            case 'view':
                $model = $this->getModel($this->defaultModel);
                return $this->module->t(
                    'opening a window for viewing user roles available to the partition {0}', [$model->partition['name']]
                );

            // вывод записей
            case 'data':
                $model = $this->getModel($this->defaultModel);
                return $this->module->t(
                    'viewing user roles available to the partition {0}', [$model->partition['name']]
                );

            // изменение записи по указанному идентификатору
            case 'update':
                /** @var FormModel $model */
                $model = $this->lastDataModel;
                if ($model instanceof FormModel) {
                    if ($model->available !== null) {
                        // если раздел доступен для выбранной роли пользователя
                        $available = (int) $model->available;
                        return $this->module->t(
                            'partition element for user role {0} is ' . ($available > 0 ? 'enabled' : 'disabled'), [$model->name]
                        );
                    }
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
        if ($partition === null) {
            $this->getResponse()
                ->meta->error(Ge::t(BACKEND, 'Invalid argument "{0}"', ['ID']));
            return parent::createWidget();
        }

        // информацию в хранилище модуля
        $store = $this->module->getStorage();
        $store->partition = $partition->getAttributes();

        /** @var GridDialog $window Окно с Сеткой данных (Ge.view.grid.Grid GeJS) */
        $window = parent::createWidget();

        // видже окна (Ext.window.Window Sencha ExtJS)
        $window->width = 500;
        $window->height = '90%';
        $window->ui = 'light';
        $window->layout = 'fit';
        $window->resizable = true;
        $window->iconCls = 'g-icon-svg g-icon_user-roles_small';
        $window->title = $this->module->t('{roles.title}', [$this->module->tH($partition->name ?? '')]);

        // столбцы (Ge.view.grid.Grid.columns GeJS)
        $window->grid->columns = [
            ExtGrid::columnNumberer(),
            [
                'text'      => '#Name',
                'dataIndex' => 'name',
                'cellTip'   => '{name}',
                'width'     => 350,
                'filter'    => ['type' => 'string'],
                'hideable'  => false
            ],
            [
                'text'        => ExtGrid::columnIcon('g-icon-m_unlock', 'svg'),
                'tooltip'     => '#User role availability',
                'xtype'       => 'g-gridcolumn-switch',
                'sortable'    => false,
                'collectData' => ['name'],
                'dataIndex'   => 'available',
                'filter'      => ['type' => 'boolean'],
                'hideable'    => false
            ]
        ];

        // сортировка строк в сетке
        $window->grid->sorters = [
            ['property' => 'name', 'direction' => 'ASC']
        ];
        // количество строк в сетке
        $window->grid->store->pageSize = 1000;
        $window->grid->router->route = Ge::alias('@match', '/roles');
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
