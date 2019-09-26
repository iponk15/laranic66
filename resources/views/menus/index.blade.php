@extends('templates.content')
@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="alert alert-light alert-elevate" role="alert">
            <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
            <div class="alert-text">
                The Metronic Datatable component supports local or remote data source. For remote data you can specify a remote data source that returns data in JSON/JSONP format. In this example the grid fetches its data from a remote JSON file.
                It also defines the schema model of the data source received from the remote data source. In addition to the visualization, the Datatable provides built-in support for operations over data such as sorting, filtering and paging
                performed in user browser(frontend).
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon2-line-chart"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        {{ $pagetitle }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <form class="kt-form kt-form--label-right form_add" action="{{url('menus_store')}}" id="kt_form_1" method="post">
                    {{csrf_field()}}
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group row" style="margin-bottom:0rem">
                                    <div class="col-lg-12">
                                        <label>New Parent:</label>
                                        <input type="text" class="form-control" placeholder="Enter new parent of menu" name="menu_nama" autocomplete="off">
                                        <span class="form-text text-muted">Please enter new parent of your menu</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12" align="right">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-folder-plus"></i> Add</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-5 col-sm-5">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head">
                                        <div class="kt-portlet__head-label">
                                            <span class="kt-portlet__head-icon">
                                                <i class="kt-font-brand flaticon-layers"></i>
                                            </span>
                                            <h3 class="kt-portlet__head-title">
                                                Custom menu
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__body" style="max-height: 300px;overflow-y: auto;" id="tree_menu">
                                        <ul>
                                          
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>            
            </div>
        </div>
    </div>

    <div class="modal fade" id="m_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body" id="menu_detail">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-update">Update</button>
                </div>
            </div>
        </div>
    </div>
    <a href="{{url($url_page)}}" class="reload ajaxify"></a>
    <script type="text/javascript">
        function customMenu(node)
        {
            var tree = $("#tree_menu").jstree(true);
            var items = {
                'Create' : {
                    'label' : 'Create Submenu',
                    'action' : function (obj) {node = tree.create_node(node);tree.edit(node);}
                },
                'Rename' : {
                    'label' : 'Rename',
                    'action' : function (obj) {tree.edit(node);}
                },
                'Delete' : {
                    'label' : 'Delete',
                    'action' : function (obj) {
                        var child = node.children.length;
                        if (child > 0) {
                            swal.fire({
                                title: "Are you sure?",
                                text: 'Submenu in this menu will be delete too!',
                                html: false,
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: '#DD6B55',
                                confirmButtonText : 'Yes'
                            }).then(function(isConfirm){
                                if(isConfirm.value == true){
                                    tree.delete_node(node);
                                }
                            });
                        }else{
                            tree.delete_node(node);
                        }
                    }
                },
                'Detail' : {
                    'label' : 'Detail',
                    'action' : function (obj) {
                        $('#exampleModalLabel').text(node.text);
                        var target = base_url+"/menus_detail/"+node.id;
                        $('#menu_detail').html('');
                        $('#m_detail').modal('show');
                        KTApp.block("#m_detail",{});
                        $("#menu_detail").load(target);
                        KTApp.unblock("#m_detail",{});
                    }
                }
            }

            if (node.type === 'level_1') {
                delete items.item2;
            } else if (node.type === 'level_2') {
                delete items.item1;
            }

            return items;
        }

        $(document).ready(function () {
            var rules = {
                menu_nama: { required: true }
            };

            var message = {};
            global.init_form_validation('.form_add',rules,message);
            console.log(base_url+'/menus_preview?operation=get_node');
            $('#tree_menu').jstree({
            'contextmenu' : {
                'items': function (node) {
                    return customMenu(node)
                }
            },
            'core' : {
                'data' : {
                      'url' : base_url+'/menus_preview?operation=get_node',
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
            'plugins' : ['state','contextmenu','wholerow','dnd']
        }).on('create_node.jstree', function (e, data) {
              $.get(base_url+'/menus_preview?operation=create_node', { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
                .done(function (d) {
                  data.instance.set_id(data.node, d.id);
                })
                .fail(function () {
                  data.instance.refresh();
                });
            }).on('rename_node.jstree', function (e, data) {
              $.get(base_url+'/menus_preview?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
                .fail(function () {
                  data.instance.refresh();
                });
            }).on('delete_node.jstree', function (e, data) {
              $.get(base_url+'/menus_preview?operation=delete_node', { 'id' : data.node.id })
                .fail(function () {
                  data.instance.refresh();
                });
            }).bind("move_node.jstree", function(e, data) {
              $.get(base_url+'/menus_preview?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent, 'position' : data.position, 'old_position' : data.old_position})
                .fail(function () {                     
                    swal.fire({title: "Failed!", text: "Connection error!", type: "error"}).then(function(){ 
                       data.instance.refresh();
                       }
                    );
                });
            });

            $('#btn-update').on('click', function(){
                KTApp.block("#m_detail",{});
                var href = $('.form_detail').attr('action');
                $.ajax({
                    type     : "POST",
                    cache    : false,
                    url      : href,
                    data     : $('.form_detail').serialize(),
                    success  : function(res) {
                        var datatrim = JSON.parse(res);
                        if (datatrim.status == 1) {
                            KTApp.unblock("#m_detail",{});
                            toastr.success(datatrim.message, 'Berhasil!');
                            $('#m_detail').modal('hide');
                            $('.reload').trigger('click');
                        } else if(datatrim.status == 0) {
                            KTApp.unblock("#m_detail",{});
                            toastr.error(datatrim.message, 'Gagal!')
                        }
                    }
                });
            });
        });
    </script>
@endsection