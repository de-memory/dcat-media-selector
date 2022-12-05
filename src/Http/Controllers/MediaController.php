<?php

namespace DeMemory\DcatMediaSelector\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use DeMemory\DcatMediaSelector\Helpers\ApiResponse;
use DeMemory\DcatMediaSelector\Helpers\FileUtil;
use DeMemory\DcatMediaSelector\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaController
{
    use ApiResponse;

    // 媒体列表
    public function list()
    {
        $keyword = request('keyword');
        $type    = request('type');
        $groupId = request('group_id');
        $sort    = request('sort', 'id');
        $order   = request('order', 'desc');
        $limit   = request('limit', 25);

        $query = Media::query()->where(function ($q) use ($keyword, $type, $groupId) {
            if (!empty($keyword)) {
                $q->where('file_name', 'like', '%' . $keyword . '%')
                    ->orWhere('file_ext', 'like', '%' . $keyword . '%');
            }
            if (!empty($type)) {
                $q->where('type', $type);
            }
            if (!empty($groupId)) {
                $q->where('media_group_id', $groupId);
            }
        });

        $list = $query->orderBy($sort, $order)->paginate($limit);

        $dataList = [];

        foreach ($list as $value) {
            $dataList[] = array(
                'id'               => $value->id,
                'media_group_name' => $value->mediaGroup->name ?? '无',
                'media_type'       => $value->type,
                'path'             => $value->path,
                'url'              => Storage::disk(config('admin.upload.disk'))->url($value->path),
                'size'             => FileUtil::formatBytes($value->size),
                'file_ext'         => $value->file_ext,
                'name'             => $value->file_name,
                'created_at'       => $value->created_at,
            );
        }

        return json_encode(["total" => $list->total(), "data" => $dataList], JSON_UNESCAPED_UNICODE);
    }

    // 上传
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
            'move' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }

        $file         = $request->file('file');
        $move         = json_decode($request->get('move'));
        $mediaGroupId = $request->get('group_id', 0);

        $dir               = $move->dir;
        $fileNameIsEncrypt = $move->fileNameIsEncrypt;

        $disk = Storage::disk(config('admin.upload.disk'));

        $newName = self::_getFileName($file, $fileNameIsEncrypt);

        $result = $disk->putFileAs($dir, $file, $newName);

        $path = "$dir/$newName";

        $type = FileUtil::verifyFileType($file);

        if ($result) {
            $data = [
                'admin_id'       => Admin::user()->id ?? 0,
                'media_group_id' => $mediaGroupId,
                'path'           => $path,
                'file_name'      => $newName,
                'size'           => $file->getSize(),
                'type'           => $type,
                'file_ext'       => $file->getClientOriginalExtension(),
                'disk'           => config('admin.upload.disk'),
                'meta'           => json_encode(FileUtil::metaInfo($file)),
                'created_at'     => time()
            ];
            Media::query()->insert($data);
        }

        return $result
            ? $this->success(['name' => Helper::basename($path), 'path' => $path, 'media_type' => $type, 'url' => $disk->url($path)])
            : $this->failed('上传失败');
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delete_ids'   => 'required|array',
            'delete_paths' => 'required|array'
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }

        Media::query()->whereIn('id', $request->get('delete_ids'))->delete();

        foreach ($request->get('delete_paths') as $v) {
            $disk   = Storage::disk(config('admin.upload.disk'));
            $exists = $disk->exists($v);
            if ($exists)
                $disk->delete($v);
        }

        return $this->success(true);
    }

    public function move(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
            'move_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }

        Media::query()->whereIn('id', explode(',', $request->get('move_ids')))
            ->update(['media_group_id' => $request->get('group_id')]);

        return $this->success(true);
    }

    private function _getFileName($file, $isEncrypt)
    {
        $fileName = $file->getClientOriginalName();
        if ($isEncrypt)
            $fileName = md5($file->getClientOriginalName() . uniqid()) . '.' . $file->getClientOriginalExtension();

        return $fileName;
    }
}
