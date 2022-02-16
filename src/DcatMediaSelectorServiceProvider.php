<?php

namespace DeMemory\DcatMediaSelector;

use Dcat\Admin\Admin;
use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Form;
use DeMemory\DcatMediaSelector\Commands\InstallCommand;

class DcatMediaSelectorServiceProvider extends ServiceProvider
{
    protected $commands = [
        InstallCommand::class,
    ];

    protected $css = [
        'css/index.css',
        'js/bootstrap-table/bootstrap-table.css',
    ];

    protected $js = [
        'js/index.js',
        'js/bootstrap-table/bootstrap-table.min.js',
        'js/bootstrap-table/locale/bootstrap-table-{lang}.min.js',
        'js/sortable/Sortable.min.js',
    ];

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dcat-media-selector');
        $this->publishes([__DIR__ . '/../updates' => database_path('migrations')], 'dcat-media-selector-migrations');
        $this->publishes([__DIR__ . '/../resources/assets' => public_path('vendor/dcat-admin-extensions/de-memory/dcat-media-selector')], 'dcat-media-selector-assets');
    }

    public function register()
    {
        Form::extend('mediaSelector', MediaSelector::class);
    }

    public function init()
    {
        parent::init();

        $this->commands($this->commands);

        $this->registerPublishing();

    }
}
