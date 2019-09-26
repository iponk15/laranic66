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
                    <label class="col-form-label col-lg-2 col-sm-12">Policy Type <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <select autofocus name="venpol_type" class="venpol_select form-control" tavindex="1" >
                            <option value=""></option>
                            <option {{ ($records[0]->venpol_type == "1" ? "selected" : "") }} value="1">General</option>
                            <option {{ ($records[0]->venpol_type == "2" ? "selected" : "") }} value="2">Open Sourcing</option>
                            <option {{ ($records[0]->venpol_type == "3" ? "selected" : "") }} value="3">Open Bidding</option>
                            <option {{ ($records[0]->venpol_type == "4" ? "selected" : "") }} value="4">Direct Selection</option>
                        </select>
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Policy Title <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <input type="text" class="form-control" name="venpol_title" placeholder="Enter policy title" tavindex="2" value="{{ $records[0]->venpol_title }}">
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Status <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <select name="venpol_status" class="form-control venpol_select" tavindex="4">
                            <option value=""></option>
                            <option {{ ($records[0]->venpol_status == "1" ? "selected" : "") }} value="1">Active</option>
                            <option {{ ($records[0]->venpol_status == "0" ? "selected" : "") }} value="0">Inactive</option>
                        </select>
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Content</label>
                    <div class="col-lg-8 col-md-3 col-sm-12">
                        <textarea class="form-control" id="venpol_content" name="venpol_content">{{ $records[0]->venpol_content }}</textarea>
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-10 ml-lg-auto">
                            <a class="btn btn-secondary ajaxify" href="{{ $url }}" tavindex="6">Back</a>
                            <button type="submit" class="btn btn-success" tavindex="5">Submit</button>
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
                venpol_type   : { required: true },
                venpol_title  : { required: true },
                venpol_status : { required: true }
            };
            var message = {};

            global.init_form_validation('.form_edit',rules,message);

            $('.venpol_select').select2({
                placeholder : "Select Option",
                allowClear : true
            });

            prmSummer = { height: 400 };
            global.init_summernote('#venpol_content',prmSummer);
        });
    </script>
@endsection