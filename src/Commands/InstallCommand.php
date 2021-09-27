<?php

namespace DeMemory\DcatMediaSelector\Commands;

use DeMemory\DcatMediaSelector\Models\MediaSelectorSeeder;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dcat-media-selector:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the store package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('migrate');

        $this->info('正在将本地文件写入数据库。该过程可能有点漫长.......');

        $this->call('db:seed', ['--class' => MediaSelectorSeeder::class]);

        $this->info('文件写入成功');
    }
}