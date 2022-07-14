<?php

namespace DeMemory\DcatMediaSelector\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class MediaGroup extends Model
{
    protected $table = 'media_group';

    public $timestamps = false;

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    protected $fillable = ['admin_id', 'name'];

    public function getCreatedAtAttribute($key)
    {
        return date(CarbonInterface::DEFAULT_TO_STRING_FORMAT, $key);
    }

    public function getUpdatedAtAttribute($key)
    {
        return date(CarbonInterface::DEFAULT_TO_STRING_FORMAT, $key);
    }
}
