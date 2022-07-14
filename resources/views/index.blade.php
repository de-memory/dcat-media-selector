<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group">
            <input name="{{ $name }}" class="form-control {{ $class }}" placeholder="{{ $placeholder }}"
                   {!! $attributes !!} value="{{ $value }}">

            <div class="input-group-append">
                <button class="btn btn btn-success btn-file mr-1"
                        onclick="{{ $elementClass . '_form_upload' }}.click()">
                    <i class="fa fa-folder-open"></i> {{ trans("admin.upload") }}
                    <span class="{{ $elementClass }}_percent_form"></span>
                    <input type="file" id="{{ $elementClass }}_form_upload" style="display: none;"/>
                </button>

                <button type="button" class="btn btn btn-primary btn-file {{ $elementClass }}_form_modal_button">
                    <i class="fa fa-folder-open"></i> {{ trans('admin.choose') }}
                </button>
            </div>
        </div>

        @include('admin::form.help-block')

        <ul class="d-flex flex-wrap list-inline plupload-preview {{ $elementClass }}_media_display">
        </ul>
    </div>
</div>

<script>
    $().MediaSelector({
        formId: '{{$formId}}',
        label: '{!! $label !!}',
        class: '{{ $elementClass }}',
        value: "{{$value}}",
        config: {!! $config !!},
        grouplist: {!! $grouplist !!},
        selectList: {!! $selectList !!},
    })
</script>
