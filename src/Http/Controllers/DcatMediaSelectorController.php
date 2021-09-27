<?php

namespace DeMemory\DcatMediaSelector\Http\Controllers;

use DeMemory\DcatMediaSelector\Helpers\ApiResponse;
use DeMemory\DcatMediaSelector\Helpers\ResourcesMedia;
use DeMemory\DcatMediaSelector\Models\MediaGroup;
use DeMemory\DcatMediaSelector\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class DcatMediaSelectorController extends Controller
{
    use ApiResponse;

    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * @param Request $request
     * @return false|string
     *
     * @bodyParam keyword string required 关键字
     * @bodyParam type string required 类型
     * @bodyParam order string required 排序方式
     * @bodyParam sort string required 排序字段名
     * @bodyParam limit string required 分页大小
     */
    public function getMediaList(Request $request)
    {
        $data = $this->mediaService->getMediaList(
            $request->get('keyword'),
            $request->get('type'),
            $request->get('group_id'),
            $request->get('sort', 'id'),
            $request->get('order', 'desc'),
            $request->get('limit', '25')
        );
        return $data;
    }

    public function addGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails())
            return $this->failed($validator->errors()->first());

        $model = MediaGroup::query()->where(['name' => $request->get('name')])->first();
        if ($model)
            return $this->failed('分组名已存在');

        $data = $this->mediaService->addGroup(
            $request->get('name')
        );

        return $this->success($data);
    }

    public function move(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
            'move_ids' => 'required',
        ]);

        if ($validator->fails())
            return $this->failed($validator->errors()->first());

        $data = $this->mediaService->move(
            $request->get('group_id'),
            $request->get('move_ids')
        );

        return $this->success($data);
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
            'move' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }

        $media_obj = $this->mediaService->upload(
            $request->file('file'),
            $request->get('media_group_id',0),
            json_decode($request->get('move'))
        );

        $data = ResourcesMedia::make($media_obj);

        return $this->success($data);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delete_ids' => 'required|array',
            'delete_paths' => 'required|array'
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }

        $data = $this->mediaService->delete(
            $request->get('delete_ids'),
            $request->get('delete_paths')
        );

        return $this->success($data);
    }
}
