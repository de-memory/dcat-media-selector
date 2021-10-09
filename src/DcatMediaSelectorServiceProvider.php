<?php

namespace DeMemory\DcatMediaSelector;

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
    ];

    protected $js = [
        'js/index.js',
        'https://unpkg.com/bootstrap-table@1.15.3/dist/bootstrap-table.min.js',
        'https://unpkg.com/bootstrap-table@1.15.3/dist/locale/bootstrap-table-zh-CN.min.js',
        'https://unpkg.com/bootstrap-table@1.15.3/dist/locale/bootstrap-table-en-US.min.js',
        'https://cdn.bootcdn.net/ajax/libs/Sortable/1.14.0/Sortable.min.js',
    ];

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dcat-media-selector');
        $this->publishes([__DIR__.'/../updates' => database_path('migrations')], 'dcat-media-selector-migrations');
        $this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/dcat-admin-extensions/de-memory/dcat-media-selector')], 'dcat-media-selector-assets');
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
