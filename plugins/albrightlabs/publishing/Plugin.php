<?php namespace Albrightlabs\Publishing;

use Event;
use Backend;
use System\Classes\PluginBase;
use Albrightlabs\Publishing\Models\ScheduledRelease;

/**
 * Publishing Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Publishing',
            'description' => 'Provides scheduled publishing support for SG.',
            'author'      => 'Albright Labs LLC',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {

        // Extend rainlab.pages form usage
        Event::listen('backend.form.extendFields', function($widget) {
            if(!$widget->getController() instanceof \RainLab\Pages\Controllers\Index){ return; }
            if(!$widget->model instanceof \RainLab\Pages\Classes\Page){ return; }
            if($widget->isNested) { return; }
            $widget->addFields([
                '_scripts' => [
                    'label' => '',
                    'type'  => 'partial',
                    'path'  => '$/albrightlabs/publishing/widgets/schedulerelease/partials/_field_scripts.htm',
                ],
            ]);
            $widget->addTabFields([
                'preview' => [
                    'label'   => 'Draft Versions',
                    'type'    => 'partial',
                    'path'    => '$/albrightlabs/publishing/widgets/schedulerelease/partials/_field_drafts.htm',
                    'context' => ['update','preview'],
                    'tab'     => 'Drafts',
                ],
            ]);
        });

        // Extend rainlab.pages form
        Event::listen('backend.form.extendFields', function($widget) {
            if(!$widget->getController() instanceof \Albrightlabs\Glossary\Controllers\Terms){ return; }
            if(!$widget->model instanceof \Albrightlabs\Glossary\Models\Term) { return; }
            if($widget->isNested){ return; }
            // $widget->addFields([
            //     'preview' => [
            //         'label'   => '',
            //         'type'    => 'partial',
            //         'path'    => '$/albrightlabs/publishing/widgets/preview/partials/_field_preview.htm',
            //         'context' => ['update','preview'],
            //     ],
            // ]);
        });

        // Bind schedulerelease widget to controllers that need it
        \RainLab\Pages\Controllers\Index::extend(function($controller) {
            if (!$controller instanceof \RainLab\Pages\Controllers\Index) { return; }
            $pendingreleases = new \Albrightlabs\Publishing\Widgets\ScheduleRelease($controller);
            $pendingreleases->alias = 'schedulerelease';
            $pendingreleases->bindToController();
        });
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [];
        return [
            'albrightlabs.publishing.manage_pending_releases' => [
                'tab' => 'Publishing',
                'label' => 'Manage system-wide pending releases',
            ],
        ];
    }

    /**
     * Registers settings menu item for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [];
        return [
            'pendingreleases' => [
                'label'       => 'Pending Releases',
                'icon'        => 'icon-calendar-o',
                'description' => 'View and manage system-wide pending releases.',
                'url'         => Backend::url('albrightlabs/publishing/pendingreleases'),
                'permissions' => ['albrightlabs.publishing.manage_pending_releases'],
                'keywords'    => 'publishing pending releases',
                'category'    => 'Publishing',
                'order'       => 510,
            ],
        ];
    }
}
