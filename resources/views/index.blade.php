<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$name}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="col-xs-12 {{$viewClass['field']}}">
        @include('admin::form.error')
        <div class="input-group">
            <input name="{{$name}}" class="form-control {{$class}}" placeholder="{{ $placeholder }}"
                   {!! $attributes !!} value="{{ old($column, $value) }}">
            <div class="input-group-btn input-group-append">
                <div class="btn btn-danger btn-file">
                    <i class="fa fa-upload"></i>
                    <span class="hidden-xs">{{__('admin.upload')}}</span>
                    <span class="{{$elementClass}}form_percent"></span>
                    <input type="file" class="avatar {{$elementClass}}form_upload" @if($length > 1) multiple @endif>
                </div>
                <div class="btn btn-primary btn-file media_selector_modal_button {{$elementClass}}get_media_list"
                     data-toggle="modal" data-target=".{{$elementClass}}modal">
                    <i class="fa fa-folder-open"></i><span class="hidden-xs">{{__('admin.choose')}}</span>
                </div>
            </div>
        </div>
        @include('admin::form.help-block')
        <ul class="d-flex flex-wrap list-inline plupload-preview help-block {{$elementClass}}media_display"></ul>
    </div>
</div>

@include('dcat-media-selector::modal')

<script require="@select-table,@select2?lang={{ config('app.locale') === 'en' ? '' : str_replace('_', '-', config('app.locale')) }},@de-memory.dcat-media-selector?lang={{ config('app.locale') === 'en' ? 'en-US' : str_replace('_', '-', config('app.locale','en-US')) }}">

    var langs = {!! $lang !!}, // 错误信息
        config = {!! $config !!}, // 上传配置
        value = "{{$value}}";

    // 获取name值后将input清空，防止叠加
    $('.' + config.elementClass).val('');

    if (value) {
        var arr = value.split(',');
        for (var i in arr) {
            var suffix = arr[i].substring(arr[i].lastIndexOf('.') + 1);
            var fileType = getFileType(suffix);
            fileDisplay({
                data: {
                    path: arr[i],
                    url: config.rootPath + arr[i],
                    media_type: fileType
                }
            }, config, langs);
        }
    }
</script>