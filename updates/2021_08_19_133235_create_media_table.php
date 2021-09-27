<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->comment('创建管理员id');
            $table->integer('media_group_id')->nullable()->default(0)->comment('媒体分组id');
            $table->enum('type', ['image', 'video', 'audio', 'powerpoint', 'code', 'zip', 'text', 'other'])->comment('类型');
            $table->string('disk')->comment("磁盘");
            $table->string('path')->comment('文件路径');
            $table->Integer('size')->comment('文件大小(K)');
            $table->string('file_ext')->comment("文件后缀");
            $table->string('file_name')->comment("文件名称");
            $table->string('meta')->comment("属性");
            $table->integer('created_at')->nullable()->comment("创建时间");
            $table->integer('updated_at')->nullable()->comment("更新时间");
            $table->index(['admin_id', 'media_group_id', 'type']);
        });

        \DB::statement("alter table media comment '媒体'; ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
