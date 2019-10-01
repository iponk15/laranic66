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

        <div class="kt-portlet__body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#kt_tabs_1_1">
                        <i class="la la-user"></i> Change Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#kt_tabs_1_2">
                        <i class="la la-key"></i> Change Password
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="kt_tabs_1_1" role="tabpanel">
                    <!--begin::Form-->
                    <form class="kt-form kt-form--label-right form_profile" action="{{ $url . '/changeProfile/' . Hashids::encode($id) }}" id="kt_form_1" method="post" data-confirm="1">
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
                                <label class="col-form-label col-lg-2 col-sm-12">Name <span class="kt-font-danger kt-font-bold">*</span> </label>
                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <input type="text" class="form-control" name="name" placeholder="Enter role name" autocomplete="off" value="{{$get->name}}">
                                    <span class="form-text text-muted"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2 col-sm-12">Email <span class="kt-font-danger kt-font-bold">*</span></label>
                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <input type="text" class="form-control" name="email" placeholder="Enter email" autocomplete="off" value="{{$get->email}}">
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
                <div class="tab-pane" id="kt_tabs_1_2" role="tabpanel">
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
                                <label class="col-form-label col-lg-3 col-sm-12">Old Password <span class="kt-font-danger kt-font-bold">*</span></label>
                                <div class="col-lg-5 col-md-3 col-sm-12">
                                    <input type="password" class="form-control" name="old_password" placeholder="Enter Old Password" autocomplete="off">
                                    <span class="form-text text-muted"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3 col-sm-12">New Password <span class="kt-font-danger kt-font-bold">*</span></label>
                                <div class="col-lg-5 col-md-3 col-sm-12">
                                    <input type="password" class="form-control" name="new_password" placeholder="Enter New Password" autocomplete="off">
                                    <span class="form-text text-muted"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3 col-sm-12">Confirm New Password <span class="kt-font-danger kt-font-bold">*</span></label>
                                <div class="col-lg-5 col-md-3 col-sm-12">
                                    <input type="password" class="form-control" name="new_password_confirm" placeholder="Re-enter New Password" autocomplete="off">
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
            </div>
        </div>

    </div>
    <a href="{{ $url . '/profile' }}" class="reload ajaxify"></a>
    <script>
        $(document).ready(function () {
            // set form validation
            var rules   = { name        : { required: true },
                            email       : { required: true, email: true },
                          };
            var message = {};

            global.init_form_validation('.form_profile',rules,message);
        })
    </script>
@endsection