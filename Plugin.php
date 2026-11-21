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
        });

        // Soporte de persistencia para Repeater en OFFLINE.Boxes
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
