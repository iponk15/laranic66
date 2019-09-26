<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class bloked extends Controller
{
    function index(){
        $data['titlehead']  = 'Page Bloked - User have not permission for this page access.';
        $data['pagetitle']  = '';
        $data['breadcrumb'] = ['Index' => url('Page404')];
        return view('templates/404',$data);
    }
}
