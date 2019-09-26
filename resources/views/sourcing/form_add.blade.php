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
                        <a href="{{ $url . '/create' }}" class="btn btn-secondary btn-sm btn-icon btn-elevate btn-elevate-air ajaxify"><i class="fa fa-sync"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form kt-form--label-right form_add" action="{{ $url . '/store' }}" id="kt_form_1" method="post">
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
                    <label class="col-form-label col-lg-2 col-sm-12">Invitation Number <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <input id="kt_inputmask_1" type="text" class="form-control" name="sourcing_no_inv" placeholder="Enter invitation number" autocomplete="off">
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Title <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <input type="text" class="form-control" name="sourcing_title" placeholder="Enter title">
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Start - End Date </label>
                    <div class="col-lg-3 col-md-3 col-sm-12 searchRangeDate">
                        <input readonly type="text" class="form-control" name="range_date" placeholder="Enter range start - end date" autocomplete="off">
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Category <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <select name="sourcing_category" class="form-control selectSource">
                            <option value=""></option>
                            <option value="1">IT</option>
                            <option value="2">Marketing</option>
                            <option value="3">Akunting</option>
                        </select>
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Sub Category <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <select name="sourcing_subcategori" class="form-control selectSource">
                            <option value=""></option>
                            <option value="1">Open Sourcing</option>
                            <option value="2">Progress Sourcing</option>
                            <option value="3">Closed Sourcing</option>
                        </select>
                        <span class="form-text text-muted"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">Type Status <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                         <select name="sourcing_type" class="form-control selectSource">
                            <option value=""></option>
                            <option value="1">Publish</option>
                            <option value="2">Open for Joining</option>
                            <option value="3">Closed</option>
                        </select>
                        <span class="form-text text-muted"></span>
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
    <a href="{{ $url . '/create' }}" class="reload ajaxify"></a>
    <script>
        $(document).ready(function () {
            // set form validation
            var rules   = { 
                sourcing_no_inv      : { required: true },
                sourcing_title       : { required: true },
                sourcing_category    : { required: true },
                sourcing_subcategori : { required: true },
                sourcing_type        : { required: true },
            };
            var message = {};

            global.init_form_validation('.form_add',rules,message);

            $('.selectSource').select2({
                placeholder : 'Select Option',
                allowClear  : true
            });

            var prm = {
                autoclose   : true,
                format      : "DD/MM/YYYY",
                orientation : "bottom left"
            }

            global.init_dtrp(4, '.searchRangeDate', prm);

            $("#kt_inputmask_1").inputmask("EPROC-AAA/9999/99/AA/9999");
        });
    </script>
@endsection