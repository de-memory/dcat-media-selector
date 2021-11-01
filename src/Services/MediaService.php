<?php

namespace DeMemory\DcatMediaSelector\Services;

use Dcat\Admin\Admin;
use DeMemory\DcatMediaSelector\Helpers\ApiResponse;
use DeMemory\DcatMediaSelector\Helpers\FileUtil;
use DeMemory\DcatMediaSelector\Models\Media;
use DeMemory\DcatMediaSelector\Models\MediaGroup;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class MediaService
{
    use ApiResponse;

    public function getMediaList($keyword, $type, $groupId, $sort, $order, $limit)
    {
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
                'id' => $value->id,
                'media_group_name' => empty($value->mediaGroup) ? '无' : $value->mediaGroup->name,
                'media_type' => $value->type,
                'path' => $value->path,
                'url' => Storage::disk(config('admin.upload.disk'))->url($value->path),
                'size' => FileUtil::getFormatBytes($value->size),
                'file_ext' => $value->file_ext,
                'name' => $value->file_name,
                'created_at' => $value->created_at,
            );
        }

        return json_encode(["total" => $list->total(), "data" => $dataList], JSON_UNESCAPED_UNICODE);
    }

    public function addGroup($name)
    {
        $id = MediaGroup::query()->insertGetId(['admin_id' => Admin::user()->id, 'name' => $name]);

        return $id;
    }

    public function move($groupId, $moveIds)
    {
        Media::query()->whereIn('id', explode(',', $moveIds))
            ->update(['media_group_id' => $groupId]);

        return true;
    }

    public function upload(UploadedFile $file, $mediaGroupId, $move)
    {
        $mime_type = $file->getMimeType();
        $type_info = $this->_getTypeInfoByMimeType($mime_type);

        //配置上传信息
        config([
            'filesystems.default' => config('admin.upload.disk', 'admin')
        ]);

        $disk = config('filesystems.default');


        $folder = $move->dir; //保存文件夹

        $file_name = $this->_getFileName($move, $file);

        $path = $file->storeAs($folder, $file_name);

        $getFileType = FileUtil::getFileType(Storage::disk(config('admin.upload.disk'))->url($path));

        $meta = $this->_getMeta($file, $getFileType, $type_info['suffix']);

        $data = [
            'admin_id' => Admin::user()->id,
            'media_group_id' => $mediaGroupId,
            'path' => $path,
            'file_name' => $file_name,
            'size' => $file->getSize(),
            'type' => $getFileType,
            'file_ext' => $file->getClientOriginalExtension(),
            'disk' => $disk,
            'meta' => json_encode($meta),
            'created_at' => time()
        ];

        return Media::query()->create($data);
    }

    public function delete($deleteIds, $deletePaths)
    {
        Media::query()->whereIn('id', $deleteIds)->delete();

        foreach ($deletePaths as $v) {
            $disk = Storage::disk(config('admin.upload.disk'));
            $exists = $disk->exists($v);
            if ($exists)
                $disk->delete($v);
        }

        return true;
    }

    private function _getMeta($file, $getFileType, $format)
    {
        switch ($getFileType) {
            case 'image':
                $manager = new ImageManager();
                $image = $manager->make($file);
                $meta = [
                    'format' => $format,
                    'suffix' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'width' => $image->getWidth(),
                    'height' => $image->getHeight()
                ];
                break;
            case 'video':
            case 'audio':
            case 'powerpoint':
            case 'code':
            case 'zip':
            case 'text':
                $meta = [
                    'format' => $format,
                    'suffix' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'width' => 0,
                    'height' => 0
                ];
                break;
            default :
                $meta = [
                    'format' => $format,
                    'suffix' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'width' => 0,
                    'height' => 0
                ];;
        }
        return $meta;
    }

    private function _getTypeInfoByMimeType($mt)
    {
        $arr = explode('/', $mt);
        return [
            'type' => $arr[0],
            'suffix' => $arr[1]
        ];
    }

    private function _getFileName($move, $file)
    {
        $fileName = $file->getClientOriginalName();
        if ($move->fileNameIsEncrypt)
            $fileName = md5(rand(1, 99999) . $file->getClientOriginalName()) . "." . $file->getClientOriginalExtension();

        return $fileName;
    }
}
