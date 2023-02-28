<?php

namespace DeMemory\DcatMediaSelector;

use Dcat\Admin\Form\Field;
use DeMemory\DcatMediaSelector\Models\MediaGroup;
use Illuminate\Support\Facades\Storage;

class MediaSelector extends Field
{
    protected $view = 'dcat-media-selector::index';

    protected static $selectList = [
        'image'      => '图片',
        'video'      => '视频',
        'audio'      => '音频',
        'powerpoint' => '文稿',
        'code'       => '代码',
        'zip'        => '压缩包',
        'text'       => '文本选择',
        'other'      => '其它',
    ];

    /*
    |--------------------------------------------------------------------------
    | 弹框大小。默认宽60%,高98%
    |--------------------------------------------------------------------------
    */
    public function area(array $area = ['60%', '98%'])
    {
        return $this->options(['area' => $area]);
    }

    /*
    |--------------------------------------------------------------------------
    | 媒体选择数量。默认1
    |--------------------------------------------------------------------------
    */
    public function limit(int $limit = 1)
    {
        return $this->options(['limit' => $limit]);
    }

    /*
    |--------------------------------------------------------------------------
    | 媒体选择类型。默认所有
    |--------------------------------------------------------------------------
    | image            图片
    | video            视频
    | audio            音频
    | powerpoint       文稿选择
    | code             代码文件选择
    | zip              压缩包选择
    | text             文本选择
    | other            其他选择
    |
    */
    public function types(array $filetypes = [])
    {
        return $this->options(['types' => empty($filetypes) ? array_keys(self::$selectList) : $filetypes]);
    }

    /*
    |--------------------------------------------------------------------------
    | 媒体上传路径，媒体名称是否加密
    |--------------------------------------------------------------------------
    | 第一个参数，媒体上传路径。默认media
    | 第二个参数，媒体名称是否加密。默认true
    |
    | 注意：第二个参数如果是false，上传文件时，跟已上传的文件名称相同，会覆盖已上传的文件
    |
    */
    public function move(string $dir = 'media', bool $fileNameIsEncrypt = true)
    {
        return $this->options(['move' => json_encode(['dir' => $dir, 'fileNameIsEncrypt' => $fileNameIsEncrypt])]);
    }

    /*
    |--------------------------------------------------------------------------
    | 推动排序。默认true
    |--------------------------------------------------------------------------
    | true             启用排序
    | false            禁用排序
    */
    public function sortable(bool $sortable = true)
    {
        return $this->options(['sortable' => $sortable]);
    }

    public function render()
    {
        $limit = (isset($this->options['limit']) && !empty($this->options['limit'])) ? $this->options['limit'] : 1;

        $types = isset($this->options['types']) && !empty($this->options['types']) ? $this->options['types'] : array_keys(self::$selectList);

        $config = array_merge(
            [
                'rootPath' => Storage::disk(config('admin.upload.disk'))->url(''),
                'area'     => ['60%', '98%'],
                'limit'    => $limit,
                'move'     => json_encode(['dir' => 'media', 'fileNameIsEncrypt' => true]),
                'types'    => $types,
                'sortable' => true,
            ],
            $this->options
        );

        $config = json_encode($config);

        $selectList = [];
        foreach ($types as $v) {
            $selectList[$v] = self::$selectList[$v];
        }

        $grouplist = MediaGroup::query()->pluck('name', 'id');

        $this->addVariables([
            'elementClass' => $this->normalizeElementClass((string)$this->getElementName()),
            'limit'        => $limit,
            'config'       => $config,
            'grouplist'    => json_encode($grouplist),
            'selectList'   => json_encode($selectList),
        ]);

        return parent::render();
    }
}
