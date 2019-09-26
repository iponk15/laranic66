@extends('templates.content')
@section('content')
    <!--Begin::Row-->
    Dashboar page <br> 
    With user data session user_id : {{ Auth::id() }}
    <!--End::Row-->
@endsection