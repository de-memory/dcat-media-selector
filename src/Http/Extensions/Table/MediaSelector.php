<?php

namespace DeMemory\DcatMediaSelector\Http\Extensions\Table;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use DeMemory\DcatMediaSelector\Models\Media;

class MediaSelector extends LazyRenderable
{
    public function grid(): Grid
    {
        $ids = $this->payload['ids'] ?? 1;

        return Grid::make(new Media(), function (Grid $grid) use ($ids) {
            if (request()->get('_view_') !== 'list') {
                // 设置自定义视图
                $grid->view('dcat-media-selector::custom');

                $grid->setActionClass(Grid\Displayers\Actions::class);
            }

            $grid->rowSelector()->check(function ($row) use ($ids) {
                return $row->id === $ids; // 默认选中state为1的行
            });

            $grid->column('id');

            $grid->column('path')->image();

            $grid->column('file_name', '名称');

        });
    }
}
