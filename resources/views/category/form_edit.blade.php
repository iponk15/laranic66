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
                        <a href="{{ $url . '/edit/' . $id }}" class="btn btn-secondary btn-sm btn-icon btn-elevate btn-elevate-air ajaxify"><i class="fa fa-sync"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form kt-form--label-right form_edit" action="{{ $url . '/update/' . $id }}" id="kt_form_1" method="post" data-confirm="1">
            {{csrf_field()}}
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
                    <label class="col-form-label col-lg-2 col-sm-12">Category Code <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <input type="text" class="form-control" name="category_code" placeholder="Enter category code" tavindex="1" value="{{ $records[0]->category_code }}">
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Category Name <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <input type="text" class="form-control" name="category_name" placeholder="Enter category name" tavindex="2" value="{{ $records[0]->category_name }}">
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="formSubCategory">
                    <div class="form-group  row">
                        <label class="col-form-label col-lg-2 col-sm-12">Sub Category :</label>
                        <div data-repeater-list="sub_category" class="col-lg-9">
                            <?php 
                                foreach ($records as $rows) {
                            ?>
                                    <div data-repeater-item class="row kt-margin-b-10">
                                        <div class="col-lg-4 col-md-3 col-sm-12">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-code"></i>
                                                    </span>
                                                </div>
                                                <input required type="text" name="subcat_code" class="form-control form-control-danger" placeholder="Sub category code" value="{{ $rows->subcat_code }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-3 col-sm-12">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-envelope-open-text"></i>
                                                    </span>
                                                </div>
                                                <input required type="text" name="subcat_name" class="form-control form-control-danger" placeholder="Sub category name" value=" {{ $rows->subcat_name }} ">
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <a href="javascript:;" data-repeater-delete="" class="btn btn-danger btn-icon">
                                                <i class="la la-remove"></i>
                                            </a>
                                        </div>
                                    </div>
                            <?php } ?>
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
                            <a class="btn btn-secondary ajaxify" href="{{ $url }}">Back</a>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
    <a href="{{ $url . '/edit/' . $id }}" class="reload ajaxify"></a>
    <script>
        $(document).ready(function () {
            // set form validation
            var rules   = { 
                category_code : { required: true, maxlength : 2 },
                category_name : { required: true }
            };
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

            global.init_form_repeater('.formSubCategory',swalParam, swalResult);
        });
    </script>
@endsection