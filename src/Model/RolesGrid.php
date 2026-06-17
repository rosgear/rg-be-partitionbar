<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Partitionbar\Model;

use Ge\Panel\Data\Model\GridModel;

/**
 * Модель данных списка доступности разделов ролям пользователей.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class RolesGrid extends GridModel
{
    /**
     * Выбранный раздел.
     *
     * @var array
     */
    public array $partition = [];

    /**
     * {@inheritdoc}
     * 
     * не задействован, т.к. вся реализация в {@see ItemsGrid::fetchRows()}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_partitionbar_roles}}',
            'primaryKey' => 'id',
            'order'      => ['name' => 'asc'],
            'useAudit'   => false,
            'fields'     => [
                ['name'],
                [
                    'partition_id',
                    'alias' => 'partitionId'
                ],
                [
                    'role_id',
                    'alias' => 'roleId'
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
    public function beforeSelect(mixed $command = null): void
    {
        $command->bindValues([
            ':partition' => $this->partition['id'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS `role`.*,`roles`.`partition_id` '
             . 'FROM `{{role}}` `role` '
             . 'LEFT JOIN `{{panel_partitionbar_roles}}` `roles` ON `roles`.`role_id`=`role`.`id` AND `roles`.`partition_id`=:partition';
        return $this->selectBySql($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow(array $row): array
    {
        // доступность роли
        $row['available'] = empty($row['partition_id']) ? 0 : 1;;
        return $row;
    }
}
