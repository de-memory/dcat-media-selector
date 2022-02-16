<?php


namespace DeMemory\DcatMediaSelector;

use Dcat\Admin\Form\Field;
use DeMemory\DcatMediaSelector\Models\MediaGroup;
use Illuminate\Support\Facades\Storage;

class MediaSelector extends Field
{
    protected $view = 'dcat-media-selector::index';

    protected $selectList;

    protected $useMicrosoftPreview = false;

    public function __construct($column, $arguments = [])
    {
        parent::__construct($column, $arguments);

        $this->selectList = DcatMediaSelectorServiceProvider::trans('media.type') ?: [
            'image'      => '图片',
            'video'      => '视频',
            'audio'      => '音频',
            'powerpoint' => '文稿',
            'code'       => '代码',
            'zip'        => '压缩包',
            'text'       => '文本选择',
            'other'      => '其它',
        ];
    }

    public function limit(int $limit = 1)
    {
        return $this->options(['length' => $limit]);
    }

    public function move(string $dir, bool $fileNameIsEncrypt = true)
    {
        return $this->options(['move' => json_encode(['dir' => $dir, 'fileNameIsEncrypt' => $fileNameIsEncrypt])]);
    }

    /**
     *--------------------------------------------------------------------------
     * 媒体选择类型。默认blend
     *--------------------------------------------------------------------------
     * blend            混合选择
     * image            图片选择
     * video            视频选择
     * audio            音频选择
     * powerpoint       文稿选择
     * code             代码文件选择
     * zip              压缩包选择
     * text             文本选择
     * other            其他选择
     */
    public function fileType(string $filetype)
    {
        if ($filetype !== 'blend' && !in_array($filetype, array_keys($this->selectList))) {
            $filetype = 'blend';
        }
        return $this->options(['type' => $filetype]);
    }

    public function sortable(bool $sortable = true)
    {
        return $this->options(['sortable' => $sortable]);
    }

    public function useMicrosoftPreview(bool $useMicrosoftPreview = true)
    {
        $this->useMicrosoftPreview = $useMicrosoftPreview;

        return $this;
    }

    public function render()
    {

        // 文件存储的根目录
        $rootPath = Storage::disk(config('admin.upload.disk'))->url('');

        $length = isset($this->options['length']) && !empty($this->options['length']) ? $this->options['length'] : 1;

        $type = isset($this->options['type']) && !empty($this->options['type']) ? $this->options['type'] : 'blend';

        $locale = config('admin.lang');

        $elementClass = $this->getDefaultElementClass()[0];

        $lang = [
            'grid_items_selected'       => __('admin.grid_items_selected'),
            'preview'                   => DcatMediaSelectorServiceProvider::trans('media.preview'),
            'name'                      => __('admin.name'),
            'type'                      => __('admin.scaffold.type'),
            'size'                      => __('admin.size'),
            'group_name'                => DcatMediaSelectorServiceProvider::trans('media.group_name'),
            'file_suffix'               => DcatMediaSelectorServiceProvider::trans('media.file_suffix'),
            'created_at'                => __('admin.created_at'),
            'action'                    => __('admin.action'),
            'view'                      => __('admin.view'),
            'delete_confirm'            => __('admin.delete_confirm'),
            'delete_succeeded'          => __('admin.delete_succeeded'),
            'move_succeeded'            => DcatMediaSelectorServiceProvider::trans('media.move_succeeded'),
            'create_succeeded'          => DcatMediaSelectorServiceProvider::trans('media.create_succeeded'),
            'upload_succeeded'          => DcatMediaSelectorServiceProvider::trans('media.uploader.upload_succeeded'),
            'Q_TYPE_DENIED'             => __('admin.uploader.Q_TYPE_DENIED'),
            'Q_TYPE_DENIED_1'           => DcatMediaSelectorServiceProvider::trans('media.uploader.Q_TYPE_DENIED_1'),
            'Q_EXCEED_NUM_LIMIT'        => __('admin.uploader.Q_EXCEED_NUM_LIMIT'),
            'Q_EXCEED_NUM_LIMIT_1'      => DcatMediaSelectorServiceProvider::trans('media.uploader.Q_EXCEED_NUM_LIMIT_1'),
            'Q_EXCEED_SIZE_LIMIT'       => DcatMediaSelectorServiceProvider::trans('media.uploader.Q_EXCEED_SIZE_LIMIT'),
            'preview_video_unsupported' => DcatMediaSelectorServiceProvider::trans('media.preview_video.unsupported'),
            'preview_audio_unsupported' => DcatMediaSelectorServiceProvider::trans('media.preview_audio.unsupported'),
        ];

        $grouplist = MediaGroup::query()->pluck('name', 'id');

        $lang = json_encode($lang);

        /**
         * elementLabel | 元素Name
         * storePath | 媒体存储的路径
         * fileNameIsEncrypt | 媒体名是否加密
         * length | 媒体选择数量
         * type | 媒体选择类型
         *      blend            混合选择
         *      image            图片选择
         *      video            视频选择
         *      audio            音频选择
         *      powerpoint       文稿选择
         *      code             代码文件选择
         *      zip              压缩包选择
         *      text             文本选择
         *      other            其他选择
         * sortable | 媒体拖动排序
         * lang | 翻译
         *
         */
        $config = array_merge(
            [
                'rootPath'            => $rootPath,
                'elementClass'        => $elementClass,
                'storePath'           => 'upload_files',
                'fileNameIsEncrypt'   => true,
                'length'              => $length,
                'move'                => json_encode(['dir' => 'upload_files', 'fileNameIsEncrypt' => true]),
                'type'                => $type,
                'sortable'            => true,
                'useMicrosoftPreview' => $this->useMicrosoftPreview,
            ],
            $this->options
        );

        $config = json_encode($config);

        // 向视图添加变量
        $this->addVariables([
            'length'       => $length,
            'rootPath'     => $rootPath,
            'type'         => $type,
            'grouplist'    => $grouplist,
            'selectList'   => $this->selectList,
            'elementClass' => $elementClass,
            'config'       => $config,
            'locale'       => $locale,
            'lang'         => $lang,
        ]);

        return parent::render();
    }
}
