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

<script init="{{ $selector }}" require="@select-table,@select2?lang={{ config('app.locale') === 'en' ? '' : str_replace('_', '-', config('app.locale')) }},@de-memory.dcat-media-selector">
    new MediaSelector({!! $config !!}, '{{ $locale }}', {!! $lang !!}).init();
</script>
