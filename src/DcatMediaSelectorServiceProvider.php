<?php

namespace DeMemory\DcatMediaSelector;

use Dcat\Admin\Admin;
use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Form;

class DcatMediaSelectorServiceProvider extends ServiceProvider
{
    protected $js = [
        'js/index.js',
        'js/util.js',
        'dist/sortable/Sortable.min.js',
        'dist/loadingoverlay/loadingoverlay.min.js',
        'dist/jquery-context-menu/jquery.contextMenu.min.js',
        'dist/jquery-context-menu/jquery.ui.position.min.js',
        'dist/bootstrap-table/bootstrap-table.min.js',
        'dist/bootstrap-table/locale/bootstrap-table-zh-CN.min.js',
    ];
    protected $css = [
        'css/index.css',
        'dist/jquery-context-menu/jquery.contextMenu.min.css',
        'dist/bootstrap-table/bootstrap-table.css',

    ];

    public function register()
    {
        Form::extend('mediaSelector', MediaSelector::class);

        $this->registerPublishing();
    }

    public function init()
    {
        parent::init();

        $lang = config('app.locale') === 'en' ? '' : str_replace('_', '-', config('app.locale'));

        Admin::requireAssets(['@de-memory.dcat-media-selector', '@select2'], ['lang' => $lang]);
    }

    protected function registerPublishing()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dcat-media-selector');

//        $this->publishes([__DIR__ . '/../updates' => database_path('migrations')], 'dcat-media-selector-migrations');

        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/dcat-admin-extensions/de-memory/dcat-media-selector')
        ], 'dcat-media-selector-assets');
    }
}