<?php

namespace DeMemory\DcatMediaSelector\Http;

use Intervention\Image\ImageManager;

class FileUtil
{
    public static array $fileTypes = [
        'image'      => 'bmp|jpg|png|tif|gif|pcx|tga|exif|fpx|svg|psd|cdr|pcd|dxf|ufo|eps|ai|raw|WMF|webp|avif|apng',
        'audio'      => 'mp3|wav|flac|3pg|aa|aac|ape|au|m4a|mpc|ogg',
        'video'      => 'mkv|rmvb|flv|mp4|avi|wmv|rm|asf|mpeg',
        'powerpoint' => 'doc|dot|docx|dotx|docm|dotm|xls|xlt|xla|xlsx|xltx|xlsm|xltm|xlam|xlsb|ppt|pdf|pot|pps|ppa|pptx|potx|ppsx|ppam|pptm|potm|ppsm',
        'code'       => 'php|js|java|python|ruby|go|c|cpp|sql|m|h|json|html|aspx',
        'zip'        => 'zip|tar\.gz|rar|rpm',
        'text'       => 'txt|pac|log|md',
    ];

    /**
     * 获取文件类型
     * @param $file | 文件流
     * @return bool|string
     */
    public static function verifyFileType($file): bool|string
    {
        $extension = \Illuminate\Support\Facades\File::guessExtension($file);
        foreach (static::$fileTypes as $type => $regex) {
            if (preg_match("/^($regex)$/i", $extension) !== 0)
                return $type;
        }
        return false;
    }

    /**
     * 文件大小转换
     * @param int $size
     * @return string
     */
    public static function formatBytes(int $size): string
    {
        $units = array(' B', ' KB', ' M', ' G', ' T');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];
    }

    public static function metaInfo($file)
    {
        switch (self::verifyFileType($file)) {
            case 'image':
                $manager = new ImageManager();
                $image   = $manager->make($file);
                $meta    = [
                    'width'  => $image->getWidth(),
                    'height' => $image->getHeight()
                ];
                break;
            case 'video':
            case 'audio':
            case 'powerpoint':
            case 'code':
            case 'zip':
            case 'text':
            default :
                $meta = [
                    'width'  => 0,
                    'height' => 0
                ];;
        }
        return $meta;
    }
}
