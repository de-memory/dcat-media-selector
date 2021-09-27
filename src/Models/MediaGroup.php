<?php

namespace DeMemory\DcatMediaSelector\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MediaGroup extends Model
{
    protected $table = 'media_group';

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    protected $fillable = ['admin_id', 'name'];

    public function getCreatedAtAttribute($key)
    {
        return date(Carbon::DEFAULT_TO_STRING_FORMAT, $key);
    }

    public function getUpdatedAtAttribute($key)
    {
        return date(Carbon::DEFAULT_TO_STRING_FORMAT, $key);
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = strtotime($value);
    }

    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = strtotime($value);
    }
}