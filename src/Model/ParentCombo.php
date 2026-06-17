<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Partitionbar\Model;

use Ge\Db\Sql;
use Ge\Panel\Data\Model\Combo\ComboModel;

/**
 * Модель данных выпадающего списка панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class ParentCombo extends ComboModel
{
    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_partitionbar}}',
            'primaryKey' => 'id',
            'order'      => ['name' => 'asc'],
            'searchBy'   => 'name',
            'fields' => [
                ['name']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilter(Sql\AbstractSql $operator): void
    {
        if ($this->search) {
            $operator->where->like($this->dataManager->searchBy, '%' . $this->search . '%');
        }
        $operator->where('parent_id IS NULL'); // если раздел 1-о уровня
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `{{panel_partitionbar}}`';

        /** @var array $result */
        $result = $this->selectBySql($sql);
        // т.к. применяются названия разделов по умолчанию (с "#"), 
        // то необходимо выполнить их локализацию
        $result['rows'] = $this->module->tH($result['rows']);

        array_unshift($result['rows'], $this->noneRow());
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function noneRow(): array
    {
        return ['null', $this->t('[ main partition ]')];
    }
}
