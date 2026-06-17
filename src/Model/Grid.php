<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Partitionbar\Model;

use Ge;
use Ge\Panel\Data\Model\AdjacencyGridModel;
use Ge\Panel\Helper\ExtGrid;

/**
 * Модель данных списка панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class Grid extends AdjacencyGridModel
{
    /**
     * {@inheritdoc}
     */
    public bool $collectRowsId = true;

    /**
     * URL-путь в последнем запросе.
     * 
     * @var string
     */
    protected string $rolesUrl = '';

    /**
     * URL-путь к расширениям.
     * 
     * @var string
     */
    protected string $extensionsUrl = '';

    /**
     * URL-путь к модулям.
     * 
     * @var string
     */
    protected string $modulesUrl = '';

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_partitionbar}}',
            'primaryKey' => 'id',
            'parentKey'  => 'parent_id',
            'countKey'   => 'count',
            'useAudit'   => false,
            'fields'     => [
                ['id'],
                ['extensionsUrl'],
                ['modulesUrl'],
                ['rolesUrl'],
                ['nameLo'],
                [
                    'index',
                    'alias' => 'itemIndex',
                    'title' => 'Index'
                ],
                [
                    'name',
                    'title' => 'Name'
                ],
                [
                    'code',
                    'title' => 'Code'
                ],
                [
                    'icon',
                    'title' => 'Icon'
                ],
                ['iconTag'],
                [
                    'icon_type',
                    'alias' => 'iconType',
                    'title' => 'Icon type'
                ],
                [
                    'visible',
                    'alias' => 'isVisible',
                    'title' => 'visible'
                ],
                ['count'],
                ['settings'],
                [
                    'parent_id',
                    'alias' => 'parentId',
                    'title' => 'Parent name'
                ]
            ],
            'resetIncrements' => ['{{panel_partitionbar}}'],
            'dependencies'    => [
                'deleteAll' => [
                    '{{panel_partitionbar_modules}}', 
                    '{{panel_partitionbar_extensions}}', 
                    '{{panel_partitionbar_roles}}'
                ],
                'delete'    => [
                    '{{panel_partitionbar_modules}}'    => ['partition_id' => 'id'],
                    '{{panel_partitionbar_extensions}}' => ['partition_id' => 'id'],
                    '{{panel_partitionbar_roles}}'      => ['partition_id' => 'id']
                ]
            ],
            'filter' => [
                'id' => ['operator' => '='],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_DELETE, function ($someRecords, $result, $message) {
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']) // всплывающие сообщение
                        ->cmdReloadTreeGrid($this->module->viewId('grid')); // обновить дерево
            })
            ->on(self::EVENT_AFTER_SET_FILTER, function ($filter) {
                $this->response()
                    ->meta
                        ->cmdReloadTreeGrid($this->module->viewId('grid'), 'root'); // обновить дерево
            });
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSelect(mixed $command = null): void
    {
        $this->extensionsUrl = Ge::alias('@match') . '/extensions/view/';
        $this->modulesUrl = Ge::alias('@match') . '/modules/view/';
        $this->rolesUrl = Ge::alias('@match') . '/roles/view/';
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow(array $row): array
    {
        $row['iconTag']  = ExtGrid::renderIcon($row['icon'], $row['icon_type']);
        $row['extensionsUrl'] = $this->extensionsUrl . $row['id'];
        $row['modulesUrl'] = $this->modulesUrl . $row['id'];
        $row['rolesUrl'] = $this->rolesUrl . $row['id'];

        // локализация названия
        if (strncmp($row['name'], '#', 1) === 0) {
            $row['nameLo'] = $this->module->t(ltrim($row['name'], '#'));
        }
        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRow(array &$row): void
    {
        // заголовок контекстного меню записи
        $row['popupMenuTitle'] = $row['name'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupplementRows(): array
    {
        if (empty($this->rowsId)) return [];

        $rows = [];

        $emptyRow = ['roles' => [], 'modules' => [], 'extensions' => []];
        /** @var \Ge\Db\Adapter\Adapter $db */
        $db = $this->getDb();
        // роли пользователей панели разделов
        /** @var \Ge\Db\Adapter\Driver\AbstractCommand $command */
        $command = $db
            ->createCommand(
                'SELECT `proles`.`partition_id`, `role`.`name` '
            . 'FROM {{panel_partitionbar_roles}} `proles` '
            . 'JOIN {{role}} `role` ON `role`.`id`=`proles`.`role_id` '
            . 'WHERE `proles`.`partition_id` IN (:partition)'
            )
            ->bindValues([':partition' => $this->rowsId]);
        $command->execute();
        while ($row = $command->fetch()) {
            $id = $row['partition_id'];
            if (!isset($rows[$id])) {
                $rows[$id] = $emptyRow;
                $rows[$id]['id'] = $id;
            }
            $rows[$id]['roles'][] = $row['name'];
        }

        // Модули панели разделов
        /**
         * @var array $moduleNames Имена и описание модулей в текущей локализации. 
         * Имеет вид: `[module_id => ['name' => 'Name', ...], ...]`.
         */
        $moduleNames = Ge::$app->modules->getRegistry()->getListNames();
        /** @var \Ge\Db\Sql\Select $select */
        $select = $db
            ->select('{{panel_partitionbar_modules}}')
                ->columns(['*'])
                ->where(['partition_id' => $this->rowsId]);
        /** @var \Ge\Db\Adapter\Driver\AbstractCommand $command */
        $command = $db->createCommand($select);
        $command->execute();
        while ($row = $command->fetch()) {
            $moduleId = $row['module_id'];
            $id       = $row['partition_id'];
            if (!isset($rows[$id])) {
                $rows[$id] = $emptyRow;
                $rows[$id]['id'] = $id;
            }
            // если модуль имеет имя и описание
            if (isset($moduleNames[$id])) {
                $rows[$id]['modules'][] = $moduleNames[$moduleId]['name'];
            } else {
                $rows[$id]['modules'][] = SYMBOL_NONAME;
            }
        }

        // Расширения панели разделов
        /**
         * @var array $extensionNames Имена и описание расширений в текущей локализации. 
         * Имеет вид: `[extension_id => ['name' => 'Name', ...], ...]`.
         */
        $extensionNames = Ge::$app->extensions->getRegistry()->getListNames();
        /** @var \Ge\Db\Sql\Select $select */
        $select = $db
            ->select('{{panel_partitionbar_extensions}}')
                ->columns(['*'])
                ->where(['partition_id' => $this->rowsId]);
        /** @var \Ge\Db\Adapter\Driver\AbstractCommand $command */
        $command = $db->createCommand($select);
        $command->execute();
        while ($row = $command->fetch()) {
            $extensionId = $row['extension_id'];
            $id          = $row['partition_id'];
            if (!isset($rows[$id])) {
                $rows[$id] = $emptyRow;
                $rows[$id]['id'] = $id;
            }
            // если модуль имеет имя и описание
            if (isset($extensionNames[$extensionId])) {
                $rows[$id]['extensions'][] = $extensionNames[$extensionId]['name'];
            } else {
                $rows[$id]['extensions'][] = SYMBOL_NONAME;
            }
        }
        return array_values($rows);
    }
}
