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
use Closure;
use Ge\Db\Sql\Where;
use Ge\Db\Sql\Select;
use Ge\Db\ActiveRecord;

/**
 * Модель данных связи модулей с элементами панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class PartitionbarModule extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function primaryKey(): string
    {
        return 'moduleId';
    }

    /**
     * {@inheritdoc}
     */
    public function tableName(): string
    {
        return '{{panel_partitionbar_modules}}';
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'partitionId' => 'partition_id',
            'moduleId'    => 'module_id'
        ];
    }

    /**
     * Возвращает запись по указанному идентификатору элемента панели раздела и модуля.
     * 
     * @see ActiveRecord::selectOne()
     * 
     * @param int|string $partitionId Идентификатор элемента панели разделов.
     * @param int $moduleId Идентификатор модуля.
     * 
     * @return PartitionbarModule|null Активная запись при успешном запросе, иначе `null`.
     */
    public function get(int $partitionId, int $moduleId): ?static
    {
        return $this->selectOne([
            'partition_id' => $partitionId,
            'module_id'    => $moduleId
        ]);
    }

    /**
     * {@inheritdoc}
     * 
     * @param bool $accessible Если `true`, возвратит все доступные модули элементов панели 
     *     разделов для текущей роли пользователя (по умолчанию `true`).
     */
    public function fetchAll(
        ?string $fetchKey = null, 
        array $columns = ['*'], 
        Where|Closure|string|array|null $where = null, 
        string|array|null $order = null,
        bool $accessible = true
    ): array
    {
        /** @var Select $select */
        $select = $this->select($columns, $where);
        if ($order)
            $select->order($order);
        // проверка доступа
        if ($accessible) {
            /** @var string $modulesId Доступные пользователю идентификаторы модулей */
            $modulesId = Ge::userIdentity()->getViewableModules();
            if ($modulesId)
                $select->where("module_id IN ($modulesId)");
            // нет доступных модулей
            else
                return [];
        }
        return $this
            ->getDb()
                ->createCommand($select)
                    ->queryAll($fetchKey);
    }

    /**
     * Возвращает все доступные идентификаторы модулей, соответствующие элементам панели 
     * разделов для текущей роли пользователя.
     * 
     * @param bool $group Если `true`, группирует результат по идентификатору элементов 
     *     панели разделов (по умолчанию `true`).
     * @param bool $toString Если `true`, возвратит идентификаторы через разделитель ',' 
     *     (по умолчанию `false`).
     * 
     * @return array|string
     */
    public function getAccessibleItems(bool $group = true, bool $toString = false): array|string
    {
        // доступные пользователю идентификаторы модулей
        $modulesId = Ge::userIdentity()->getModules();
        if (empty($modulesId)) {
            return $toString ? '' : [];
        }
        /** @var \Ge\Db\Adapter\Adapter $db */
        $db = $this->getDb();
        /** @var \Ge\Db\Sql\Select $select */
        $select = $db
            ->select($this->tableName())
            ->columns(['*'])
            ->where("module_id IN ($modulesId)");
        /** @var \Ge\Db\Adapter\Driver\AbstractCommand $command */
        $command = $db
            ->createCommand($select)
                ->query();
        $rows = [];
        while ($row = $command->fetch()) {
            if ($group) {
                $id = $row['partition_id'];
                if (!isset($rows[$id])) {
                    $rows[$id] = [];
                }
                $rows[$id][] = $row['module_id'];
            } else {
                $rows[] = $row['module_id'];
            }
        }
        if ($group) {
            if ($toString) {
                foreach ($rows as $id => &$modules) {
                    $modules = implode(',', $modules);
                }
            } else
                return $rows;
        }
        return $toString ? implode(',', $rows) : $rows;
    }

    /**
     * Возвращает идентификаторы модулей, соответствующие элементам панели разделов 
     * для текущей роли пользователя.
     * 
     * @param bool $group Если `true`, группирует результат по идентификатору элементов 
     *     панели разделов (по умолчанию `true`).
     * @param bool $toString Если `true`, возвратит идентификаторы через разделитель ',' 
     *     (по умолчанию `false`).
     * 
     * @return array|string
     */
    public function getItems(bool $group = true, bool $toString = false): array|string
    {
        /** @var \Ge\Db\Adapter\Adapter $db */
        $db = $this->getDb();
        /** @var \Ge\Db\Sql\Select $select */
        $select = $db
            ->select($this->tableName())
            ->columns(['*']);
        /** @var \Ge\Db\Adapter\Driver\AbstractCommand $command */
        $command = $db
            ->createCommand($select)
                ->query();
        $rows = [];
        while ($row = $command->fetch()) {
            if ($group) {
                $id = $row['partition_id'];
                if (!isset($rows[$id])) {
                    $rows[$id] = [];
                }
                $rows[$id][] = $row['module_id'];
            } else {
                $rows[] = $row['module_id'];
            }
        }
        if ($group) {
            foreach ($rows as $id => &$modules) {
                $modules = implode(',', $modules);
            }
        }
        return $toString ? implode(',', $rows) : $rows;
    }


    /**
     * Возвращает все записи (модули) соответствующие элементам панели разделов.
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
    public function getAll(bool $caching = true, bool $accessible = true): ?array
    {
        if ($caching)
            return $this->cache(
                function () use ($accessible) {
                    return $this->fetchAll(null, $this->maskedAttributes(), null, null, $accessible); 
                },
                null,
                true
            );
        else
            return $this->fetchAll(null, $this->maskedAttributes(), null, null, $accessible);
    }

    /**
     * Удаляет записи по указанному идентификатору модуля.
     * 
     * @param int $moduleId Идентификатор модуля.
     * 
     * @return false|int Возвращает значение `false`, если ошибка выполнения запроса. 
     *     Иначе, количество удалённых записей.
     */
    public function deleteByModule(int $moduleId): false|int
    {
        return $this->deleteRecord(['module_id' => $moduleId]);
    }

    /**
     * Удаляет записи по указанному идентификатору раздела.
     * 
     * @param int $partitionId Идентификатор раздела.
     * 
     * @return false|int Возвращает значение `false`, если ошибка выполнения запроса. 
     *     Иначе, количество удалённых записей.
     */
    public function deleteByPartition(int $partitionId): false|int
    {
        return $this->deleteRecord(['partition_id' => $partitionId]);
    }
}
