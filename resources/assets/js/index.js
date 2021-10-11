(function () {
        /**
         * @param config object | 配置
         * @param locale string | 语言
         * @param lang object | 翻译
         * @constructor
         */
        function MediaSelector(config, locale, lang) {
            this.config = config; // 上传配置
            this.locale = locale; // 语言
            this.langs = lang; // 错误信息
            this.lang = Dcat.Translator(lang, locale); // 错误翻译
            this.btselectarr = []; // 选中行id
            this.selectedGroupId = 0; // 选中分组id
        }
        // 参数化
        MediaSelector.prototype.init = function () {
            var _this = this;
            _this.sortable();
            $('.' + _this.config.elementClass + 'type').select2({
                language: _this.locale,
                allowClear: true,
                minimumInputLength: 0
            });
            // 绑定Form Input媒体上传事件
            $('body').delegate('.' + _this.config.elementClass + 'form_upload', 'change', function (e) {
                if ($(this).val() !== '') {
                    var files = $(this)[0].files;
                    var isEnd = true;
                    if (_this.config.length > 1 && files && (_this.getFileNumber() + files.length) > _this.config.length) {
                        _this.onError('Q_EXCEED_NUM_LIMIT_1', {num: _this.config.length});
                        // 操作完成后，使用如下代码，将其值置位空，则可以解决再次触发change事件时失效的问题
                        e.target.value = '';
                        return false;
                    }
                    $.each(files, function (i, field) {
                        var suffix = field.name.substring(field.name.lastIndexOf('.') + 1);
                        var fileType = _this.getFileType(suffix);
                        if (_this.config.type !== 'blend' && _this.config.type !== fileType) {
                            _this.onError('Q_TYPE_DENIED');
                            isEnd = false;
                            return false;
                        }
                    });
                    if (isEnd) {
                        _this.mediaUpload(this, 'form');
                    }
                }
                // 操作完成后，使用如下代码，将其值置位空，则可以解决再次触发change事件时失效的问题
                e.target.value = '';
            });
            // 绑定Form媒体选择事件
            $('body').delegate('.' + _this.config.elementClass + 'get_media_list', 'click', function () {
                $('.' + _this.config.elementClass + 'table').bootstrapTable('destroy');
                $('.' + _this.config.elementClass + 'more').hide();
                _this.getMediaList();
            });
            // 绑定模态框"新建分组"按钮点击事件
            $('body').delegate('.' + _this.config.elementClass + 'add_media_group', 'click', function () {
                layer.prompt({
                    title: _this.langs.group_name,
                    maxmin: false,
                    move: false,
                }, function (value, index) {
                    $.ajax({
                        type: 'POST',
                        url: '/admin/media-selector/add-group',
                        data: {name: value},
                        datatype: 'jsonp',
                        //成功返回之后调用的函数
                        success: function (data) {
                            $('.' + _this.config.elementClass + 'media_group').append('<a class="list-group-item list-group-item-action" data-toggle="list" data-id="' + data.data + '" href="javascript:;">' + value + '</a>');
                            Dcat.success(_this.langs.create_succeeded);
                        },
                        error: function (XmlHttpRequest) {
                            Dcat.error(XmlHttpRequest.responseJSON.message);
                        }
                    });
                    layer.close(index);
                });
            });
            // 绑定模态框"点击分组"按钮点击事件
            $('body').delegate('.' + _this.config.elementClass + 'media_group a', 'click', function () {
                layer.closeAll();
                _this.selectedGroupId = $(this).attr('data-id');
                $('.' + _this.config.elementClass + 'more').hide();
                _this.bootstrapTableRefresh();
            });
            // 绑定模态框"批量删除"按钮点击事件
            $('body').delegate('.' + _this.config.elementClass + 'batch_delete', 'click', function () {
                Dcat.confirm(_this.langs.delete_confirm, null, function () {
                    Dcat.loading();
                    var deleteId = [], deletePaths = [];
                    $.each(_this.bootstrapTableGetSelections(), function (i, field) {
                        deleteId.push(field.id);
                        deletePaths.push(field.path);
                    });
                    $.ajax({
                        type: 'POST',
                        url: '/admin/media-selector/media-delete',
                        data: {delete_ids: deleteId, delete_paths: deletePaths},
                        datatype: 'jsonp',
                        success: function () {
                            Dcat.success(_this.langs.delete_succeeded);
                            layer.closeAll();
                            _this.btselectarr = [];
                            $('.' + _this.config.elementClass + 'more').hide();
                            _this.bootstrapTableRefresh();
                            Dcat.loading(false);
                        },
                        error: function (XmlHttpRequest) {
                            layer.closeAll();
                            Dcat.loading(false);
                            Dcat.error(XmlHttpRequest.responseJSON.message);
                        }
                    });
                });
            });
            // 绑定模态框"批量移动"按钮点击事件
            $('body').delegate('.' + _this.config.elementClass + 'batch_mobile', 'click', function () {
                var row = _this.bootstrapTableGetSelections();
                var moveId = [];
                $.each(row, function (i, field) {
                    moveId.push(field.id);
                });
                layer.open({
                    type: 1,
                    maxmin: false,
                    move: false,
                    area: ['275px', '200px'],
                    title: '移动',
                    btn: ['确定'],
                    content: $('.' + _this.config.elementClass + 'layui-inline'),
                    yes: function () {
                        Dcat.loading();
                        var groupId = $('#' + _this.config.elementClass + 'group_id').val();
                        $.ajax({
                            type: 'POST',
                            url: '/admin/media-selector/media-move',
                            data: {group_id: groupId, move_ids: moveId.join(',')},
                            datatype: 'jsonp',
                            success: function () {
                                Dcat.success(_this.langs.move_succeeded);
                                layer.closeAll();
                                Dcat.loading(false);
                                _this.btselectarr = [];
                                $('.' + _this.config.elementClass + 'more').hide();
                                _this.bootstrapTableRefresh();
                            },
                            error: function (XmlHttpRequest) {
                                layer.closeAll();
                                Dcat.loading(false);
                                Dcat.error(XmlHttpRequest.responseJSON.message);
                            }
                        });
                    }
                });
            });
            // 绑定模态框"刷新、搜索"按钮点击事件
            $('body').delegate('.' + _this.config.elementClass + 'refresh, .' + _this.config.elementClass + 'search', 'click', function () {
                _this.bootstrapTableRefresh();
            });
            // 绑定模态框"筛选"按钮点击事件
            $('body').delegate('.' + _this.config.elementClass + 'filter', 'click', function () {
                $('.' + _this.config.elementClass + 'form').toggle();
            });
            // 绑定模态框"选择"按钮点击事件
            $('body').delegate('.' + _this.config.elementClass + 'choose', 'click', function () {
                var row = _this.bootstrapTableGetSelections();
                console.log(row);
                if (row.length === 0) {
                    $('.' + _this.config.elementClass + 'modal').modal('hide');
                }
                if (_this.config.length > 1 && (_this.getFileNumber() + row.length) > _this.config.length) {
                    _this.onError('Q_EXCEED_NUM_LIMIT_1', {num: _this.config.length});
                    return false;
                }
                var result = true;
                $.each(row, function (i, field) {
                    if (_this.config.type !== 'blend') {
                        if (field.media_type !== _this.config.type) {
                            result = false;
                            return false;
                        }
                    }
                });
                if (!result) {
                    _this.onError('Q_TYPE_DENIED_1');
                    return false;
                }
                $.each(row, function (i, field) {
                    _this.fileDisplay({data: field});
                    $('.' + _this.config.elementClass + 'modal').modal('hide');
                });
            });
            // 绑定Modal媒体上传事件
            $('body').delegate('#' + _this.config.elementClass + 'modal_upload', 'change', function (e) {
                if ($(this).val() !== '') {
                    _this.mediaUpload(this, 'modal');
                }
                // 操作完成后，使用如下代码，将其值置位空，则可以解决再次触发change事件时失效的问题
                e.target.value = '';
            });
            // 模态框关闭监听
            $('.' + _this.config.elementClass + 'modal').on('hidden.bs.modal', function () {
                layer.closeAll();
            });
            var value = $('.' + this.config.elementClass).val();
            // // 获取name值后将input清空，防止叠加
            $('.' + this.config.elementClass).val('');
            if (value) {
                var arr = value.split(',');
                for (var i in arr) {
                    var suffix = arr[i].substring(arr[i].lastIndexOf('.') + 1);
                    var fileType = _this.getFileType(suffix);
                    _this.fileDisplay({
                        data: {
                            path: arr[i],
                            path_url: _this.config.rootPath + arr[i],
                            media_type: fileType
                        }
                    });
                }
            }
        };
        // 媒体列表
        MediaSelector.prototype.getMediaList = function () {
            var _this = this;
            var dropdownToggleButtonClass = $('.' + _this.config.elementClass + 'more');
            $('.' + _this.config.elementClass + 'table').bootstrapTable({
                url: '/admin/media-selector/media-list',         //请求后台的URL（*）
                method: 'get',                      //请求方式（*）
                toolbar: '.' + _this.config.elementClass + 'toolbar',                //工具按钮用哪个容器
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
                        title: _this.langs.preview, formatter: function (value, row) {
                            var html = '<a href="' + row.path_url + '" target="_blank" title="' + _this.langs.view + '">';
                            if (row.media_type === 'image') {
                                html += '<img class="img-thumbnail modal-img-thumbnail " src="' + row.path_url + '">';
                            } else if (row.media_type === 'video') {
                                html += '<video class="img-thumbnail modal-img-thumbnail" src="' + row.path_url + '"> </video>';
                            } else if (row.media_type === 'audio') {
                                html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-audio-o my_fa"></i></div>';
                            } else if (row.media_type === 'powerpoint') {
                                html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-word-o my_fa"></i></div>';
                            } else if (row.media_type === 'code') {
                                html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-code-o my_fa"></i></div>';
                            } else if (row.media_type === 'zip') {
                                html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-zip-o my_fa"></i></div>';
                            } else if (row.media_type === 'text') {
                                html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file-text-o my_fa"></i></div>';
                            } else if (row.media_type === 'other') {
                                html += '<div class="img-thumbnail modal-img-thumbnail" ><i class="fa fa-file my_fa"></i></div>';
                            }
                            html += '</a>';
                            return html;
                        }
                    },
                    {field: 'name', title: _this.langs.name},
                    {field: 'media_group_name', title: _this.langs.group_name},
                    {field: 'media_type', title: _this.langs.type},
                    {field: 'size', title: _this.langs.size},
                    {field: 'file_ext', title: _this.langs.file_suffix},
                    {field: 'created_at', title: _this.langs.created_at, sortable: true},
                    {
                        field: 'operate',
                        title: _this.langs.action,
                        width: '50%',
                        events: {
                            'click .chooseone': function (e, value, row) {
                                if (_this.config.type !== 'blend') {
                                    if (row.media_type !== _this.config.type) {
                                        _this.onError('Q_TYPE_DENIED_1');
                                        return false;
                                    }
                                }
                                if (_this.config.length > 1 && (_this.getFileNumber() + 1) > _this.config.length) {
                                    _this.onError('Q_EXCEED_NUM_LIMIT_1', {num: _this.config.length});
                                    return false;
                                }
                                _this.fileDisplay({data: row});
                                $('.' + _this.config.elementClass + 'modal').modal('hide');
                            },
                        },
                        formatter: _this.operateFormatter()
                    },
                ],
                queryParams: function (params) {
                    return {
                        page: (params.limit + params.offset) / params.limit,  // 分页索引
                        limit: params.limit,   // 分页大小
                        sort: params.sort, // 排序字段名
                        order: params.order, // 排序方式:asc正序,desc倒序
                        keyword: $('input[name="' + _this.config.elementClass + 'name"]').val(),
                        type: $('.' + _this.config.elementClass + 'type').select2('val'),
                        group_id: _this.selectedGroupId,
                    };
                },
                onLoadSuccess: function () {
                    return Dcat.loading(false);
                },
                onCheckAll: function (row) { // 点击全选框时触发的操作
                    if (row.length) {
                        $.each(row, function (i, field) {
                            _this.btselectarr.push(field.id);
                        });
                        var grid_items_selected = _this.langs.grid_items_selected;
                        dropdownToggleButtonClass.find(' .selected').html(grid_items_selected.replace('{n}', row.length));
                        dropdownToggleButtonClass.show();
                    }
                },
                onUncheckAll: function () { // 取消所有
                    _this.btselectarr = [];
                    dropdownToggleButtonClass.hide();
                },
                onCheck: function (row) { // 点击每一个单选框时触发的操作
                    _this.btselectarr.push(row.id);
                    if (_this.btselectarr.length >= 1) {
                        var grid_items_selected = _this.langs.grid_items_selected;
                        dropdownToggleButtonClass.find(' .selected').html(grid_items_selected.replace('{n}', _this.btselectarr.length));
                        dropdownToggleButtonClass.show();
                    }
                },
                onUncheck: function (row) { // 取消每一个单选框时对应的操作
                    var index = _this.btselectarr.indexOf(row.id);
                    if (index > -1) {
                        _this.btselectarr.splice(index, 1);
                    }
                    var grid_items_selected = _this.langs.grid_items_selected;
                    dropdownToggleButtonClass.find(' .selected').html(grid_items_selected.replace('{n}', _this.btselectarr.length));
                    if (_this.btselectarr.length <= 0) {
                        dropdownToggleButtonClass.hide();
                    }
                },
            });
        };
        // 媒体列表操作
        MediaSelector.prototype.operateFormatter = function () {
            return [
                '<a href="javascript:;" class="btn btn-block btn-danger btn-xs chooseone"><i class="fa fa-check"></i></a>'
            ].join('');
        };
        // 拖动排序
        MediaSelector.prototype.sortable = function () {
            var _this = this;
            if (_this.config.sortable) {
                new Sortable($('.' + _this.config.elementClass + 'media_display').get(0), {
                    animation: 150,
                    ghostClass: 'blue-background-class',
                    // 结束拖拽,对input值排序
                    onEnd: function () {
                        _this.getInputMedia();
                        return false;
                    },
                });
            }
        };
        // 媒体上传
        MediaSelector.prototype.mediaUpload = function (data, whereToUpload) {
            var _this = this;
            var formData = new FormData();
            var files = $(data)[0].files;
            $.each(files, function (i, field) {
                formData.append('file', field);
                formData.append('type', _this.config.type);
                formData.append('move', _this.config.move);
                formData.append('media_group_id', _this.selectedGroupId);
                formData.append('_token', Dcat.token);
                $.ajax({
                    type: 'post', // 提交方式 get/post
                    url: '/admin/media-selector/media-upload', // 需要提交的 url
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.code === 200) {
                            if (whereToUpload === 'form') {
                                _this.fileDisplay(data);
                            } else {
                            }
                        }
                        Dcat.success(_this.langs.upload_succeeded);
                    },
                    error: function (XmlHttpRequest) {
                        Dcat.error(XmlHttpRequest.responseJSON.message);
                    }
                });
                // 删除formData，防止重复累加
                formData.delete('file');
                formData.delete('type');
                formData.delete('move');
                formData.delete('_token');
                if (i === files.length - 1 && whereToUpload === 'modal') {
                    // 延迟刷新
                    setTimeout(function () {
                        _this.bootstrapTableRefresh();
                    }, 500);
                }
            });
        };
        // 媒体预览
        MediaSelector.prototype.fileDisplay = function (data) {
            var _this = this;
            if (_this.config.length === 1) {
                $('.' + _this.config.elementClass).val(data.data.path);
            } else if (_this.config.length > 1) {
                $('.' + _this.config.elementClass).val() ? $('.' + _this.config.elementClass).val($('.' + _this.config.elementClass).val() + ',' + data.data.path) : $('.' + _this.config.elementClass).val(data.data.path);
            }
            var html = '<li class="list-inline-item">';
            if (data.data.media_type === 'image') {
                html += '<img data-action="preview-img" class="img-thumbnail" src="' + data.data.path_url + '">';
            } else{
                html += '<a href="' + data.data.path_url + '" target="_blank" title="' + _this.langs.view + '">';
                if (data.data.media_type === 'video') {
                    html += '<video class="img-thumbnail" src="' + data.data.path_url + '"></video>';
                } else if (data.data.media_type === 'audio') {
                    html += '<i class="fa fa-file-audio-o img-thumbnail modal_my_fa"></i>';
                    html += '<video src="' + data.data.path_url + '" style="display: none"></video>';
                } else if (data.data.media_type === 'powerpoint') {
                    html += '<i class="fa fa-file-word-o img-thumbnail modal_my_fa"></i>';
                    html += '<video src="' + data.data.path_url + '" style="display: none"></video>';
                } else if (data.data.media_type === 'code') {
                    html += '<i class="fa fa-file-code-o img-thumbnail modal_my_fa"></i>';
                    html += '<video src="' + data.data.path_url + '" style="display: none"></video>';
                } else if (data.data.media_type === 'zip') {
                    html += '<div class="img-thumbnail" ><i class="fa fa-file-zip-o modal_my_fa"></i></div>';
                    html += '<video src="' + data.data.path_url + '" style="display: none"></video>';
                } else if (data.data.media_type === 'text') {
                    html += '<i class="fa fa-file-text-o img-thumbnail modal_my_fa modal_my_fa"></i>';
                    html += '<video src="' + data.data.path_url + '" style="display: none"></video>';
                } else if (data.data.media_type === 'other') {
                    html += '<i class="fa fa-file img-thumbnail modal_my_fa" ></i>';
                    html += '<video src="' + data.data.path_url + '" style="display: none"></video>';
                }
                html += '</a>';
            }
            html += '<button type="button" class="btn btn-block btn-danger btn-xs remove_media_display">';
            html += '<i class="fa fa-trash"></i>';
            html += '</button>';
            html += '</li>';
            if (_this.config.length === 1) {
                $('.' + _this.config.elementClass + 'media_display').html(html);
            } else if (_this.config.length > 1) {
                $('.' + _this.config.elementClass + 'media_display').append(html);
            }
            // 删除
            $('.remove_media_display').on('click', function () {
                $(this).hide().parent().remove();
                _this.getInputMedia();
                return false;
            });
        };
        // 获取预览区媒体值，重新组装
        MediaSelector.prototype.getInputMedia = function () {
            var _this = this;
            var src = '';
            // 循环获取属性下面的img/video src 值
            $.each($('.' + _this.config.elementClass + 'media_display li a'), function (index, content) {
                $(content).html().replace(/<img.*?src="(.*?)"[^>]*>/ig, function (a, b) {
                    src += b + ',';
                });
                $(content).html().replace(/<video.*?src="(.*?)"[^>]*>/ig, function (a, b) {
                    src += b + ',';
                });
                $(content).html().replace(/<a.*?href="(.*?)"[^>]*>/ig, function (a, b) {
                    src += b + ',';
                });
            });

            var reg = new RegExp(_this.config.rootPath, 'g');//g,表示全部替换。

            var srcs = src.replace(reg, '');

            $('.' + _this.config.elementClass).val(srcs.substring(0, srcs.length - 1));
        };
        // 获取预览区媒体数量
        MediaSelector.prototype.getFileNumber = function () {
            var _this = this;
            return $('.' + _this.config.elementClass + 'media_display').find('li').length;
        };
        // 刷新Table
        MediaSelector.prototype.bootstrapTableRefresh = function () {
            var _this = this;
            Dcat.loading();
            $('.' + _this.config.elementClass + 'table').bootstrapTable('refresh').bootstrapTable('hideLoading');
        };
        // 获取Table选中的行数据
        MediaSelector.prototype.bootstrapTableGetSelections = function () {
            var _this = this;
            return $('.' + _this.config.elementClass + 'table').bootstrapTable('getSelections');
        };
        /**
         * 翻译
         *
         * @example
         *      this.trans('name')
         *      this.trans('selected_options', {num: 18}) // :num options selected
         *
         * @param {string} code
         * @param {object} replace
         * @returns {*}
         */
        MediaSelector.prototype.onError = function (code, replace = '') {
            var _this = this;
            switch (code) {
                case 'grid_items_selected':
                    Dcat.error(_this.lang.trans(code));
                    break;
                case 'Q_TYPE_DENIED':
                    Dcat.error(_this.lang.trans(code));
                    break;
                case 'Q_TYPE_DENIED_1':
                    Dcat.error(_this.lang.trans(code));
                    break;
                case 'Q_EXCEED_NUM_LIMIT':
                    Dcat.error(_this.lang.trans(code, replace));
                    break;
                case 'Q_EXCEED_NUM_LIMIT_1':
                    Dcat.error(_this.lang.trans(code, replace));
                    break;
                default:
                    Dcat.error('Error: ' + code);
            }
        };
        // 获取文件类型
        MediaSelector.prototype.getFileType = function (suffix) {
            // 获取类型结果
            var result = '';
            // 匹配图片
            var img_list = [
                'bmp', 'cgm', 'djv', 'djvu', 'gif', 'ico', 'ief', 'jp2', 'jpe', 'jpeg', 'jpg', 'mac', 'pbm', 'pct', 'pgm', 'pic', 'pict',
                'png', 'pnm', 'pnt', 'pntg', 'ppm', 'qti', 'qtif', 'ras', 'rgb', 'svg', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd'
            ];
            // 匹配音频
            var audio_list = ['mp3', 'wav', 'flac', '3pg', 'aa', 'aac', 'ape', 'au', 'm4a', 'mpc', 'ogg'];
            // 匹配视频
            var video_list = ['mp4', 'rmvb', 'flv', 'mkv', 'avi', 'wmv', 'rm', 'asf', 'mpeg'];
            // 匹配文稿
            var powerpoint_list = [
                'doc', 'dot', 'docx', 'dotx', 'docm', 'dotm', 'xls', 'xlt', 'xla', 'xlsx', 'xltx', 'xlsm', 'xltm', 'xlam', 'xlsb',
                'ppt', 'pdf', 'pot', 'pps', 'ppa', 'pptx', 'potx', 'ppsx', 'ppam', 'pptm', 'potm', 'ppsm'
            ];
            // 匹配代码
            var code_list = ['php', 'js', 'java', 'python', 'ruby', 'go', 'c', 'cpp', 'sql', 'm', 'h', 'json', 'html', 'aspx'];
            // 匹配压缩包
            var zip_list = ['zip', 'tar', 'gz', 'rar', 'rpm'];
            // 匹配文本
            var text_list = ['txt', 'pac', 'log', 'md'];
            // 无后缀返回 false
            if (!suffix) {
                result = false;
                return result;
            }
            result = img_list.some(function (item) {
                return item === suffix;
            });
            if (result) {
                result = 'image';
                return result;
            }
            result = audio_list.some(function (item) {
                return item === suffix;
            });
            if (result) {
                result = 'audio';
                return result;
            }
            result = video_list.some(function (item) {
                return item === suffix;
            });
            if (result) {
                result = 'video';
                return result;
            }
            result = powerpoint_list.some(function (item) {
                return item === suffix;
            });
            if (result) {
                result = 'powerpoint';
                return result;
            }
            result = code_list.some(function (item) {
                return item === suffix;
            });
            if (result) {
                result = 'code';
                return result;
            }
            result = zip_list.some(function (item) {
                return item === suffix;
            });
            if (result) {
                result = 'zip';
                return result;
            }
            result = text_list.some(function (item) {
                return item === suffix;
            });
            if (result) {
                result = 'text';
                return result;
            }
            // 其他 文件类型
            result = 'other';
            return result;
        };
        window.MediaSelector = MediaSelector;
    }
)();
