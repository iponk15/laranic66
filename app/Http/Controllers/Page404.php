<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Page404 extends Controller
{
    function index(){
        $data['titlehead'] = 'Page 404';
        $data['pagetitle'] = '';
        $data['breadcrumb'] = ['Index' => url('Page404')];
        return view('templates/404',$data);
    }
}
