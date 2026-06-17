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
use Ge\Panel\Data\Model\AdjacencyFormModel;

/**
 * Модель данных профиля раздела панели.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Model
 * @since 1.0
 */
class Form extends AdjacencyFormModel
{
    /**
     * Помощник.
     * 
     * @var \Ge\Data\Model\BaseModel
     */
    protected $helper;

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'useAudit'   => false,
            'tableName'  => '{{panel_partitionbar}}',
            'primaryKey' => 'id',
            'parentKey'  => 'parent_id',
            'countKey'   => 'count',
            // поля
            'fields' => [
                ['id'],
                [
                    'index',
                    'alias' => 'itemIndex',
                    'label' => 'Index'
                ],
                [
                    'name', 
                    'label' => 'Name'
                ],
                [
                    'code',
                    'label' => 'Code'
                ],
                [
                    'icon',
                    'label' => 'Icon'
                ],
                [
                    'icon_type', 
                    'alias' => 'iconType', 
                    'label' => 'Icon type'
                ],
                [
                    'visible',
                    'alias' => 'isVisible',
                    'label' => 'visible'
                ],
                ['count'],
                ['settings'],
                [
                    'parent_id',
                    'alias' => 'parentId',
                    'label' => 'Parent name'
                ]
            ],
            // зависимости
            'dependencies'    => [
                'delete'    => [
                    '{{panel_partitionbar_modules}}'    => ['partition_id' => 'id'],
                    '{{panel_partitionbar_extensions}}' => ['partition_id' => 'id'],
                    '{{panel_partitionbar_roles}}'      => ['partition_id' => 'id']
                ]
            ],
            // правила форматирования полей
            'formatterRules' => [
                [['name'], 'safe'],
                ['isVisible', 'logic'],
                ['parentId', 'combo']
            ],
            // правила валидации полей
            'validationRules' => [
                [['name'], 'notEmpty'],
                // порядковый номер
                [
                    'itemIndex', 
                    'between',
                    'min' => 1, 'max' => PHP_INT_MAX
                ],
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
                /** @var \Ge\Panel\Http\Response $response */
                $response = $this->response();
                $response
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']) // всплывающие сообщение
                        ->cmdReloadTreeGrid($this->module->viewId('grid'), $this->parentId ?: 'root'); // обновить дерево
            })
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                /** @var \Ge\Panel\Http\Response $response */
                $response = $this->response();
                $response
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']) // всплывающие сообщение
                        ->cmdReloadTreeGrid($this->module->viewId('grid')); // обновить дерево
            });
    }

    /**
     * Возвращает помощника.
     * 
     * @return \Ge\Data\Model\BaseModel
     */
    protected function getHelper()
    {
        if ($this->helper === null) {
            $this->helper = $this->module->getModel('Helper');
        }
        return $this->helper;
    }

    /**
     * {@inheritDoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            $types = [
                'font'  => 'Font glyph',
                'glyph' => 'Code glyph',
                'icon'  => 'Image',
                'css'   => 'CSS-class',
                'fcss'  => 'FCSS-class'
            ];
            // тип значка
            $iconType = Ge::$app->request->post('iconType');
            if (isset($types[$iconType])) {
                // значение типа значка
                $icon = Ge::$app->request->post('input-' . $iconType, false);
                if ($icon)
                    $this->icon = $icon;
                else
                    $this->addError($this->errorFormatMsg(Ge::t('app', 'Value is required and can\'t be empty'), $types[$iconType]));
            } else
                $this->addError(Ge::t('app', 'Invalid query parameter'));
            return !$this->hasErrors();
        }
        return $isValid;
    }

    /**
     * Возвращает значение для выпадающего списка разделов панели.
     * 
     * @return array
     */
    protected function getParentValue(): array
    {
        // значение могут быть: 'null' и больше нуля
        if ($this->parentId > 0) {
            /** @var \Rg\Backend\Partitionbar\Model\Partitionbar $partitionbar */
            $partitionbar = $this->module->getModel('Partitionbar');
            $item = $partitionbar->get($this->parentId);
            if ($item) {
                return [
                    'type'  => 'combobox',
                    'value' => $this->parentId,
                    'text'  => $item->name
                ];
            }
        }
        return [
            'type'  => 'combobox',
            'value' => 'null',
            'text'  => $this->t('[ main partition ]')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function processing(): void
    {
        parent::processing();

        /** @var array $parentId идент. раздела (его предок) */
        $this->parentId = $this->getParentValue();

        $this->response()
            ->meta
                ->cmdComponent($this->module->viewId('form') . '__f' . $this->iconType, 'setValue', [$this->icon]);
    }
}
