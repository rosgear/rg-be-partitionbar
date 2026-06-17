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
use Ge\Panel\Data\Model\GridModel;
use Ge\Db\Sql;

/**
 * Модель данных списка модулей панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class ModulesGrid extends GridModel
{
    /**
     * Выбранный раздел.
     *
     * @var array|null
     */
    public ?array $partition;

    /**
     * {@inheritdoc}
     * 
     * не задействован, т.к. вся реализация в {@see ModulesGrid::fetchRows()}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName' => '{{panel_partitionbar_modules}}',
            'useAudit'  => false,
            'fields'    => [
                ['name'],
                ['path'],
                ['route'],
                ['icon'],
                [
                    'partition_id',
                    'alias' => 'partitionId'
                ],
                [
                    'module_id',
                    'alias' => 'moduleId'
                ],
                ['available']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->partition = $this->module->getStorage()->partition;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilter(Sql\AbstractSql $operator): void
    {
        $operator
            ->where(['partition_id' => $this->partition['id']]);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRows(mixed $receiver = null): array
    {
        $rows = [];
        // конфигурации установленных модулей
        $modulesInfo = Ge::$app->modules->getRegistry()->getListInfo(true, false);
        $partitionItems = $receiver->queryAll('module_id');
        foreach ($modulesInfo as $rowId => $moduleInfo) {
            $rows[] = [
                'id'          => $rowId,
                'moduleId'    => $rowId,
                'partitionId' => $this->partition['id'],
                'icon'        => $moduleInfo['smallIcon'],
                'name'        => $moduleInfo['name'],
                'available'   => (int) isset($partitionItems[$rowId])
            ];
        }
        return $rows;
    }
}
