(function (w, $) {
    function MediaSelector(options) {
        this.options = options;

        this.init();
    }

    MediaSelector.prototype = {
        // 初始化
        init: function () {
            var _this = this,
                inputValue = $(_this.options.$el.find('input').eq(0)).val(),
                config = _this.options.config;

            _this.sortable();

            // 获取input值后将input清空，防止叠加
            $(_this.options.$el.find('input').eq(0)).val('');

            // 给input、预览区赋值
            if (inputValue) {
                var arr = inputValue.split(',');
                for (var i in arr) {
                    var media_type = getFileType(arr[i].substring(arr[i].lastIndexOf('.') + 1));
                    let url = arr[i].indexOf('http') === 0 ? arr[i] : config.rootPath + arr[i];
                    _this.fileDisplay({data: {path: arr[i], url: url, media_type: media_type}});
                }
            }

            // 表单上传按钮
            _this.options.$el.find('button').eq(0).off('click').on('click', function () {
                $(this).prev().click();
            });

            // 表单上传文本框
            _this.options.$el.find('input').eq(1).off('change').on('change', function (e) {
                if ($(this).val() !== '') {
                    var isUpload = true;

                    if (config.limit > 1 && _this.getFileNumber() + 1 > config.limit) {
                        Dcat.error('对不起，已超出文件数量限制');
                        return false;
                    }

                    $.each($(this)[0].files, function (i, row) {

                        var suffix = row.name.substring(row.name.lastIndexOf('.') + 1);

                        if ($.inArray(getFileType(suffix), config.types) < 0) {
                            Dcat.error('对不起，不允许选择此类型文件');
                            isUpload = false;
                            return false;
                        }
                    });

                    if (isUpload) {
                        _this.upload(this, 0, 'form');
                    }

                }

                // 操作完成后，将其值置位空，则可以解决再次触发change事件时失效的问题
                e.target.value = '';
            });

            _this.options.$el.find('button').eq(1).off('click').on('click', function () {
                var table = '<div class="row">';
                // 分组
                table += _this.leftGroupHtml();
                // 表单
                table += _this.rightTableQueryFormHtml();
                // 工具栏
                table += rightTableHtml();
                table += '</div>';

                layer.open({
                    id: 'media_selector',
                    area: config.area,
                    title: _this.options.label,
                    btn: [],
                    content: table,
                    success: function (layero, index) {
                        _this.list(index);
                    }
                });
            });
        },

        // 预览区
        fileDisplay: function (data) {
            var _this = this,
                inputId = _this.options.$el.find('input').eq(0),
                config = _this.options.config;

            if (config.limit === 1) {
                inputId.val(data.data.path);
            } else if (config.limit > 1) {
                inputId.val() ? inputId.val(inputId.val() + ',' + data.data.path) : inputId.val(data.data.path);
            }

            var html = '<li>';
            html += '<input type="hidden" value="' + data.data.path + '">';
            html += '<a href="' + data.data.url + '" title="' + data.data.name + '" target="_blank" class="thumbnail">';
            html += fileDisplayHtml({media_type: data.data.media_type, url: data.data.url});
            html += '</a>';
            html += '<button type="button" class="btn btn-block btn-danger btn-xs remove_media_display">';
            html += '<i class="fa fa-trash"></i>';
            html += '</button>';
            html += '</li>';

            if (config.limit === 1) {
                _this.options.$el.find('ul').html(html);
            } else if (config.limit > 1) {
                _this.options.$el.find('ul').append(html);
            }

            _this.getInputMedia();

            // form表单删除事件
            $('.remove_media_display').on('click', function () {
                $(this).hide().parent().remove();
                _this.getInputMedia();
                return false;
            });
        },
        
        // 获取前缀
        getPrefix: function () {
            return '/' + window.location.href.split('/')[3] + '/';
        },
        
        // 上传
        upload: function (data, groupId, whereToUpload) {
            var _this = this,
                config = _this.options.config,
                rows = $(data)[0].files,
                formData = new FormData();

            $.each(rows, function (i, row) {
                formData.append('file', row);
                formData.append('types', config.types);
                formData.append('move', config.move);
                formData.append('group_id', groupId);
                formData.append('_token', Dcat.token);

                $.ajax({
                    type: 'post', // 提交方式 get/post
                    url: _this.getPrefix() + 'media-selector/m-upload', // 需要提交的 url
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function () {
                        var xhr = $.ajaxSettings.xhr();

                        if (xhr.upload) {
                            xhr.upload.addEventListener('progress', function (event) {
                                var percent = Math.floor(event.loaded / event.total * 100);
                                if (whereToUpload === 'form') {
                                    _this.options.$el.find('span').text(percent + '%');
                                } else if (whereToUpload === 'modal') {
                                    $('.media_selector_modal_percent').text(percent + '%');
                                }
                            }, false);
                        }

                        return xhr;
                    }, success: function (data) {
                        if (whereToUpload === 'form') {
                            _this.options.$el.find('span').text('');
                            _this.fileDisplay(data);
                        } else if (whereToUpload === 'modal') {
                            $('.media_selector_modal_percent').text('');
                        }
                    }, error: function (XmlHttpRequest) {
                        if (whereToUpload === 'form') {
                            _this.options.$el.find('span').text('');
                        } else if (whereToUpload === 'modal') {
                            $('.media_selector_modal_percent').text('');
                        }
                        Dcat.error(XmlHttpRequest.responseJSON.message);
                        return false;
                    }
                });

                if (i === rows.length - 1 && whereToUpload === 'modal') {
                    // 延迟刷新
                    setTimeout(function () {
                        $('.media_selector_toolbar_refresh').click();
                    }, 500);
                }
            });
        },

        // 排序
        sortable: function () {
            var _this = this,
                config = _this.options.config;

            if (config.sortable) {
                new Sortable(_this.options.$el.find('ul').get(0), {
                    animation: 150,
                    ghostClass: 'blue-background-class',
                    // 结束拖拽,对input值排序
                    onEnd: function () {
                        _this.getInputMedia();
                        return false;
                    },
                });
            }

        },

        // 列表
        list: function (layerIndex) {
            var _this = this,
                config = _this.options.config,
                toolbarMore = $('.media_selector_toolbar_more'),
                queryType = '',
                btselectarr = [],
                selectedGroupId = 0;

            $.contextMenu({
                selector: '.media_selector_media_group .menu',
                items: {
                    edit: {name: '编辑', icon: 'edit'},
                    delete: {name: '删除', icon: 'delete'},
                },
                callback: function (key, options) {
                    if (key === 'edit') {
                        layer.prompt({
                            title: '编辑',
                            maxmin: false,
                            value: options.$trigger.html(),
                        }, function (value, index) {
                            $.ajax({
                                type: 'POST',
                                url: _this.getPrefix() + 'media-selector/g-edit',
                                data: {id: options.$trigger.data('id'), name: value},
                                datatype: 'jsonp',
                                success: function () {
                                    options.$trigger.html(value);
                                    Dcat.success('更新成功');
                                    layer.close(index);
                                },
                                error: function (XmlHttpRequest) {
                                    Dcat.error(XmlHttpRequest.responseJSON.message);
                                }
                            });
                        });
                    } else if (key === 'delete') {
                        Dcat.confirm('确认删除?', options.$trigger.html(), function () {
                            $.ajax({
                                type: 'POST',
                                url: _this.getPrefix() + 'media-selector/g-delete',
                                data: {id: options.$trigger.data('id')},
                                datatype: 'jsonp',
                                success: function () {
                                    options.$trigger.remove();
                                    Dcat.success('删除成功');
                                },
                                error: function (XmlHttpRequest) {
                                    Dcat.error(XmlHttpRequest.responseJSON.message);
                                }
                            });
                        });
                    }
                }
            });

            $('.media_selector_table').bootstrapTable('destroy').bootstrapTable({
                url: _this.getPrefix() + 'media-selector/m-list',
                toolbar: '.media_selector_toolbar', // 工具按钮用哪个容器
                dataField: 'data',
                classes: 'table table-collapse custom-data-table',
                striped: true, // 是否显示行间隔色
                pagination: true, // 是否显示分页（*）
                sortable: true, // 是否启用排序
                sortOrder: 'desc', // 排序方式
                sidePagination: 'server', // 分页方式：client客户端分页，server服务端分页（*）
                columns: [
                    {checkbox: true},
                    {field: 'id', title: 'ID', sortable: true, visible: false},
                    {
                        title: '预览', formatter: function (value, row) {
                            var html = '<a href="' + row.url + '" title="' + row.name + '" target="_blank">';
                            html += fileDisplayHtml(row);
                            html += '</a>';
                            return html;
                        }
                    },
                    {field: 'media_group_name', title: '分组'},
                    {field: 'size', title: '大小'},
                    {field: 'file_ext', title: '后缀'},
                    {field: 'media_type', title: '类型'},
                    {
                        field: 'operate', title: '操作', width: '50%',
                        events: {
                            'click .choose': function (e, value, row) {
                                if ($.inArray(row.media_type, config.types) < 0) {
                                    Dcat.error('对不起，不允许选择此类型文件');
                                    return false;
                                }

                                if (config.limit > 1 && _this.getFileNumber() + 1 > config.limit) {
                                    Dcat.error('对不起，已超出文件数量限制');
                                    return false;
                                }

                                _this.fileDisplay({data: row});
                                layer.close(layerIndex);
                            },
                        },
                        formatter: [
                            '<a href="javascript:;" class="btn btn-block btn-danger btn-xs choose"><i class="fa fa-check"></i></a>'
                        ].join('')
                    }
                ],
                queryParams: function (params) {
                    return {
                        page: (params.limit + params.offset) / params.limit,  // 分页索引
                        limit: params.limit,   // 分页大小
                        sort: params.sort, // 排序字段名
                        order: params.order, // 排序方式:asc正序,desc倒序
                        keyword: $('input[name="media_selector_name"]').val(),
                        type: queryType,
                        group_id: selectedGroupId,
                    };
                },
                onLoadSuccess: function () {
                    $.LoadingOverlay('hide');
                },
                onCheckAll: function (row) { // 点击全选框时触发的操作
                    if (row.length) {
                        $.each(row, function (i, field) {
                            btselectarr.push(field.id);
                        });
                        toolbarMore.find(' .selected').html('已选择' + row.length + '项');
                        toolbarMore.show();
                    }
                },
                onUncheckAll: function () { // 取消所有
                    btselectarr = [];
                    toolbarMore.hide();
                },
                onCheck: function (row) { // 点击每一个单选框时触发的操作
                    btselectarr.push(row.id);
                    if (btselectarr.length >= 1) {
                        toolbarMore.find(' .selected').html('已选择' + btselectarr.length + '项');
                        toolbarMore.show();
                    }
                },
                onUncheck: function (row) { // 取消每一个单选框时对应的操作
                    var index = btselectarr.indexOf(row.id);
                    if (index > -1) {
                        btselectarr.splice(index, 1);
                    }
                    toolbarMore.find(' .selected').html('已选择' + btselectarr.length + '项');
                    if (btselectarr.length <= 0) {
                        toolbarMore.hide();
                    }
                },
            });

            $('.media_selector_add_group').on('click', function () {
                layer.prompt({
                    title: '添加',
                    maxmin: false,
                }, function (value, index) {
                    $.ajax({
                        type: 'POST',
                        url: _this.getPrefix() + 'media-selector/g-add',
                        data: {name: value},
                        datatype: 'jsonp',
                        success: function (data) {
                            layer.close(index);
                            $('.media_selector_media_group').append('<a class="list-group-item list-group-item-action menu" data-toggle="list" data-id="' + data.data + '" href="javascript:;">' + value + '</a>');
                            Dcat.success('添加成功');
                        },
                        error: function (XmlHttpRequest) {
                            Dcat.error(XmlHttpRequest.responseJSON.message);
                        }
                    });
                });
            });

            $(document).off('click', '.media_selector_media_group a').on('click', '.media_selector_media_group a', function () {
                selectedGroupId = $(this).attr('data-id');
                $('.media_selector_toolbar_refresh').click();
            });

            $('.media_selector_type').select2({allowClear: true, minimumInputLength: 0});

            $('.media_selector_batch_delete').click(function () {
                Dcat.confirm('确认删除?', null, function () {
                    Dcat.loading();
                    var deleteId = [], deletePaths = [];
                    var rows = $('.media_selector_table').bootstrapTable('getSelections');

                    $.each(rows, function (i, row) {
                        deleteId.push(row.id);
                        deletePaths.push(row.path);
                    });
                    $.ajax({
                        type: 'POST',
                        url: _this.getPrefix() + 'media-selector/m-delete',
                        data: {delete_ids: deleteId, delete_paths: deletePaths},
                        datatype: 'jsonp',
                        success: function () {
                            Dcat.loading(false);
                            Dcat.success('删除成功');
                            $('.media_selector_toolbar_refresh').click();
                        },
                        error: function (XmlHttpRequest) {
                            Dcat.loading(false);
                            Dcat.error(XmlHttpRequest.responseJSON.message);
                        }
                    });
                });
            });

            $('.media_selector_batch_mobile').click(function () {
                var rows = $('.media_selector_table').bootstrapTable('getSelections');
                var moveId = [];
                $.each(rows, function (i, row) {
                    moveId.push(row.id);
                });
                layer.open({
                    title: '移动',
                    type: 1,
                    shadeClose: true,
                    maxmin: false,
                    move: false,
                    area: ['275px', '200px'],
                    btn: ['确定', '取消'],
                    content: _this.groupSelect(),
                    yes: function (index) {
                        $.ajax({
                            type: 'POST',
                            url: _this.getPrefix() + 'media-selector/m-move',
                            data: {group_id: $('#media_selector_group_id').val(), move_ids: moveId.join(',')},
                            datatype: 'jsonp',
                            success: function () {
                                Dcat.success('移动成功');
                                Dcat.loading(false);
                                $('.media_selector_toolbar_refresh').click();
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

            $('.media_selector_toolbar_refresh, .media_selector_search').on('click', function () {
                $.LoadingOverlay('show', {
                    imageColor: '#2090ff',
                    progressColor: '#2090ff'
                });
                queryType = $('.media_selector_type').select2('val');
                btselectarr = [];
                toolbarMore.hide();
                $('.media_selector_table').bootstrapTable('refresh').bootstrapTable('hideLoading');
            });

            $('.media_selector_toolbar_filter').on('click', function () {
                $('.media_selector_form').toggle();
            });

            $('.media_selector_toolbar_choose').on('click', function () {
                var rows = $('.media_selector_table').bootstrapTable('getSelections');

                if (rows.length === 0) {
                    layer.close(layerIndex);
                }

                if (_this.getFileNumber() + rows.length > config.limit) {
                    Dcat.error('对不起，已超出文件数量限制');
                    return false;
                }

                var result = true;
                $.each(rows, function (i, row) {
                    if ($.inArray(row.media_type, config.types) < 0) {
                        result = false;
                        return false;
                    }
                });

                if (!result) {
                    Dcat.error('对不起，不允许选择此类型文件');
                    return false;
                }
                $.each(rows, function (i, row) {
                    _this.fileDisplay({data: row});
                    layer.close(layerIndex);
                });

            });

            $('.media_selector_modal_upload').on('change', function (e) {
                if ($(this).val() !== '') {

                    var files = $(this)[0].files,
                        isUpload = true;

                    $.each(files, function (i, field) {
                        var suffix = field.name.substring(field.name.lastIndexOf('.') + 1);
                        if ($.inArray(getFileType(suffix), config.types) < 0) {
                            Dcat.error('对不起，不允许选择此类型文件');
                            isUpload = false;
                            return false;
                        }
                    });

                    if (isUpload) {
                        _this.upload(this, selectedGroupId, 'modal');
                    }
                }

                // 操作完成后，将其值置位空，则可以解决再次触发change事件时失效的问题
                e.target.value = '';
            });
        },

        // 获取ul li 数量
        getFileNumber: function () {
            return this.options.$el.find('ul li').length;
        },

        // 获取ul li 下面的input值，赋给表单input
        getInputMedia: function () {
            var _this = this;

            var results = $.map(_this.options.$el.find('ul').children('li'), function (content) {
                return $(content).find('input').val();
            });

            _this.options.$el.find('input').eq(0).val(results.join(','));
        },

        // 左侧分组
        leftGroupHtml: function () {
            var _this = this,
                grouplist = _this.options.grouplist;


            var html = '<div class="col-3 col-sm-3 border-right">';
            html += '<button type="button" class="btn btn-primary btn-block grid-refresh btn-mini btn-outline mb-1 media_selector_add_group"><i class="feather icon-plus"></i> 添加分组</button>';
            html += '<div class="list-group list-group-flush media_selector_media_group">';
            html += '<a class="list-group-item list-group-item-action active" data-toggle="list" data-id="0" href="javascript:;">全部</a>';
            for (var key1 in grouplist) {
                html += '<a class="list-group-item list-group-item-action menu" data-toggle="list" data-id="' + key1 + '" href="javascript:;">' + grouplist[key1] + '</a>';
            }
            html += '</div>';
            html += '</div>';

            return html;
        },

        // 左侧表格搜索表单
        rightTableQueryFormHtml: function () {
            var _this = this,
                selectList = _this.options.selectList;

            var html = '<div class="col-9 col-sm-9">';
            html += '<div class="media_selector_form border-bottom mb-1" style="display: none">';
            html += '<div class="row">';
            html += '<div class="filter-input col-sm-3">';
            html += '<div class="form-group">';
            html += '<div class="input-group input-group-sm">';
            html += '<div class="input-group-prepend"><span class="input-group-text bg-white text-capitalize"><b>名称</b></span></div>';
            html += '<input type="text" class="form-control" name="media_selector_name" placeholder="名称">';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '<div class="filter-input col-sm-3">';
            html += '<div class="form-group">';
            html += ' <div class="input-group input-group-sm">';
            html += '<div class="input-group-prepend"><span class="input-group-text bg-white text-capitalize"><b>类型</b></span></div>';
            html += '<select class="form-control media_selector_type" style="width: 100%;" tabIndex="-1" aria-hidden="true" data-placeholder="类型">';
            html += '<option value=""></option>';
            for (var key2 in selectList) {
                html += '<option value="' + key2 + '">' + selectList[key2] + '</option>';
            }
            html += '</select>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '<div class="btn-group" style="height: fit-content">';
            html += '<button type="button"  class="btn btn-primary grid-refresh btn-mini btn-sm btn-outline media_selector_search"><i class="feather icon-search"></i> 搜索</button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

            return html;
        },

        // 分组下拉框
        groupSelect: function () {
            var grouplist = [];

            $('.media_selector_media_group a').each(function () {
                var groupId = $(this).attr('data-id');
                if (groupId >= 1) {
                    grouplist[groupId] = $(this).text();
                }
            });

            var html = '<div style="margin: 0 auto; width: 200px; height: 80px; text-align: center; line-height: 80px;">';
            html += '<select id="media_selector_group_id" style="width: 100%;height: 35px">';
            for (var key3 in grouplist) {
                html += '<option value="' + key3 + '">' + grouplist[key3] + '</option>';
            }
            html += '</select>';
            html += '</div>';

            return html;
        }
    };

    $.fn.MediaSelector = function (options) {
        options = options || {};
        options = $.extend({
            $el: $(this),
        }, options);
        return new MediaSelector(options);
    };
})(window, jQuery);
