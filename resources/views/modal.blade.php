<div class="modal fade {{$elementClass}}modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{$label}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-3 col-sm-2 border-right">
                        <button type="button"
                                class="btn btn-block btn-outline-success mb-1 {{$elementClass}}add_media_group">
                            <i class="feather icon-plus"></i>
                            {{DeMemory\DcatMediaSelector\DcatMediaSelectorServiceProvider::trans('media.add_media_group')}}
                        </button>
                        <div class="list-group list-group-flush pre-scrollable1 {{$elementClass}}media_group">
                            <a class="list-group-item list-group-item-action active" data-toggle="list" data-id="0"
                               href="javascript:;">{{__('admin.all')}}
                            </a>
                            @foreach($grouplist as $select => $option)
                                <a class="list-group-item list-group-item-action" data-toggle="list"
                                   data-id="{{$select}}" href="javascript:;">{{$option}}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-9 col-sm-10">
                        <div class="{{$elementClass}}form border-bottom mb-1" style="display: none">
                            <div class="row mt-1 mb-0">
                                <div class="filter-input col-sm-4">
                                    <div class="form-group">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white text-capitalize">
                                                    <b>{{__('admin.name')}}</b>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control " name="{{$elementClass}}name"
                                                   placeholder="{{__('admin.name')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-input col-sm-4">
                                    <div class="form-group">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white text-capitalize">
                                                    <b>{{__('admin.scaffold.type')}}</b>
                                                </span>
                                            </div>
                                            <select class="form-control {{$input = $elementClass}}type"
                                                    style="width: 100%;" tabindex="-1" aria-hidden="true"
                                                    data-placeholder="{{__('admin.scaffold.type')}}" {{$type != 'blend'? 'disabled':''}}>
                                                <option value=""></option>
                                                @foreach($selectList as $select => $option)
                                                    <option value="{{$select}}" {{ $select == old($input, $type) ?'selected':'' }}>{{$option}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group ml-1 mb-1" style="height: fit-content;margin-right: 10px">
                                    <button type="button"
                                            class="btn btn-primary grid-refresh btn-mini btn-sm btn-outline {{$elementClass}}search">
                                        <i class="feather icon-search"></i> {{__('admin.search')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="{{$elementClass}}toolbar mb-1">
                            <div class="btn-group dropdown grid-select-all-btn {{$elementClass}}more"
                                 style="display: none">
                                <button type="button" class="btn btn-white dropdown-toggle btn-mini btn-outline"
                                        data-toggle="dropdown">
                                    <span class="d-none d-sm-inline selected">{{__('admin.selected_options')}}</span>
                                    <span class="caret"></span>
                                    <span class="sr-only"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu" style="left: 0px; right: inherit;">
                                    <li class="dropdown-item">
                                        <a class="{{$elementClass}}batch_delete">
                                            <i class="feather icon-trash"></i> {{__('admin.delete')}}
                                        </a>
                                    </li>
                                    <li class="dropdown-item">
                                        <a class="{{$elementClass}}batch_mobile">
                                            <i class="fa fa-arrows-h"></i>
                                            {{DeMemory\DcatMediaSelector\DcatMediaSelectorServiceProvider::trans('media.move')}}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn btn-outline-custom {{$elementClass}}refresh">
                                <i class="feather icon-refresh-cw"></i> {{__('admin.refresh')}}
                            </button>
                            <button type="button" class="btn btn btn-outline-custom {{$elementClass}}filter">
                                <i class="feather icon-filter"></i> {{__('admin.filter')}}
                            </button>
                            @if($length >1)
                                <button type="button" class="btn btn btn-outline-custom {{$elementClass}}choose">
                                    <i class="fa fa-check"></i> {{__('admin.choose')}}
                                </button>
                            @endif
                            <span style="position: relative;">
                                <label class="btn btn-outline-success">
                                    <i class="fa fa-upload"></i> {{__('admin.upload')}}
                                    <span id="{{$elementClass}}modal_percent"></span>
                                    <input type="file" id="{{$elementClass}}modal_upload" multiple
                                           style="display: none;">
                                 </label>
                            </span>
                        </div>
                        <div class="pre-scrollable1">
                            <table class="{{$elementClass}}table" data-locale="{{config('app.locale')}}"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="{{$elementClass}}layui-inline" style="display: none">
    <div style="margin: 0 auto; width: 200px; height: 80px; text-align: center; line-height: 80px;">
        <select id="{{$elementClass}}group_id" style="width: 100%;height: 35px">
            @foreach($grouplist as $select => $option)
                <option value="{{$select}}">{{$option}}</option>
            @endforeach
        </select>
    </div>
</div>

<script init="{{ $selector }}"
        require="@select-table,@select2?lang={{ config('app.locale') === 'en' ? '' : str_replace('_', '-', config('app.locale')) }},@de-memory.dcat-media-selector">


    var langs = {!! $lang !!}, // 错误信息
        config = {!! $config !!}, // 上传配置
        locale = '{{ $locale }}', // 语言
        lang = Dcat.Translator(langs, locale);// 错误翻译;

    var btselectarr = [], // 选中行id
        selectedGroupId = 0; // 选中分组id

    var dropdownToggleButtonClass = $('.' + config.elementClass + 'more'); // 模态框更多操作按钮元素

    // 多媒体并且开启排序
    if (config.length > 1 && config.sortable) sortable(config);

    $('.' + config.elementClass + 'type').select2({language: locale, allowClear: true, minimumInputLength: 0});

    // Form Input媒体上传事件
    $('.' + config.elementClass + 'form_upload').change(function (e) {
        if ($(this).val() !== '') {
            var files = $(this)[0].files;
            var isEnd = true;
            if (config.length > 1 && files && (getFileNumber(config) + files.length) > config.length) {
                onError(lang, 'Q_EXCEED_NUM_LIMIT_1', {num: config.length});
                // 操作完成后，使用如下代码，将其值置位空，则可以解决再次触发change事件时失效的问题
                e.target.value = '';
                return false;
            }

            $.each(files, function (i, field) {
                var suffix = field.name.substring(field.name.lastIndexOf('.') + 1);
                var fileType = getFileType(suffix);
                if (config.type !== 'blend' && config.type !== fileType) {
                    onError(lang, 'Q_TYPE_DENIED');
                    isEnd = false;
                    return false;
                }
            });
            if (isEnd) {
                mediaUpload(this, selectedGroupId, config, langs, 'form');
            }
        }
        // 操作完成后，使用如下代码，将其值置位空，则可以解决再次触发change事件时失效的问题
        e.target.value = '';
    });

    // Form媒体选择事件
    $('.' + config.elementClass + 'get_media_list').click(function () {

        dropdownToggleButtonClass.hide(); // 隐藏更多操作

        $('.' + config.elementClass + 'table').bootstrapTable('destroy').bootstrapTable({
            url: '/admin/media-selector/media-list',         //请求后台的URL（*）
            method: 'get',                      //请求方式（*）
            toolbar: '.' + config.elementClass + 'toolbar',                //工具按钮用哪个容器
            dataField: 'data',
            classes: 'table custom-data-table data-table',
            striped: true,                      //是否显示行间隔色
            cache: false,                       //是否使用缓存，默认为true，所以一般情况下需要设置一下这个属性（*）
            pagination: true,                   //是否显示分页（*）
            sortable: true,                     //是否启用排序
            sortOrder: 'desc',                   //排序方式
            sidePagination: 'server',           //分页方式：client客户端分页，server服务端分页（*）
            pageNumber: 1,                       //初始化加载第一页，默认第一页
            pageSize: 20,                       //每页的记录行数（*）
            pageList: [10, 25, 50, 100],        //可供选择的每页的行数（*）
            minimumCountColumns: 2,             //最少允许的列数
            clickToSelect: false,                //是否启用点击选中行
            uniqueId: 'id',                     //每一行的唯一标识，一般为主键列
            columns: [
                {checkbox: true},
                {field: 'id', title: 'ID', sortable: true, visible: false},
                {
                    title: langs.preview, formatter: function (value, row) {
                        var html = '<a href="javascript:;" title="' + langs.view + '">';
                        if (row.media_type === 'image') {
                            html += '<img class="img-thumbnail modal-img-thumbnail " src="' + row.url + '" data-action="mediaselector-preview-image" data-url="' + row.url + '">';
                        } else if (row.media_type === 'video') {
                            html += '<video class="img-thumbnail modal-img-thumbnail" src="' + row.url + '" data-action="mediaselector-preview-video" data-url="' + row.url + '"> </video>';
                        } else if (row.media_type === 'audio') {
                            html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-audio-o my_fa" data-action="mediaselector-preview-audio" data-url="' + row.url + '"></i></div>';
                        } else if (row.media_type === 'powerpoint') {
                            html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-word-o my_fa" data-action="mediaselector-preview-powerpoint" data-url="' + row.url + '"></i></div>';
                        } else if (row.media_type === 'code') {
                            html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-code-o my_fa" data-action="mediaselector-preview-code" data-url="' + row.url + '"></i></div>';
                        } else if (row.media_type === 'zip') {
                            html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-zip-o my_fa" data-action="mediaselector-preview-zip" data-url="' + row.url + '"></i></div>';
                        } else if (row.media_type === 'text') {
                            html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-text-o my_fa" data-action="mediaselector-preview-text" data-url="' + row.url + '"></i></div>';
                        } else if (row.media_type === 'other') {
                            html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file my_fa" data-action="mediaselector-preview-other" data-url="' + row.url + '"></i></div>';
                        }
                        html += '</a>';
                        return html;
                    }
                },
                {field: 'name', title: langs.name},
                {field: 'media_group_name', title: langs.group_name},
                {field: 'media_type', title: langs.type},
                {field: 'size', title: langs.size},
                {field: 'file_ext', title: langs.file_suffix},
                {field: 'created_at', title: langs.created_at, sortable: true},
                {
                    field: 'operate',
                    title: langs.action,
                    width: '50%',
                    events: {
                        'click .chooseone': function (e, value, row) {
                            if (config.type !== 'blend') {
                                if (row.media_type !== config.type) {
                                    onError(lang, 'Q_TYPE_DENIED_1');
                                    return false;
                                }
                            }
                            if (config.length > 1 && (getFileNumber(config) + 1) > config.length) {
                                onError(lang, 'Q_EXCEED_NUM_LIMIT_1', {num: config.length});
                                return false;
                            }
                            fileDisplay({data: row}, config, langs);
                            $('.' + config.elementClass + 'modal').modal('hide');
                        },
                    },
                    formatter: operateFormatter()
                }
            ],
            queryParams: function (params) {
                return {
                    page: (params.limit + params.offset) / params.limit,  // 分页索引
                    limit: params.limit,   // 分页大小
                    sort: params.sort, // 排序字段名
                    order: params.order, // 排序方式:asc正序,desc倒序
                    keyword: $('input[name="' + config.elementClass + 'name"]').val(),
                    type: $('.' + config.elementClass + 'type').select2('val'),
                    group_id: selectedGroupId,
                };
            },
            onLoadSuccess: function () {
                return Dcat.loading(false);
            },
            onCheckAll: function (row) { // 点击全选框时触发的操作
                if (row.length) {
                    $.each(row, function (i, field) {
                        btselectarr.push(field.id);
                    });
                    var grid_items_selected = langs.grid_items_selected;
                    dropdownToggleButtonClass.find(' .selected').html(grid_items_selected.replace('{n}', row.length));
                    dropdownToggleButtonClass.show();
                }
            },
            onUncheckAll: function () { // 取消所有
                btselectarr = [];
                dropdownToggleButtonClass.hide();
            },
            onCheck: function (row) { // 点击每一个单选框时触发的操作
                btselectarr.push(row.id);
                if (btselectarr.length >= 1) {
                    var grid_items_selected = langs.grid_items_selected;
                    dropdownToggleButtonClass.find(' .selected').html(grid_items_selected.replace('{n}', btselectarr.length));
                    dropdownToggleButtonClass.show();
                }
            },
            onUncheck: function (row) { // 取消每一个单选框时对应的操作
                var index = btselectarr.indexOf(row.id);
                if (index > -1) {
                    btselectarr.splice(index, 1);
                }
                var grid_items_selected = langs.grid_items_selected;
                dropdownToggleButtonClass.find(' .selected').html(grid_items_selected.replace('{n}', btselectarr.length));
                if (btselectarr.length <= 0) {
                    dropdownToggleButtonClass.hide();
                }
            },
        });
    });

    // 模态框"新建分组"按钮点击事件
    $('.' + config.elementClass + 'add_media_group').click(function () {
        layer.prompt({
            title: langs.group_name,
            maxmin: false,
            move: false,
            shade: 0.2,
        }, function (value, index) {
            $.ajax({
                type: 'POST',
                url: '/admin/media-selector/add-group',
                data: {name: value},
                datatype: 'jsonp',
                //成功返回之后调用的函数
                success: function (data) {
                    $('.' + config.elementClass + 'media_group').append('<a class="list-group-item list-group-item-action" data-toggle="list" data-id="' + data.data + '" href="javascript:;">' + value + '</a>');
                    Dcat.success(langs.create_succeeded);
                },
                error: function (XmlHttpRequest) {
                    Dcat.error(XmlHttpRequest.responseJSON.message);
                }
            });
            layer.close(index);
        });
    });

    // 模态框"点击分组"按钮点击事件
    $('.' + config.elementClass + 'media_group a').click(function () {
        selectedGroupId = $(this).attr('data-id');
        dropdownToggleButtonClass.hide();
        bootstrapTableRefresh(config);
    });

    // 模态框"批量删除"按钮点击事件
    $('.' + config.elementClass + 'batch_delete').click(function () {
        Dcat.confirm(langs.delete_confirm, null, function () {
            Dcat.loading();
            var deleteId = [], deletePaths = [];
            $.each(bootstrapTableGetSelections(config), function (i, field) {
                deleteId.push(field.id);
                deletePaths.push(field.path);
            });
            $.ajax({
                type: 'POST',
                url: '/admin/media-selector/media-delete',
                data: {delete_ids: deleteId, delete_paths: deletePaths},
                datatype: 'jsonp',
                success: function () {
                    Dcat.success(langs.delete_succeeded);
                    btselectarr = [];
                    dropdownToggleButtonClass.hide();
                    bootstrapTableRefresh(config);
                    Dcat.loading(false);
                },
                error: function (XmlHttpRequest) {
                    Dcat.loading(false);
                    Dcat.error(XmlHttpRequest.responseJSON.message);
                }
            });
        });
    });

    // 模态框"批量移动"按钮点击事件
    $('.' + config.elementClass + 'batch_mobile').click(function () {
        var row = bootstrapTableGetSelections(config);
        var moveId = [];
        $.each(row, function (i, field) {
            moveId.push(field.id);
        });
        layer.open({
            type: 1,
            shade: 0.2,
            shadeClose: true,
            maxmin: false,
            move: false,
            area: ['275px', '200px'],
            title: '移动',
            btn: ['确定'],
            content: $('.' + config.elementClass + 'layui-inline').html(),
            yes: function (index, value) {
                Dcat.loading();
                var groupId = $('#' + config.elementClass + 'group_id').val();
                $.ajax({
                    type: 'POST',
                    url: '/admin/media-selector/media-move',
                    data: {group_id: groupId, move_ids: moveId.join(',')},
                    datatype: 'jsonp',
                    success: function () {
                        Dcat.success(langs.move_succeeded);
                        Dcat.loading(false);
                        btselectarr = [];
                        dropdownToggleButtonClass.hide();
                        bootstrapTableRefresh(config);
                    },
                    error: function (XmlHttpRequest) {
                        Dcat.loading(false);
                        Dcat.error(XmlHttpRequest.responseJSON.message);
                    }
                });
                layer.close(index);
            }
        });
    });

    // 模态框"刷新、搜索"按钮点击事件
    $('.' + config.elementClass + 'refresh, .' + config.elementClass + 'search').click(function () {
        bootstrapTableRefresh(config);
    });

    // 模态框"筛选"按钮点击事件
    $('.' + config.elementClass + 'filter').click(function () {
        $('.' + config.elementClass + 'form').toggle();
    });

    // 模态框"选择"按钮点击事件
    $('.' + config.elementClass + 'choose').click(function () {
        var row = bootstrapTableGetSelections(config);

        if (row.length === 0) {
            $('.' + config.elementClass + 'modal').modal('hide');
        }
        if (config.length > 1 && (getFileNumber(config) + row.length) > config.length) {
            onError(lang, 'Q_EXCEED_NUM_LIMIT_1', {num: config.length});
            return false;
        }
        var result = true;
        $.each(row, function (i, field) {
            if (config.type !== 'blend') {
                if (field.media_type !== config.type) {
                    result = false;
                    return false;
                }
            }
        });
        if (!result) {
            onError(lang, 'Q_TYPE_DENIED_1');
            return false;
        }
        $.each(row, function (i, field) {
            fileDisplay({data: field}, config, langs);
            $('.' + config.elementClass + 'modal').modal('hide');
        });
    });

    // Modal媒体上传事件
    $('#' + config.elementClass + 'modal_upload').change(function (e) {
        if ($(this).val() !== '') {
            mediaUpload(this, selectedGroupId, config, langs, 'modal');
        }
        // 操作完成后，使用如下代码，将其值置位空，则可以解决再次触发change事件时失效的问题
        e.target.value = '';
    });

    // 预览powerpoint
    $(document).off('click.mediaselector', '[data-action="mediaselector-preview-powerpoint"]')
        .on('click.mediaselector', '[data-action="mediaselector-preview-powerpoint"]', function () {
            previewPowerpoint($(this).data('url'), config);
        });

    // 预览视频
    $(document).off('click.mediaselector', '[data-action="mediaselector-preview-video"]')
        .on('click.mediaselector', '[data-action="mediaselector-preview-video"]', function () {
            previewVideo($(this).data('url'), lang);
        });

    // 预览音频
    $(document).off('click.mediaselector', '[data-action="mediaselector-preview-audio"]')
        .on('click.mediaselector', '[data-action="mediaselector-preview-audio"]', function () {
            previewAudio($(this).data('url'), lang);
        });
</script>