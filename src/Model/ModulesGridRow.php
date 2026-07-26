<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Partitionbar\Model;

use Ge\Panel\Data\Model\FormModel;

/**
 * Модель данных профиля записи выбора модуля.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class ModulesGridRow extends FormModel
{
    /**
     * Идентификатор модуля.
     * 
     * @var int
     */
    protected int $moduleId;

    /**
     * Идентификатор раздела.
     * 
     * @var int
     */
    protected int $partitionId; 

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_partitionbar_modules}}',
            'primaryKey' => 'id',
            'useAudit'   => false,
            'fields'     => [
                ['name'],
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

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                // если успешно добавлен модуль
                if ($message['success']) {
                    // если выбранный модуль входит в раздел
                    $available = (int) $this->available;
                    $message['message'] = $this->module->t(
                        'Partition element for module {0} - ' . ($available > 0 ? 'enabled' : 'disabled'),
                        [$this->name]
                    );
                    $message['title'] = $this->t('Module in partition');
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function get(mixed $identifier = null): ?static
    {
        // т.к. записи формируются при выводе списка, то нет
        // необходимости делать запрос к бд (нет основной таблицы)
        return $this;
    }

    /**
     * Возвращает идентификатор модуля.
     * 
     * @return int
     */
    public function getModuleId(): int
    {
        if (!isset($this->moduleId)) {
            $this->moduleId = $this->getIdentifier();
        }
        return $this->moduleId;
    }

    /**
     * Возвращает идентификатор раздела.
     * 
     * @return int
     */
    public function getPartitionId(): int
    {
        if (!isset($this->partitionId)) {
            $store = $this->module->getStorage();
            $this->partitionId = isset($store->partition['id']) ? (int) $store->partition['id'] : 0;
        }
        return $this->partitionId;
    }

    /**
     * {@inheritdoc}
     */
    protected function insertProcess(?array $attributes = null): false|int|string
    {
        if (!$this->beforeSave(true))
            return false;

        $columns = [];
        // если выбранный модуль входит в раздел
        if ((int) $this->available > 0) {
            $columns = [
                'partition_id' => $this->getPartitionId(),
                'module_id'    => $this->getModuleId()
            ];
            $this->insertRecord($columns);
            // т.к. ключ составной, то при добавлении всегда будет "0"
            $this->result = 1;
        // если выбранный модуль не входит в раздел
        } else {
            $this->result = $this->deleteRecord([
                'partition_id' => $this->getPartitionId(),
                'module_id'    => $this->getModuleId()
            ]);
        }
        $this->afterSave(true, $columns, $this->result, $this->saveMessage(true, (int) $this->result));
        return $this->result;
    }
}
