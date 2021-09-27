<?php


namespace DeMemory\DcatMediaSelector;


use App\Admin\Renderable\UserTable;
use Dcat\Admin\Form;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\CanLoadFields;
use Dcat\Admin\Form\Field\PlainInput;
use Dcat\Admin\Widgets\DialogTable;
use Dcat\Admin\Widgets\Modal;
use DeMemory\DcatMediaSelector\Models\MediaGroup;
use Illuminate\Support\Facades\Storage;

class MediaSelector extends Field
{
    protected $view = 'dcat-media-selector::index';

    protected $selectList = [
        'image' => '图片',
        'video' => '视频',
        'audio' => '音频',
        'powerpoint' => '文稿',
        'code' => '代码',
        'zip' => '压缩包',
        'text' => '文本选择',
        'other' => '其它',
    ];

    public function __construct($column, $arguments = [])
    {
        parent::__construct($column, $arguments);

        $this->selectList = DcatMediaSelectorServiceProvider::trans('media.type');
    }

    public function render()
    {

        // 文件存储的根目录
        $rootPath = Storage::disk(config('admin.upload.disk'))->url('');

        $length = isset($this->options['length']) && !empty($this->options['length']) ? $this->options['length'] : 1;

        $type = isset($this->options['type']) && !empty($this->options['type']) ? $this->options['type'] : 'blend';

        $locale = config('admin.lang');

        $lang = [
            'grid_items_selected' => __('admin.grid_items_selected'),
            'preview' => DcatMediaSelectorServiceProvider::trans('media.preview'),
            'name' => __('admin.name'),
            'type' => __('admin.scaffold.type'),
            'size' => __('admin.size'),
            'group_name' => DcatMediaSelectorServiceProvider::trans('media.group_name'),
            'file_suffix' => DcatMediaSelectorServiceProvider::trans('media.file_suffix'),
            'created_at' => __('admin.created_at'),
            'action' => __('admin.action'),
            'view' => __('admin.view'),
            'delete_confirm' => __('admin.delete_confirm'),
            'delete_succeeded' => __('admin.delete_succeeded'),
            'move_succeeded' => DcatMediaSelectorServiceProvider::trans('media.move_succeeded'),
            'create_succeeded' => DcatMediaSelectorServiceProvider::trans('media.create_succeeded'),
            'upload_succeeded' => __('admin.uploader.upload_succeeded'),
            'Q_TYPE_DENIED' => __('admin.uploader.Q_TYPE_DENIED'),
            'Q_TYPE_DENIED_1' => DcatMediaSelectorServiceProvider::trans('media.uploader.Q_TYPE_DENIED_1'),
            'Q_EXCEED_NUM_LIMIT' => __('admin.uploader.Q_EXCEED_NUM_LIMIT'),
            'Q_EXCEED_NUM_LIMIT_1' => DcatMediaSelectorServiceProvider::trans('media.uploader.Q_EXCEED_NUM_LIMIT_1')
        ];

        $grouplist = MediaGroup::query()->pluck('name', 'id');

        $lang = json_encode($lang);

        // 向视图添加变量
        $this->addVariables([
            'length' => $length,
            'rootPath' => $rootPath,
            'type' => $type,
            'grouplist' => $grouplist,
            'selectList' => $this->selectList,
            'elementClass' => $this->getDefaultElementClass()[0],
        ]);

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
                'rootPath' => $rootPath,
                'elementClass' => $this->getDefaultElementClass()[0],
                'storePath' => 'upload_files',
                'fileNameIsEncrypt' => true,
                'length' => $length,
                'move' => json_encode(['dir' => 'upload_files', 'fileNameIsEncrypt' => true]),
                'type' => $type,
                'sortable' => true,
            ],
            $this->options
        );

        $config = json_encode($config);


        $elementName = 'media_selector' . $this->getDefaultElementClass()[0];
        $script = <<<SCRIPT
if(!window.$elementName){
   window.$elementName = new MediaSelector($config,'$locale',$lang).init();
}
SCRIPT;


        $this->script = $script;
        return parent::render();
    }
}
