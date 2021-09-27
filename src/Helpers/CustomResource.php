<?php

namespace DeMemory\DcatMediaSelector\Helpers;

trait CustomResource
{
    protected $withoutFields = [];//当前模型字段设置
    protected $hide = true;

    protected function filterFields($array)
    {
        if (!$this->hide) {
            return collect($array)->only($this->withoutFields)->toArray();
        }
        return collect($array)->forget($this->withoutFields)->toArray();
    }
}