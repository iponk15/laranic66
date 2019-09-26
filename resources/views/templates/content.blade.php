@extends(!Request::ajax() ? 'templates.layout' : 'templates.layoutajax')

@section('fullcontent')
    @yield('content')
@endsection