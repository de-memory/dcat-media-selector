<?php

namespace DeMemory\DcatMediaSelector\Helpers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ResourcesMedia extends JsonResource
{
    use CustomResource;

    public function toArray($request)
    {
        switch ($this->type) {
            case 'image':
            case 'video':
            case 'audio':
            case 'powerpoint':
            case 'code':
            case 'zip':
            case 'text':
            case 'other':
                return $this->_media();
                break;
            default:
                return [];
        }
    }

    private function _media()
    {
        $meta = json_decode($this->meta);
        return $this->filterFields([
            'id' => $this->id,
            'media_type' => $this->type,
            'name' => $this->file_name,
            'size' => FileUtil::getFormatBytes($this->size),
            'file_ext' => $this->file_ext,
            'path' => $this->path,
            'url' => FileUtil::getFileUrl($this->disk, $this->path),
            'meta' => $meta,
            'disk' => $this->disk,
            'width' => $meta->width,
            'height' => $meta->height,
        ]);
    }
}
