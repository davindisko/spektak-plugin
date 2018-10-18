<?php namespace DFE\Spektak;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'DFE\Spektak\Components\Dashboard' => 'dashboard',
        ];
    }

    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'Spektak',
                'description' => 'Manage configuration',
                'class'       => 'DFE\Spektak\Models\Settings',
                'icon'        => 'icon-simplybuilt',
                'order'       => 500
            ]
        ];   
    }

}
