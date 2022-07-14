<?php

namespace DeMemory\DcatMediaSelector\Http\Controllers;

use Dcat\Admin\Admin;
use DeMemory\DcatMediaSelector\Helpers\ApiResponse;
use DeMemory\DcatMediaSelector\Models\Media;
use DeMemory\DcatMediaSelector\Models\MediaGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController
{
    use ApiResponse;

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails())
            return $this->failed($validator->errors()->first());

        $model = MediaGroup::query()->where(['name' => $request->get('name')])->first();
        if ($model)
            return $this->failed('分组名已存在');

        $id = MediaGroup::query()->insertGetId(['admin_id' => Admin::user()->id, 'name' => $request->get('name'), 'created_at' => time()]);

        return $this->success($id);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'   => 'required',
            'name' => 'required'
        ]);

        if ($validator->fails())
            return $this->failed($validator->errors()->first());

        $model = MediaGroup::query()->find($request->get('id'));
        if (!$model)
            return $this->failed('分组不存在');

        $is = MediaGroup::query()->where('id', '<>', $request->get('id'))->where(['name' => $request->get('name')])->first();
        if ($is)
            return $this->failed('分组名已存在');

        $model->update(['name' => $request->get('name'), 'updated_at' => time()]);

        return $this->success(true);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails())
            return $this->failed($validator->errors()->first());

        $model = MediaGroup::query()->find($request->get('id'));
        if (!$model)
            return $this->failed('分组不存在');

        if ($model->delete()) {
            Media::query()->where('media_group_id', $request->get('id'))->update(['media_group_id' => 0, 'updated_at' => time()]);
        }

        return $this->success(true);
    }
}
