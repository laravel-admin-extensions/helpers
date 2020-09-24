<div class="card card-@color card-outline">
    <div class="card-header with-border">
        <h3 class="card-title">Scaffold</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">

        <form method="post" action="{{$action}}" id="scaffold" pjax-container>

            <div class="card-body">

                <div class="form-horizontal">

                    <div class="form-group row">

                        <label for="inputTableName" class="col-2 control-label">Table name</label>

                        <div class="col-4">
                            <input type="text" name="table_name" class="form-control" id="inputTableName"
                                   placeholder="table name" value="{{ old('table_name') }}">
                        </div>

                        <span class="help-block hide" id="table-name-help">
                        <i class="fa fa-info"></i>&nbsp; Table name can't be empty!
                    </span>

                    </div>
                    <div class="form-group row">
                        <label for="inputModelName" class="col-2 control-label">Model</label>

                        <div class="col-4">
                            <input type="text" name="model_name" class="form-control" id="inputModelName"
                                   placeholder="model" value="{{ old('model_name', "App\\Models\\") }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputControllerName" class="col-2 control-label">Controller</label>

                        <div class="col-4">
                            <input type="text" name="controller_name" class="form-control" id="inputControllerName"
                                   placeholder="controller"
                                   value="{{ old('controller_name', "App\\Admin\\Controllers\\") }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-2 col-10">
                            <div class="checkbox">

                            <span class="icheck-@color">
                                <input type="checkbox" checked value="migration" id="@id" name="create[]"/>
                                <label for="@id"> Create migration</label>
                            </span>

                                <span class="icheck-@color">
                                <input type="checkbox" checked value="model" id="@id" name="create[]"/>
                                <label for="@id"> Create model</label>
                            </span>

                                <span class="icheck-@color">
                                <input type="checkbox" checked value="controller" id="@id" name="create[]"/>
                                <label for="@id">Create controller</label>
                            </span>

                                <span class="icheck-@color">

                                <input type="checkbox" checked value="migrate" id="@id" name="create[]"/>
                                    <label for="@id">Run migrate
                            </label>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                <hr/>

                <h4>Fields</h4>

                <table class="table table-hover" id="table-fields">
                    <tbody>
                    <tr>
                        <th style="width: 200px">Field name</th>
                        <th>Type</th>
                        <th>Nullable</th>
                        <th>Key</th>
                        <th>Default value</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>

                    @if(old('fields'))
                        @foreach(old('fields') as $index => $field)
                            <tr>
                                <td>
                                    <input type="text" name="fields[{{$index}}][name]" class="form-control"
                                           placeholder="field name" value="{{$field['name']}}"/>
                                </td>
                                <td>
                                    <select style="width: 200px" name="fields[{{$index}}][type]">
                                        @foreach($dbTypes as $type)
                                            <option
                                                value="{{ $type }}" {{$field['type'] == $type ? 'selected' : '' }}>{{$type}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="checkbox"
                                           name="fields[{{$index}}][nullable]" {{ \Illuminate\Support\Arr::get($field, 'nullable') == 'on' ? 'checked': '' }}/>
                                </td>
                                <td>
                                    <select style="width: 150px" name="fields[{{$index}}][key]">
                                        {{--<option value="primary">Primary</option>--}}
                                        <option value="" {{$field['key'] == '' ? 'selected' : '' }}>NULL</option>
                                        <option value="unique" {{$field['key'] == 'unique' ? 'selected' : '' }}>Unique
                                        </option>
                                        <option value="index" {{$field['key'] == 'index' ? 'selected' : '' }}>Index
                                        </option>
                                    </select>
                                </td>
                                <td><input type="text" class="form-control" placeholder="default value"
                                           name="fields[{{$index}}][default]" value="{{$field['default']}}"/></td>
                                <td><input type="text" class="form-control" placeholder="comment"
                                           name="fields[{{$index}}][comment]" value="{{$field['comment']}}"/></td>
                                <td><a class="btn btn-sm btn-danger table-field-remove"><i class="fa fa-trash"></i>
                                        remove</a></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <input type="text" name="fields[0][name]" class="form-control"
                                       placeholder="field name"/>
                            </td>
                            <td>
                                <select style="width: 200px" name="fields[0][type]">
                                    @foreach($dbTypes as $type)
                                        <option value="{{ $type }}">{{$type}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" name="fields[0][nullable]"/>
                            </td>
                            <td>
                                <select style="width: 150px" name="fields[0][key]">
                                    {{--<option value="primary">Primary</option>--}}
                                    <option value="" selected>NULL</option>
                                    <option value="unique">Unique</option>
                                    <option value="index">Index</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control" placeholder="default value"
                                       name="fields[0][default]"></td>
                            <td><input type="text" class="form-control" placeholder="comment" name="fields[0][comment]">
                            </td>
                            <td><a class="btn btn-sm btn-danger table-field-remove"><i class="fa fa-trash"></i>
                                    remove</a></td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                <hr class="mt-0"/>

                <div class='row'>

                    <div class="col-3">
                        <button type="button" class="btn btn-sm btn-success" id="add-table-field"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add field
                        </button>
                    </div>

                    <div class="col-9">
                        <div class='d-inline float-right mt-2'>
                            <span class="icheck-@color">
                                <input type="checkbox" checked name="timestamps" id="@id">
                                <label for="@id">Created_at & Updated_at</label>
                            </span>
                                &nbsp;&nbsp;
                            <span class="icheck-@color">
                                <input type="checkbox" name="soft_deletes" id="@id">
                                <label for="@id"> Soft deletes</label>
                            </span>
                        </div>

                        <div class="d-inline float-right mr-4">
                            <label for="inputPrimaryKey">Primary key</label>
                            <input type="text" name="primary_key" class="form-control d-inline" id="inputPrimaryKey"
                                   placeholder="Primary key" value="id" style="width: 100px;">
                        </div>
                    </div>

                </div>

                {{--<hr />--}}

                {{--<h4>Relations</h4>--}}

                {{--<table class="table table-hover" id="model-relations">--}}
                {{--<tbody>--}}
                {{--<tr>--}}
                {{--<th style="width: 200px">Relation name</th>--}}
                {{--<th>Type</th>--}}
                {{--<th>Related model</th>--}}
                {{--<th>forignKey</th>--}}
                {{--<th>OtherKey</th>--}}
                {{--<th>With Pivot</th>--}}
                {{--<th>Action</th>--}}
                {{--</tr>--}}
                {{--</tbody>--}}
                {{--</table>--}}

                {{--<hr style="margin-top: 0;"/>--}}

                {{--<div class='form-inline margin' style="width: 100%">--}}

                {{--<div class='form-group'>--}}
                {{--<button type="button" class="btn btn-sm btn-success" id="add-model-relation"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add relation</button>--}}
                {{--</div>--}}

                {{--</div>--}}

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-info float-right">submit</button>
            </div>

        {{ csrf_field() }}

        <!-- /.card-footer -->
        </form>

    </div>

</div>


<template>
    <template id="table-field-tpl">
        <tr>
            <td>
                <input type="text" name="fields[__index__][name]" class="form-control" placeholder="field name"/>
            </td>
            <td>
                <select style="width: 200px" name="fields[__index__][type]">
                    @foreach($dbTypes as $type)
                        <option value="{{ $type }}">{{$type}}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="checkbox" name="fields[__index__][nullable]"/></td>
            <td>
                <select style="width: 150px" name="fields[__index__][key]">
                    <option value="" selected>NULL</option>
                    <option value="unique">Unique</option>
                    <option value="index">Index</option>
                </select>
            </td>
            <td><input type="text" class="form-control" placeholder="default value" name="fields[__index__][default]">
            </td>
            <td><input type="text" class="form-control" placeholder="comment" name="fields[__index__][comment]"></td>
            <td><a class="btn btn-sm btn-danger table-field-remove"><i class="fa fa-trash"></i> remove</a></td>
        </tr>
    </template>

    <template id="model-relation-tpl">
        <tr>
            <td><input type="text" class="form-control" placeholder="relation name" value=""></td>
            <td>
                <select style="width: 150px">
                    <option value="HasOne" selected>HasOne</option>
                    <option value="BelongsTo">BelongsTo</option>
                    <option value="HasMany">HasMany</option>
                    <option value="BelongsToMany">BelongsToMany</option>
                </select>
            </td>
            <td><input type="text" class="form-control" placeholder="related model"></td>
            <td><input type="text" class="form-control" placeholder="default value"></td>
            <td><input type="text" class="form-control" placeholder="default value"></td>
            <td><input type="checkbox"/></td>
            <td><a class="btn btn-sm btn-danger model-relation-remove"><i class="fa fa-trash"></i> remove</a></td>
        </tr>
    </template>
</template>

<script require="select2">

    $('select').select2();

    $('#add-table-field').click(function (event) {
        $('#table-fields tbody').append($('#table-field-tpl').html().replace(/__index__/g, $('#table-fields tr').length - 1));
        $('select').select2();
    });

    $('#table-fields').on('click', '.table-field-remove', function (event) {
        $(event.target).closest('tr').remove();
    });

    $('#add-model-relation').click(function (event) {
        $('#model-relations tbody').append($('#model-relation-tpl').html().replace(/__index__/g, $('#model-relations tr').length - 1));
        $('select').select2();
        relation_count++;
    });

    $('#model-relations').on('click', '.model-relation-remove', function (event) {
        $(event.target).closest('tr').remove();
    });

    $('#scaffold').on('submit', function (event) {

        if ($('#inputTableName').val() == '') {
            $('#inputTableName').closest('.form-group').addClass('has-error');
            $('#table-name-help').removeClass('hide');

            return false;
        }

        return true;
    });
</script>
