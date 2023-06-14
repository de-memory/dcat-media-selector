<div class="{{$viewClass['form-group']}} {{ $class }}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group">
            <input name="{{ $name }}" class="form-control" placeholder="{{ $placeholder }}"
                   {!! $attributes !!} value="{{ $value }}">

            <div class="input-group-append">
                <input type="file" style="display: none;" @if($limit > 1) multiple @endif >

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
    $('#' + id).MediaSelector({
        label: '{!! $label !!}',
        config: {!! $config !!},
        grouplist: {!! $grouplist !!},
        selectList: {!! $selectList !!},
    });
</script>
