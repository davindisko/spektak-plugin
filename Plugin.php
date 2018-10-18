<?php namespace Davindisko\Spektak;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Davindisko\Spektak\Components\Dashboard' => 'dashboard',
        ];
    }

    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'Spektak',
                'description' => 'Manage configuration',
                'class'       => 'Davindisko\Spektak\Models\Settings',
                'icon'        => 'icon-simplybuilt',
                'order'       => 500
            ]
        ];   
    }

}
