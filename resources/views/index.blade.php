<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group">
            <input name="{{ $name }}" class="form-control {{ $class }}" placeholder="{{ $placeholder }}"
                   {!! $attributes !!} value="{{ $value }}">

            <div class="input-group-append">
                <input type="file" style="display: none;"/>

                <button type="button" class="btn btn-success btn-file mr-1">
                    <i class="fa fa-folder-open"></i> 上传<span></span>
                </button>

                <button type="button" class="btn btn-primary btn-file"><i class="fa fa-folder-open"></i> 选择</button>
            </div>
        </div>

        @include('admin::form.help-block')

        <ul class="d-flex flex-wrap list-inline plupload-preview"></ul>
    </div>
</div>

<script init="{!! $selector !!}">
    // 上传文本框
    $(this).next().find('input').addClass('form_upload' + id);
    // 上传按钮
    $(this).next().children('button').eq(0).addClass('form_upload_button' + id);
    // 上传进度条
    $(this).next().children('button').eq(0).find('span').addClass('form_percent' + id);
    // 模态框按钮
    $(this).next().children('button').eq(1).addClass('form_modal_button' + id);
    // 预览区
    $(this).parent().next().next().addClass('media_display' + id);

    $('.form_upload_button' + id + ',.form_modal_button' + id).MediaSelector({
        inputId: id,
        label: '{!! $label !!}',
        config: {!! $config !!},
        grouplist: {!! $grouplist !!},
        selectList: {!! $selectList !!},
    })
</script>
