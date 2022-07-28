<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_group', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->comment('创建管理员id');
            $table->string('name')->comment('分组名称');
            $table->integer('created_at')->nullable()->comment("创建时间");
            $table->integer('updated_at')->nullable()->comment("更新时间");
            $table->index(['admin_id']);
        });
        \DB::statement("alter table media_group comment '媒体分组'; ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_group');
    }
}
