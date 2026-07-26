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
 * Модель данных профиля записи выбора роли пользователя.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class RolesGridRow extends FormModel
{
    /**
     * Идентификатор роли пользователя.
     * 
     * @var int
     */
    protected int $roleId;

    /**
     * Идентификатор раздела.
     * 
     * @var int
     */
    protected int $partitionId; 

    /**
     * {@inheritdoc}
     */
    public function getModelName(): string
    {
        return 'partitionbarRolesRow';
    }

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{panel_partitionbar_roles}}',
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
                // если успешно добавлен доступ
                if ($message['success']) {
                    // если выбранная роль входит в раздел
                    $available = (int) $this->available > 0;
                    $message['message'] = $this->module->t(
                        'Partition element for user role {0} - ' . ($available > 0 ? 'enabled' : 'disabled'),
                        [$this->name]
                    );
                    $message['title'] = $this->t('Access to the partition');
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
     * Возвращает идентификатор роли пользователя.
     * 
     * @return int
     */
    public function getRoleId(): int
    {
        if (!isset($this->roleId)) {
            $this->roleId = $this->getIdentifier();
        }
        return $this->roleId;
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
        // если выбранная роль входит в раздел
        if ((int) $this->available > 0) {
            $columns = [
                'partition_id' => $this->getPartitionId(),
                'role_id'      => $this->getRoleId()
            ];
            $this->insertRecord($columns);
            // т.к. ключ составной, то при добавлении всегда будет "0"
            $this->result = 1;
        // если выбранная роль не входит в раздел
        } else {
            $this->result = $this->deleteRecord([
                'partition_id' => $this->getPartitionId(),
                'role_id'      => $this->getRoleId()
            ]);
        }
        $this->afterSave(true, $columns, $this->result, $this->saveMessage(true, (int) $this->result));
        return $this->result;
    }
}
