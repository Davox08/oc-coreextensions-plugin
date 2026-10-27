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

            // 1.b. Removed header/footer variants injection as per user request

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

        // Soporte de persistencia para Repeater y FileUpload en OFFLINE.Boxes
        if (class_exists(\OFFLINE\Boxes\Models\Box::class)) {
            \OFFLINE\Boxes\Models\Box::extend(function ($model) {
                if (!isset($model->hasMany['items'])) {
                    $model->hasMany['items'] = [
                        \OFFLINE\Boxes\Models\RepeaterItem::class,
                        'key' => 'parent_id',
                        'delete' => true,
                        'replicate' => true,
                    ];
                }
            });
        }

        if (class_exists(\OFFLINE\Boxes\Models\RepeaterItem::class)) {
            \OFFLINE\Boxes\Models\RepeaterItem::extend(function ($model) {
                if (!isset($model->attachOne['image_desktop'])) {
                    $model->attachOne['image_desktop'] = [\System\Models\File::class, 'delete' => true];
                }
                if (!isset($model->attachOne['image_mobile'])) {
                    $model->attachOne['image_mobile'] = [\System\Models\File::class, 'delete' => true];
                }
                if (!isset($model->attachOne['bg_image'])) {
                    $model->attachOne['bg_image'] = [\System\Models\File::class, 'delete' => true];
                }
                if (!isset($model->attachOne['bg_video'])) {
                    $model->attachOne['bg_video'] = [\System\Models\File::class, 'delete' => true];
                }
            });
        }
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
