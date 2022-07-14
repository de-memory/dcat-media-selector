<?php

namespace DeMemory\DcatMediaSelector\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasDateTimeFormatter;

    protected $fillable = ['admin_id', 'media_group_id', 'path', 'file_name', 'size', 'type', 'file_ext', 'disk', 'meta', 'created_at'];

    public $timestamps = false;

    public function mediaGroup()
    {
        return $this->belongsTo(MediaGroup::class);
    }
}
