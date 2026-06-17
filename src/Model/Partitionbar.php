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
 * Модель данных панели (элементов панели) разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class Partitionbar extends ActiveRecord
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
        return '{{panel_partitionbar}}';
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'id'       => 'id',
            'parentId' => 'parent_id',
            'count'    => 'count',
            'index'    => 'index',
            'code'     => 'code',
            'icon'     => 'icon',
            'iconType' => 'icon_type',
            'name'     => 'name',
            'visible'  => 'visible'
        ];
    }

    /**
     * {@inheritdoc}
     * 
     * @see Partitionbar::getAccessible()
     * 
     * @param bool $accessible Если `true`, возвратит все доступные элементы панели 
     *     разделов для текущей роли пользователя (по умолчанию `true`).
     */
    public function fetchAll(
        string $fetchKey = null, 
        array $columns = ['*'], 
        Where|Closure|string|array|null $where = null, 
        string|array|null $order = null,
        bool $accessible = true
    ): array
    {
        /** @var Select $select */
        $select = $this->select($columns, $where);
        if ($order === null) {
            $order = ['index' => 'ASC'];
        }
        $select->order($order);
        // проверка доступа
        if ($accessible) {
            $partitionId = $this->getAccessible();
            // если нет доступных элементов
            if (empty($partitionId)) {
                return [];
            }
            $select->where(['id' => $partitionId]);
        }
        return $this
            ->getDb()
                ->createCommand($select)
                    ->queryAll($fetchKey);
    }

    /**
     * @var PartitionbarRole
     */
    protected PartitionbarRole $partitionbarRole;

    /**
     * Возвращает все доступные идентификаторы элементов панели разделов для текущей 
     * роли пользователя.
     * 
     * @param bool $toString Если `true`, возвратит идентификаторы через разделитель ',' 
     *     (по умолчанию `true`).
     * 
     * @return array|string
     */
    public function getAccessible(bool $toString = false): array|string
    {
        if (!isset($this->partitionbarRole)) {
            $this->partitionbarRole  = new PartitionbarRole();
        }
        return $this->partitionbarRole ->getAccessible($toString);
    }

    /**
     * Возвращает запись по указанному значению первичного ключа.
     * 
     * @see ActiveRecord::selectByPk()
     * 
     * @param mixed $id Идентификатор записи.
     * 
     * @return null|Partitionbar Активная запись при успешном запросе, иначе `null`.
     */
    public function get(mixed $identifier): ?static
    {
        return $this->selectByPk($identifier);
    }

    /**
     * Возвращает все "корневые" элементы (не имеющих родителя) панели разделов.
     * 
     * @param bool $accessible Если `true`, возвратит только доступные элементы для текущей 
     *     роли пользователя (по умолчанию `true`).
     * 
     * @return array
     */
    public function getRoot(bool $accessible = true): array
    {
        return $this->getChildren(null, $accessible);
    }

    /**
     * Возвращает все (дочернии) элементы панели разделов по указанному идентифкатору 
     * родителя.
     * 
     * @param int|string $parentId Идентифкатор родителя, который содержит дочернии 
     *     элементы панели разделов. Если `null`, возвратит все элементы (по умолчанию `null`). 
     * @param bool $accessible Если `true`, возвратит только доступные элементы для текущей 
     *     роли пользователя (по умолчанию `true`).
     * 
     * @return array
     */
    public function getChildren($parentId = null, bool $accessible = true): array
    {
        return $this->fetchAll(
            null, 
            ['*'],
            ['visible' => 1, 'parent_id' => $parentId], null,
            $accessible
        );
    }

    protected function getChildrenItems($parentId, array $items, bool $onlyId = true): array
    {
        $result = [];
        foreach ($items as $id => $item) {
            if ($item['parent_id'] == $parentId) {
                $result[] = $onlyId ? $item['id'] : $item;
                $subresult = $this->getChildrenItems($item['id'], $items, $onlyId);
                if ($subresult) {
                    $result = array_merge($result, $subresult);
                }
            }
        }
        return $result;
    }

    public function getRecursiveChildren($parentId, bool $accessible = true, bool $onlyId = false): array
    {
        $items = $this->fetchAll(
            $this->primaryKey(), 
            ['*'],
            ['visible' => 1], null, 
            $accessible
        );
        return $this->getChildrenItems($parentId, $items);
    }

    /**
     * Возвращает записи модулей принадлежащих элементам панели разделов.
     * 
     * Если значение `true` аргумента `$group`, то результат имеет вид:
     * ```php
     * [
     *    'partitionbar_id' => [
     *        ['id' => 'module_id',], // конфигурация установленного модуля
     *        // ...
     *    ],
     *    // ...
     * ]
     * ```
     * Если значение `false`:
     * ```php
     * [
     *     ['id' => 'module_id',], // конфигурация установленного модуля
     *     // ...
     * ]
     * ```
     * 
     * @param bool $group Если `true`, группирует результат по идентификатору элементов 
     *     панели разделов (по умолчанию `true`).
     * @param bool $accessible Если `true`, возвратит только доступные модули для текущей 
     *     роли пользователя (по умолчанию `true`).
     * 
     * @return array
     */
    public function getModules(bool $group = true, bool $accessible = true): array
    {
        $result = [];
        /** @var \Ge\ModuleManager\ModuleRegistry $installed Установленные модули */
        $installed = Ge::$app->modules->getRegistry();
        /** 
         * @var array $modules Конфигурация установленных модулей. 
         * Имеет вид: `[module_id1 => [...], module_id2 => [...], ...]`.
         */
        $modules = $installed->getMap();
        /** 
         * @var array $barModules Модули пренадлежащие панеле разделов. 
         * Имеет вид: `[['partitionId' => 1, 'moduleId' => 2], ...]`.
         */
        $barModules = (new PartitionbarModule())->getAll(false, $accessible);
        if (empty($barModules) || empty($modules)) {
            return $result;
        }
        /** 
         * @var array $names Названия установленных модулей в текущей локализации.
         * Имеет вид: `[module_id1 => ['name' => 'Name', 'description' => 'Description', ...], ...]`.
         */
        $names = Ge::$app->modules->selectNames();
        foreach ($barModules as $index => $barModule) {
            $module = $modules[$barModule['moduleId']] ?? null;
            if ($module) {
                // если моудль не доступен
                if (!$module['enabled'] || !$module['visible'] || $module['use'] !== BACKEND) continue;
                $icons = $installed->getIcon($module);
                // определение названия и описания модуля
                if (isset($names[$module['rowId']])) {
                    $name = $names[$module['rowId']];
                    $module['name'] = $name['name'];
                    $module['description'] = $name['description'];
                }
                $module['icon'] = $icons['icon'];
                $module['smallIcon'] = $icons['small'];
                // c группированием
                if ($group) {
                    // если нет еще группы
                    if (!isset($result[$barModule['partitionId']]))
                        $result[$barModule['partitionId']] = [$module];
                    else  
                        $result[$barModule['partitionId']][] = $module;
                // без группированием
                } else {
                    $result[] = $module;
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает записи расширений модулей принадлежащих элементам панели разделов.
     * 
     * Если значение `true` аргумента `$group`, то результат имеет вид:
     * ```php
     * [
     *    'partitionbar_id' => [
     *        ['id' => 'extension_id',], // конфигурация установленного расширения модуля
     *        // ...
     *    ],
     *    // ...
     * ]
     * ```
     * Если значение `false`:
     * ```php
     * [
     *     ['id' => 'extension_id',], // конфигурация установленного расширения модуля
     *     // ...
     * ]
     * ```
     * 
     * @param bool $group Если `true`, группирует результат по идентификатору элементов 
     *     панели разделов (по умолчанию `true`).
     * @param bool $accessible Если `true`, возвратит только доступные расширения модулей 
     *     для текущей роли пользователя (по умолчанию `true`).
     * 
     * @return array
     */
    public function getExtensions(bool $group = true, bool $accessible = true): array
    {
        $result = [];
        /** @var \Ge\ModuleManager\ExtensionRegistry $installed Установленные расширения */
        $installed = Ge::$app->extensions->getRegistry();
        /** 
         * @var array $extensions Конфигурация установленных расширений. 
         * Имеет вид: `[extension_id1 => [...], extension_id2 => [...], ...]`.
         */
        $extensions = $installed->getMap();
        /** 
         * @var array $barExtensions Все расширения в панеле разделов. 
         * Имеет вид: `[['partitionId' => 1, 'extensionId' => 2], ...]`.
         */
        $barExtensions = (new PartitionbarExtension())->getAll(false, $accessible);
        if (empty($barExtensions) || empty($extensions)) {
            return $result;
        }
        /** 
         * @var array $names Названия установленных расширений в текущей локализации.
         * Имеет вид: `[extension_id => ['name' => 'Название', 'description' => 'Описание', ...], ...]`.
         */
        $names = Ge::$app->extensions->selectNames();
        foreach ($barExtensions as $index => $barExtension) {
            $extension = $extensions[$barExtension['extensionId']] ?? null;
            if ($extension) {
                // если расширение не доступно
                if (!$extension['enabled']) continue;
                $icons = $installed->getIcon($extension);
                // определение названия и описания расширения
                if (isset($names[$extension['rowId']])) {
                    $name = $names[$extension['rowId']];
                    $extension['name'] = $name['name'];
                    $extension['description'] = $name['description'];
                }
                $extension['icon'] = $icons['icon'];
                $extension['smallIcon'] = $icons['small'];
                // c группированием
                if ($group) {
                    // если нет еще группы
                    if (!isset($result[$barExtension['partitionId']]))
                        $result[$barExtension['partitionId']] = [$extension];
                    else  
                        $result[$barExtension['partitionId']][] = $extension;
                // без группированием
                } else {
                    $result[] = $extension;
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает элементы панели разделов в виде пар 'идентификатор, имя' для поля с выпадающем списком.
     * 
     * @param string $valueField Имя поля с идентификатором элементов (по умолчанию 'id').
     * @param string $displayField Имя поля с именем элементов (по умолчанию 'name').
     * @param bool $useNone Если `true`, добавит к результату пару 'null, без выбора' (по умолчанию `false`).
     * 
     * @return array
     */
    public function getItemPairs(string $valueField = 'id', string $displayField = 'name', bool $useNone = false): array
    {
        /** @var Select $select */
        $select = new Select($this->tableName());
        $select
            ->columns([$valueField, $displayField])
            ->order([$displayField => 'ASC']);
        $rows = $this
            ->getDb()
                ->createCommand($select)
                    ->queryTo([$valueField, $displayField]);
        if ($useNone) {
            array_unshift($rows, ['null', Ge::t(BACKEND, '[None]')]);
        }
        return $rows;
    }

    /**
     * Возвращает все записи (элементы) панели разделов с указанным ключом.
     * 
     * Ключом каждой записи является значение первичного ключа {@see ActiveRecord::tableName()} 
     * текущей таблицы.
     * 
     * @see Partitionbar::fetchAll()
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
                function () { return $this->fetchAll($this->primaryKey(), $this->maskedAttributes(), ['visible' => 1]); },
                null,
                true
            );
        else
            return $this->fetchAll($this->primaryKey(), $this->maskedAttributes(), ['visible' => 1]);
    }
}
