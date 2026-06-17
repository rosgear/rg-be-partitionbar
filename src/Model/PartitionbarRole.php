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
use Ge\Db\ActiveRecord;

/**
 * Модель данных элементов панели разделов для определения доступности ролям пользователей.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class PartitionbarRole extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function primaryKey(): string
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function tableName(): string
    {
        return '{{panel_partitionbar_roles}}';
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'id'          => 'id',
            'partitionId' => 'partition_id',
            'roleId'      => 'role_id'
        ];
    }

    /**
     * Возвращает запись по указанному идентификатору элемента панели раздела и роли 
     * пользователя.
     * 
     * @see ActiveRecord::selectOne()
     * 
     * @param int|string $partitionId Идентификатор элемента панели разделов.
     * @param int $roleId Идентификатор роли пользователя.
     * 
     * @return PartitionbarRole|null Активная запись при успешном запросе, иначе `null`.
     */
    public function get(int $partitionId, int $roleId): ?static
    {
        return $this->selectOne([
            'partition_id' => $partitionId,
            'role_id'      => $roleId
        ]);
    }

    /**
     * Возвращает все доступные идентификаторы элементов панели разделов для текущей 
     * роли пользователя.
     * 
     * @param bool $toString Если `true`, возвратит идентификаторы через разделитель ',' 
     *     (по умолчанию `false`).
     * 
     * @return array|string
     */
    public function getAccessible(bool $toString = false): array|string
    {
        $roleId = Ge::userIdentity()->getRoles()->ids(false);
        if (empty($roleId)) {
            return [];
        }
        /** @var \Ge\Db\Adapter\Adapter $db */
        $db = $this->getDb();
        /** @var \Ge\Db\Sql\Select $select */
        $select = $db
            ->select($this->tableName())
            ->columns(['*'])
            ->where([
                // доступные роли пользователю
                'role_id' => $roleId
            ]);
        /** @var \Ge\Db\Adapter\Driver\AbstractCommand $command */
        $command = $db
            ->createCommand($select)
                ->query();
        $rows = [];
        while ($row = $command->fetch()) {
            $rows[] = $row['partition_id'];
        }
        return $toString ? implode(',', $rows) : $rows;
    }

    /**
     * Возвращает все записи (элементы) панели разделов соответствующие ролям пользователей.
     * 
     * Ключом каждой записи является значение первичного ключа {@see ActiveRecord::tableName()} 
     * текущей таблицы.
     * 
     * @see ActiveRecord::fetchAll()
     * 
     * @param bool $caching Указывает на принудительное кэширование. Если служба кэширования 
     *     отключена, кэширование не будет выполнено (по умолчанию `true`).
     * 
     * @return array
     */
    public function getAll(bool $caching = true): ?array
    {
        if ($caching)
            return $this->cache(
                function () { return $this->fetchAll($this->primaryKey(), $this->maskedAttributes()); },
                null,
                true
            );
        else
            return $this->fetchAll($this->primaryKey(), $this->maskedAttributes());
    }
}
