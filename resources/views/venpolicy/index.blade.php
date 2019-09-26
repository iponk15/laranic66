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
                            <a href="{{ $url.'/create' }}" class="btn btn-success btn-icon-sm btn-sm ajaxify btn-elevate btn-elevate-air" >
                                <i class="flaticon2-plus"></i> Add Data
                            </a>
                            <a href="{{ $url }}" class="btn btn-secondary btn-sm btn-icon btn-elevate btn-elevate-air ajaxify"><i class="fa fa-sync"></i></a>
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
                                        <input type="text" class="form-control generalSearch" placeholder="Search by title">
                                        <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                            <span><i class="la la-search"></i></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <div class="kt-input-icon kt-input-icon--left">
                                        <select name="" class="venpol_type" style="width: 60%;">
                                            <option value=""></option>
                                            <option value="1">General</option>
                                            <option value="2">Open Sourcing</option>
                                            <option value="3">Open Bidding</option>
                                            <option value="4">Direct Selection</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 order-1 order-xl-2 kt-align-right">
                            <a href="#" class="btn btn-default kt-hidden">
                                <i class="la la-cart-plus"></i> New Order
                            </a>
                            <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg d-xl-none"></div>
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
    <a href="{{ $url }}" class="reload ajaxify"></a>
    <script>
        $(document).ready(function () {

            var clas   = ".user_datatable";
            var urll   = "{{ url($url.'/select') }}";
            var column = [
                { field: "no",title: "No.",width: 30,textAlign: 'center',sortable:!1},
                { field: "venpol_type",title: "Type",filterable: true,width: 100,textAlign: 'center'},
                { field: "venpol_title",title: "Title",filterable: true,width:150},
                { field: "venpol_status",title: "Status",filterable: true,width:80,textAlign: 'center'},
                { field: "venpol_createddate",title: "Created On",filterable: true,width:180,textAlign: 'center'},
                { field: "venpol_createdby",title: "Created by",filterable: true,width:180,textAlign: 'center'},
                { field: "action", title: "Action",width: 120,textAlign: 'center',sortable:!1}
                
            ];

            var cari = {generalSearch :'.generalSearch', venpol_type : '.venpol_type'};
            global.init_datatable(clas, urll, column, cari);

            $('.venpol_type').select2({
                placeholder : "Select Type",
                allowClear : true
            });
        });
    </script>
@endsection