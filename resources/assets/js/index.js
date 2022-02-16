(function () {
    // 预览图片
    $(document).off('click.mediaselector', '[data-action="mediaselector-preview-image"]')
        .on('click.mediaselector', '[data-action="mediaselector-preview-image"]', function () {
            Dcat.helpers.previewImage($(this).data('url'));
        });

    // 预览代码
    $(document).off('click.mediaselector', '[data-action="mediaselector-preview-code"]')
        .on('click.mediaselector', '[data-action="mediaselector-preview-code"]', function () {
            previewCode($(this).data('url'));
        });
    // 预览压缩包
    $(document).off('click.mediaselector', '[data-action="mediaselector-preview-zip"]')
        .on('click.mediaselector', '[data-action="mediaselector-preview-zip"]', function () {
            previewZip($(this).data('url'));
        });
    // 预览文本
    $(document).off('click.mediaselector', '[data-action="mediaselector-preview-text"]')
        .on('click.mediaselector', '[data-action="mediaselector-preview-text"]', function () {
            previewText($(this).data('url'));
        });
    // 预览其他
    $(document).off('click.mediaselector', '[data-action="mediaselector-preview-other"]')
        .on('click.mediaselector', '[data-action="mediaselector-preview-other"]', function () {
            previewOther($(this).data('url'));
        });
})();

// 刷新Table
function bootstrapTableRefresh(config) {
    Dcat.loading();
    $('.' + config.elementClass + 'table').bootstrapTable('refresh').bootstrapTable('hideLoading');
}

// 获取Table选中的行数据
function bootstrapTableGetSelections(config) {
    return $('.' + config.elementClass + 'table').bootstrapTable('getSelections');
};

// 拖动排序
function sortable(config) {
    if (config.sortable) {
        new Sortable($('.' + config.elementClass + 'media_display').get(0), {
            animation: 150,
            ghostClass: 'blue-background-class',
            // 结束拖拽,对input值排序
            onEnd: function () {
                getInputMedia(config);
                return false;
            },
        });
    }
}

// 模态框操作列
function operateFormatter() {
    return [
        '<a href="javascript:;" class="btn btn-block btn-danger btn-xs chooseone"><i class="fa fa-check"></i></a>'
    ].join('');
}

function onError(lang, code, replace = '') {
    switch (code) {
        case 'grid_items_selected':
            Dcat.error(lang.trans(code));
            break;
        case 'Q_TYPE_DENIED':
            Dcat.error(lang.trans(code));
            break;
        case 'Q_TYPE_DENIED_1':
            Dcat.error(lang.trans(code));
            break;
        case 'Q_EXCEED_NUM_LIMIT':
            Dcat.error(lang.trans(code, replace));
            break;
        case 'Q_EXCEED_NUM_LIMIT_1':
            Dcat.error(lang.trans(code, replace));
            break;
        default:
            Dcat.error('Error: ' + code);
    }
}

function getFileNumber(config) {
    return $('.' + config.elementClass + 'media_display').find('li').length;
}

function fileDisplay(data, config, langs) {
    if (config.length === 1) {
        $('.' + config.elementClass).val(data.data.path);
    } else if (config.length > 1) {
        $('.' + config.elementClass).val() ? $('.' + config.elementClass).val($('.' + config.elementClass).val() + ',' + data.data.path) : $('.' + config.elementClass).val(data.data.path);
    }
    var html = '<li class="list-inline-item">';
    html += '<a href="javascript:;" title="' + langs.view + '">';
    if (data.data.media_type === 'image') {
        html += '<img class="img-thumbnail mediaselector-preview" src="' + data.data.url + '" data-action="mediaselector-preview-image" data-url="' + data.data.url + '">';
    } else if (data.data.media_type === 'video') {
        html += '<video class="img-thumbnail mediaselector-preview" src="' + data.data.url + '" data-action="mediaselector-preview-video" data-url="' + data.data.url + '"/>';
    } else if (data.data.media_type === 'audio') {
        html += '<i class="fa fa-file-audio-o img-thumbnail modal_my_fa mediaselector-preview" data-action="mediaselector-preview-audio" data-url="' + data.data.url + '"></i>';
    } else if (data.data.media_type === 'powerpoint') {
        html += '<i class="fa fa-file-word-o img-thumbnail modal_my_fa mediaselector-preview" data-action="mediaselector-preview-powerpoint" data-url="' + data.data.url + '"></i>';
    } else if (data.data.media_type === 'code') {
        html += '<i class="fa fa-file-code-o img-thumbnail modal_my_fa mediaselector-preview" data-action="mediaselector-preview-code" data-url="' + data.data.url + '"></i>';
    } else if (data.data.media_type === 'zip') {
        html += '<div class="img-thumbnail mediaselector-preview" ><i class="fa fa-file-zip-o modal_my_fa" data-action="mediaselector-preview-zip" data-url="' + data.data.url + '"></i></div>';
    } else if (data.data.media_type === 'text') {
        html += '<i class="fa fa-file-text-o img-thumbnail modal_my_fa modal_my_fa mediaselector-preview" data-action="mediaselector-preview-text" data-url="' + data.data.url + '"></i>';
    } else if (data.data.media_type === 'other') {
        html += '<i class="fa fa-file img-thumbnail modal_my_fa mediaselector-preview" data-action="mediaselector-preview-other" data-url="' + data.data.url + '"></i>';
    }
    html += '</a>';
    html += '<button type="button" class="btn btn-block btn-danger btn-xs remove_media_display">';
    html += '<i class="fa fa-trash"></i>';
    html += '</button>';
    html += '</li>';

    if (config.length === 1) {
        $('.' + config.elementClass + 'media_display').html(html);
    } else if (config.length > 1) {
        $('.' + config.elementClass + 'media_display').append(html);
    }

    // 删除
    $('.remove_media_display').on('click', function () {
        $(this).hide().parent().remove();
        getInputMedia(config);
        return false;
    });
};

function getInputMedia(config) {
    // 循环获取属性下面的img/video src 值
    var urls = $.map($('.' + config.elementClass + 'media_display li'), function (content, index) {
        return $(content).find('[data-url]').first().data('url');
    });

    var src = urls.join(',');

    var reg = new RegExp(config.rootPath, 'g');//g,表示全部替换。

    var srcs = src.replace(reg, '');

    $('.' + config.elementClass).val(srcs);
}

// 预览视频
function previewVideo(url, lang) {
    var tips = lang.trans('preview_video_unsupported');
    layer.open({
        type: 1,
        shade: 0.2,
        offset: 'auto',
        area: ["500px", "500px"],
        shadeClose: true,
        skin: 'layer-preview-video',
        title: false,
        move: '.layui-layer-content',
        content: '<video src="' + url + '" width="100%" height="100%" controls autoplay>' + tips + '</video>'
    })
}

// 预览音频
function previewAudio(url, lang) {
    var tips = lang.trans('preview_audio_unsupported');
    layer.open({
        type: 1,
        shade: 0.2,
        shadeClose: true,
        resize: false,
        title: false,
        maxmin: false,
        skin: 'layer-preview-audio',
        content: '<audio src="' + url + '" controls autoplay>' + tips + '</audio>'
    });
}

// 预览PowerPoint
function previewPowerpoint(url, config) {
    if (config.useMicrosoftPreview) {
        layer.open({
            type: 2,
            shade: 0.2,
            area: ["800px", "500px"],
            shadeClose: true,
            title: lang.preview,
            skin: 'layer-preview-powerpoint',
            content: 'http://view.officeapps.live.com/op/view.aspx?src=' + url
        });
    } else {
        this.openNewWindow(url)
    }
}

// 预览代码
function previewCode(url) {
    layer.open({
        type: 2,
        shade: 0.2,
        shadeClose: true,
        title: this.langs.preview,
        skin: 'layer-preview-code',
        content: url
    });
}

// 预览压缩包
function previewZip(url) {
    this.openNewWindow(url)
}

// 预览文本
function previewText(url) {
    layer.open({
        type: 2,
        shade: 0.2,
        shadeClose: true,
        title: this.langs.preview,
        skin: 'layer-preview-text',
        content: url
    });
}

// 预览其他
function previewOther(url) {
    this.openNewWindow(url)
}

// 打开新窗口
function openNewWindow(url) {
    var link = $("<a><span> </span></a>").attr("href", url).attr("target", "_blank");
    $("body").append(link);
    link[0].click();
    link.remove();
}

// 获取文件类型
function getFileType(suffix) {
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
}

function mediaUpload(data, selectedGroupId, config, langs, whereToUpload) {
    var formData = new FormData();
    var files = $(data)[0].files;
    $.each(files, function (i, field) {
        formData.append('file', field);
        formData.append('type', config.type);
        formData.append('move', config.move);
        formData.append('media_group_id', selectedGroupId);
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
                        fileDisplay(data, config, langs);
                    } else {
                    }
                }
                Dcat.success(langs.upload_succeeded);
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
                bootstrapTableRefresh(config);
            }, 500);
        }
    });
}
