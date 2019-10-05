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
                        <a href="{{ route($route.'.create') }}" class="btn btn-secondary btn-sm btn-icon btn-elevate btn-elevate-air ajaxify"><i class="fa fa-sync"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form kt-form--label-right form_add" action="{{ route($route.'.store') }}" id="kt_form_1" method="POST">
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
                    <label class="col-form-label col-lg-2 col-sm-12">Role Name <span class="kt-font-danger kt-font-bold">*</span> </label>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <input type="text" class="form-control" name="name" placeholder="input name ...">
                        <span class="form-text text-muted"></span>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-2 col-sm-12">  </label>
                    <div class="col-lg-9 col-md-3 col-sm-12">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th width="70%"><center><b>List Permission</b></center></th>
                                    <th width="30%"><center><b>List Menu</b></center></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="row">
                                            @if(empty($permission))
                                                <div class="alert alert-danger" role="alert">
                                                    <strong>Sorry!</strong> Permission data is empty
                                                </div>
                                            @else
                                                @foreach($permission as $rows)
                                                    <div class="col-lg-3">
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th style="background-color: lightgray;">
                                                                        <div class="kt-checkbox-list">
                                                                            <label class="kt-checkbox kt-checkbox--success">
                                                                                <input type="checkbox" class="checkAll" value="{{ $rows['menu_nama'] }}"> <b>{{ $rows['menu_nama'] }}</b>
                                                                                <span></span>
                                                                            </label>
                                                                        </div>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($rows['list_permission'] as $params)
                                                                    <tr>
                                                                        <td width="20%" align="center">
                                                                            <div class="kt-checkbox-list">
                                                                                <label class="kt-checkbox kt-checkbox--success">
                                                                                    <input class="child-{{ $params->menu_nama }}" type="checkbox" name="permission[]" value="{{ $params['permin_id'] }}"> {{ $params['permin_name'] }}
                                                                                    <span></span>
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div id="m_tree_3" class="tree-demo"> </div>
                                        <input type="hidden" name="val_jstree" value="" id="val_jstree">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <span class="form-text text-muted"></span>
                    </div>
                </div>        
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-10 ml-lg-auto">
                            <a class="btn btn-secondary ajaxify" href="{{ route($route.'.index') }}">Back</a>
                            @if(!empty($permission))
                                <button type="submit" class="btn btn-success">Submit</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
    <a href="{{ route($route.'.create') }}" class="reload ajaxify"></a>
    <script>
        $(document).ready(function () {
            // hirarki menu
            $("#m_tree_3").jstree({
                plugins: ["wholerow", "checkbox", "types"],
                core: {
                    themes: {
                        responsive: !1
                    },
                    data : {
                        // 'url'  : base_url+'/roles/preview_menu?operation=get_node',
                        url : "{{ route($route.'.preview_menu') }}" + '?operation=get_node',
                        'data' : function (node) {
                            return { 'id' : node.id };
                        },
                        "dataType" : "json"
                    },
                    'check_callback' : true,
                    'themes' : {
                        'responsive' : false
                    }
                },
                types: {
                    default: {
                        icon: "fa fa-folder m--font-warning"
                    },
                    file: {
                        icon: "fa fa-file  m--font-warning"
                    }
                }
            });

            // function checkall checkbox permission
            $('.checkAll').on('change', function(){
                var value = $(this).val();
                if($(this).prop("checked") == true){
                    $('.child-'+value).prop('checked', true);
                }else if($(this).prop("checked") == false){
                    $('.child-'+value).prop('checked', false);
                }
            });

            // set form validation
            var rules   = { name : { required: true } };
            var message = {};

            global.init_form_validation('.form_add',rules,message);
        })
    </script>
@endsection