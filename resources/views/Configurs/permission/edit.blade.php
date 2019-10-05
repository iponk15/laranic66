@extends('templates.content')
@section('content')
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {{ $pagetitle }}
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="dropdown dropdown-inline">
                        <a href="{{ route($route.'.edit',['permission' => $id]) }}" class="btn btn-secondary btn-sm btn-icon btn-elevate btn-elevate-air ajaxify"><i class="fa fa-sync"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form kt-form--label-right form_edit" action="{{ route($route.'.update', ['permission' => $id]) }}" id="kt_form_1" method="POST" data-confirm="1">
            {{csrf_field()}}
            @method('PUT')
            <div class="kt-portlet__body">
                <div class="form-group form-group-last kt-hide">
                    <div class="alert alert-danger" role="alert" id="kt_form_1_msg">
                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                        <div class="alert-text">
                            Oh snap! Change a few things up and try submitting again.
                        </div>
                        <div class="alert-close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="la la-close"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Module <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <select name="module_menu_id" class="form-control module_menu_id">
                            <option value="{{ $records[0]->menu_id }}">{{ $records[0]->menu_nama }}</option>
                        </select>
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="formPermission">
                    <div class="form-group  row">
                        <label class="col-form-label col-lg-2 col-sm-12">List Permission </label>
                        <div data-repeater-list="permission" class="col-lg-9">     
                        @foreach ($records as $rows)
                            <div data-repeater-item class="row kt-margin-b-10">
                                <div class="col-lg-4 col-md-3 col-sm-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-code"></i>
                                            </span>
                                        </div>
                                        <input value="{{ $rows->permin_id }}" type="hidden" name="permission_id">
                                        <input value="{{ $rows->module_id }}" type="hidden" name="permission_module_id">
                                        <input value="{{ $rows->permin_name }}" required type="text" name="permission_name" class="form-control form-control-danger" placeholder="Input permission name ...">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <a href="javascript:;" data-repeater-delete="" class="btn btn-danger btn-icon">
                                        <i class="la la-remove"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2"></div>
                        <div class="col">
                            <div data-repeater-create="" class="btn btn btn-primary">
                                <span>
                                    <i class="la la-plus"></i>
                                    <span>Add</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-10 ml-lg-auto">
                            <a class="btn btn-secondary ajaxify" href="{{ route($route.'.index') }}">Back</a>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
    <a href="{{ route($route.'.edit', ['permission' => $id]) }}" class="reload ajaxify"></a>
    <script>
        $(document).ready(function () {
            // set form validation
            var rules   = { module_menu_id : { required: true } };
            var message = {};


            global.init_form_validation('.form_edit',rules,message);

            swalParam = {
                title             : 'Are you sure ?',
                text              : 'You want to delete this form',
                type              : 'warning',
                showCancelButton  : true,
                confirmButtonText : 'Yes, delete it!'
            };

            swalResult = {
                title : 'Deleted',
                text  : 'Your form has been deleted',
                type  : 'success'
            }

            global.init_form_repeater('.formPermission',swalParam, swalResult);

            var urlSlct = '{{ route("configurs.fetch.sample") }}';
            global.init_select2('.module_menu_id',urlSlct,'Select Module',false,true);
        })
    </script>
@endsection