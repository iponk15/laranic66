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
        <form class="kt-form kt-form--label-right form_edit" action="{{ $url . '/update/' . $id }}" id="kt_form_1" method="post" data-confirm="1" onsubmit="return submitMe()">
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
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-form-label col-lg-4 col-sm-12">Role Name <span class="kt-font-danger kt-font-bold">*</span> </label>
                            <div class="col-lg-8 col-md-9 col-sm-12">
                                <input type="text" class="form-control" name="role_name" placeholder="Enter role name" autocomplete="off" value="{{ $records[0]->role_name }}">
                                <span class="form-text text-muted"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-4 col-sm-12">Role Description</label>
                            <div class="col-lg-8 col-md-9 col-sm-12">
                                <textarea name="role_description" class="form-control" cols="30" rows="5">{{ $records[0]->role_description }}</textarea>
                                <span class="form-text text-muted"></span>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div id="m_tree_3" class="tree-demo"> </div>
                    </div>
                    <input type="hidden" name="val_jstree" value="" id="val_jstree">
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
        function submitMe(){
            var result = $('#m_tree_3').jstree('get_selected', true);
            var resultIds = [];
            $.each(result, function() {
                resultIds.push(
              {
                id: this.id,
                parent: this.parent == '#' ? this.id : this.parent
              }
             )
            });
            $('#val_jstree').val(JSON.stringify(resultIds) == '[]' ? '' : JSON.stringify(resultIds));
        }

        $(document).ready(function () {
            // set form validation
            var role_id = '{{$id}}';
            var rules   = { role_name : { required: true } };
            var message = {};

            global.init_form_validation('.form_edit',rules,message);

            $("#m_tree_3").jstree({
                plugins: ["wholerow", "checkbox", "types"],
                core: {
                    themes: {
                        responsive: !1
                    },
                    data : {
                      'url' : base_url+'/roles/preview_menu/'+role_id+'?operation=get_node',
                      'data' : function (node) {
                        return { 'id' : node.id };
                      },
                      "dataType" : "json"
                    }
                    ,'check_callback' : true,
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
        })
    </script>
@endsection