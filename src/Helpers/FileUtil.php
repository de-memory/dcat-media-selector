<?php

namespace DeMemory\DcatMediaSelector\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileUtil
{
    public static $fileTypes = [
        'image' => 'bmp|cgm|djv|djvu|gif|ico|ief|jp2|jpe|jpeg|jpg|mac|pbm|pct|pgm|pic|pict|png|pnm|pnt|pntg|ppm|qti|qtif|ras|rgb|svg|tif|tiff|wbmp|xbm|xpm|xwd',
        'audio' => 'mp3|wav|flac|3pg|aa|aac|ape|au|m4a|mpc|ogg',
        'video' => 'mkv|rmvb|flv|mp4|avi|wmv|rm|asf|mpeg',
        'powerpoint' => 'doc|dot|docx|dotx|docm|dotm|xls|xlt|xla|xlsx|xltx|xlsm|xltm|xlam|xlsb|ppt|pdf|pot|pps|ppa|pptx|potx|ppsx|ppam|pptm|potm|ppsm',
        'code' => 'php|js|java|python|ruby|go|c|cpp|sql|m|h|json|html|aspx',
        'zip' => 'zip|tar\.gz|rar|rpm',
        'text' => 'txt|pac|log|md',
    ];

    /**
     * @param $filePath | 文件绝对路径
     * @return bool|int|string
     */
    public static function getFileType($filePath)
    {
        foreach (self::$fileTypes as $type => $regex) {
            if (preg_match("/^($regex)$/i", self::getExtension($filePath)) !== 0) {
                return $type;
            }
        }
        return 'other';
    }

    public static function getFileSize($filePath)
    {
        return File::size($filePath);
    }

    public static function getBasename($filePath)
    {
        return File::basename($filePath);
    }

    public static function getExtension($filePath)
    {
        return File::extension($filePath);
    }

    public static function getFormatBytes($size)
    {
        $units = array(' B', ' KB', ' M', ' G', ' T');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];

    }

    public static function getFileUrl($disk, $path)
    {
        $disk = Storage::disk($disk);
        return $disk->url($path);
    }

}