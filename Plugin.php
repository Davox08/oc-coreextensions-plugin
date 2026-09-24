<?php namespace Davox\CoreExtensions;

use Backend;
use Event;
use System\Classes\PluginBase;
use System\Models\SiteDefinition;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/4.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['Davox.ContactHub', 'Davox.Icones'];

    /**
     * pluginDetails about this plugin.
     */
    public function pluginDetails()
    {
        return [
            'name' => 'CoreExtensions',
            'description' => 'No description provided yet...',
            'author' => 'Davox',
            'icon' => 'icon-leaf'
        ];
    }

    /**
     * register method, called when the plugin is first registered.
     */
    public function register()
    {
        //
    }

    /**
     * boot method, called right before the request route.
     */
    public function boot()
    {
        Event::listen('backend.form.extendFields', function($widget) {
            // 1. Inyectar el campo custom_flag_icon en SiteDefinition
            if ($widget->model instanceof SiteDefinition) {
                $widget->addFields([
                    'custom_flag_icon' => [
                        'label' => 'Flag Icon',
                        'type' => 'iconesfinder',
                        'tab' => 'General',
                        'span' => 'full',
                        'comment' => 'Select an icon to represent this site (e.g. flag icon).'
                    ]
                ]);
            }

            // 1.b. Inject header_variant and footer_variant to CMS Pages
            if ($widget->model instanceof \Cms\Classes\Page) {
                $widget->addFields([
                    'settings[viewBag][header_variant]' => [
                        'label' => 'Header Variant',
                        'type' => 'dropdown',
                        'tab' => 'cms::lang.editor.settings',
                        'span' => 'left',
                        'options' => \Davox\CoreExtensions\Classes\ThemeOptions::class . '::getAvailableHeaders',
                        'comment' => 'Select a custom header variant for this page.'
                    ],
                    'settings[viewBag][footer_variant]' => [
                        'label' => 'Footer Variant',
                        'type' => 'dropdown',
                        'tab' => 'cms::lang.editor.settings',
                        'span' => 'right',
                        'options' => \Davox\CoreExtensions\Classes\ThemeOptions::class . '::getAvailableFooters',
                        'comment' => 'Select a custom footer variant for this page.'
                    ]
                ], 'primary');
            }

            // 1.c. Inject header_variant and footer_variant to Offline Boxes Pages
            if ($widget->model instanceof \OFFLINE\Boxes\Models\Page) {
                $widget->addFields([
                    'custom_config[header_variant]' => [
                        'label' => 'Header Variant',
                        'type' => 'dropdown',
                        'tab' => 'CMS',
                        'span' => 'left',
                        'options' => \Davox\CoreExtensions\Classes\ThemeOptions::class . '::getAvailableHeaders',
                        'comment' => 'Select a custom header variant for this page.'
                    ],
                    'custom_config[footer_variant]' => [
                        'label' => 'Footer Variant',
                        'type' => 'dropdown',
                        'tab' => 'CMS',
                        'span' => 'right',
                        'options' => \Davox\CoreExtensions\Classes\ThemeOptions::class . '::getAvailableFooters',
                        'comment' => 'Select a custom footer variant for this page.'
                    ]
                ], 'primary');
            }

            // 2. Ocultar opciones condicionales en el Topbar (Tailor GlobalRecord)
            // Revisamos si el modelo es un registro global y si su blueprint es el Header (Site\Header)
            if (
                class_exists(\Tailor\Models\GlobalRecord::class) && 
                $widget->model instanceof \Tailor\Models\GlobalRecord && 
                $widget->model->blueprint_uuid === 'b095bb4d-774b-4475-9969-9ad77f24faf9'
            ) {
                $pluginManager = \System\Classes\PluginManager::instance();
                
                $hasUser = $pluginManager->hasPlugin('RainLab.User');
                $hasSearch = $pluginManager->hasPlugin('Offline.SiteSearch');
                $hasMall = $pluginManager->hasPlugin('Offline.Mall');

                // Campos repeater donde buscaremos los grupos
                $repeaters = ['topbar_left', 'topbar_center', 'topbar_right'];

                foreach ($repeaters as $fieldName) {
                    $field = $widget->getField($fieldName);
                    if ($field && isset($field->config['groups'])) {
                        $groups = $field->config['groups'];

                        if (!$hasUser && isset($groups['user_account'])) {
                            unset($groups['user_account']);
                        }
                        if (!$hasSearch && isset($groups['search_input'])) {
                            unset($groups['search_input']);
                        }
                        if (!$hasMall && isset($groups['mall_cart'])) {
                            unset($groups['mall_cart']);
                        }

                        $field->config['groups'] = $groups;
                    }
                }
            }
        });
    }

    /**
     * registerComponents used by the frontend.
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Davox\CoreExtensions\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * registerPermissions used by the backend.
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'davox.coreextensions.some_permission' => [
                'tab' => 'CoreExtensions',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * registerNavigation used by the backend.
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'coreextensions' => [
                'label' => 'CoreExtensions',
                'url' => Backend::url('davox/coreextensions/mycontroller'),
                'icon' => 'icon-leaf',
                'permissions' => ['davox.coreextensions.*'],
                'order' => 500,
            ],
        ];
    }
}
