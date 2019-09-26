<div class="kt-subheader  kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <a href="{{ url('/')}}" class="ajaxify">
                <h3 class="kt-subheader__title"> Home </h3>
            </a>
            <div class="kt-subheader__breadcrumbs">
                @if (!empty($breadcrumb))
                    @foreach ($breadcrumb as $key => $row)
                        <div style="margin-right: 8px">
                            @if($row == null)
                            <span class="kt-subheader__breadcrumbs-link">{{ $key }}</span>
                            @else
                                <i class="flaticon2-next" style="margin-right: 8px"></i>
                                <a href="{{ $row }}" class="kt-subheader__breadcrumbs-link ajaxify">{{ $key }}</a>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div style="margin-right: 8px">
                        <span class="kt-subheader__breadcrumbs-link">Please add some BREADCRUMB</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>