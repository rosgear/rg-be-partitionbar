<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Partitionbar\Controller;

use Ge;
use Ge\Panel\Widget\EditWindow;
use Ge\Panel\Controller\FormController;

/**
 * Контроллер формы панели раздела.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Partitionbar\Controller
 * @since 1.0
 */
class Form extends FormController
{
    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var \Ge\FontAwesome\FontAwesome $fa */
        $fa = Ge::$app->fontAwesome;
        $fa->loadMap('v5.8 pro');

        $faItems = $fa->getRenderItems('fab ');

        /** @var EditWindow $window */
        $window = parent::createWidget();

        // панель формы (Ge.view.form.Panel GeJS)
        $window->form->autoScroll = true;
        $window->form->bodyPadding = 10;
        $window->form->layout = 'anchor';
        $window->form->defaults = [
            'labelAlign' => 'right',
            'labelWidth' => 95
        ];
        $window->form->controller = 'rg-be-partitionbar-form';
        $window->form->loadJSONFile('/form', 'items', [
            '@comboStoreUrl' => [Ge::alias('@match', '/trigger/combo')],
            '@iconStoreData' => $faItems
        ]);

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->width = 480;
        $window->height = 520;
        $window->layout = 'fit';
        $window->resizable = false;
        $window
            ->setNamespaceJS('Rg.be.partitionbar')
            ->addRequire('Rg.be.partitionbar.FormController' . (GE_DEBUG ? '-debug' : ''))
            ->addCss(GE_DEBUG ? '/form.css' : '/form.min.css');
        return $window;
    }
}
