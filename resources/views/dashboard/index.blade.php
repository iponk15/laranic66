@extends('templates.content')
@section('content')
    <!--Begin::Row-->
    Dashboar page <br> 
    With user data session user_id : {{ Auth::id() }} <br>
    With Data Redis <pre> {{ print_r($redisData) }} </pre>
    <!--End::Row-->
@endsection