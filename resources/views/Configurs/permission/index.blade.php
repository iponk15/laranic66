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
                        <div class="dropdown dropdown-inline">
                            @can('permission-create')
                                <a href="{{ route($route.'.create') }}" class="btn btn-success btn-icon-sm btn-sm ajaxify btn-elevate btn-elevate-air" >
                                    <i class="flaticon2-plus"></i> Add Data
                                </a>
                            @endcan
                            <a href="{{ route($route.'.index') }}" class="btn btn-secondary btn-sm btn-icon btn-elevate btn-elevate-air ajaxify"><i class="fa fa-sync"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">

                <!--begin: Search Form -->
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="row align-items-center">
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <div class="kt-input-icon kt-input-icon--left">
                                        <input type="text" class="form-control generalSearch" placeholder="Search...">
                                        <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                            <span><i class="la la-search"></i></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <div class="kt-input-icon kt-input-icon--left">
                                        <select name="" class="module_status" style="width: 40%;">
                                            <option value="">Pilih data</option>
                                            <option value="0">InActive</option>
                                            <option value="1">Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end: Search Form -->
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">

                <!--begin: Datatable -->
                <div class="user_datatable"></div>

                <!--end: Datatable -->
            </div>
        </div>
    </div>
    <a href="{{ route($route.'.index') }}" class="reload ajaxify"></a>
    <script>
        $(document).ready(function () {

            var clas   = ".user_datatable";
            var urll   = "{{ route($route.'.table') }}";
            var column = [
                { field: "no",title: "No.",width: 30,textAlign: 'center',sortable:!1},
                { field: "module_name",title: "Nama Module",filterable: true,width: 200},
                { field: "module_status",title: "Status",filterable: true,width:100},
                { field: "name",title: "Createdby",filterable: true,width:100,textAlign: 'center'},
                { field: "updated_at",title: "Updatedat",filterable: true,width:190,textAlign: 'center'},
                { field: "action", title: "Action",width: 100,textAlign: 'center',sortable:!1}
            ];

            var cari = {generalSearch :'.generalSearch', role_name : '.role_name', module_status : '.module_status'};
            global.init_datatable(clas, urll, column, cari);

            $('.module_status').select2({
                placeholder : "Select Status",
                allowClear  : true
            });
        });
    </script>
@endsection