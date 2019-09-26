<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    private $url       = 'dasboard';
    private $pagetitle = 'Data Dashboard';

    public function __construct(){
        $this->middleware('auth');
    }

    function index(){
        $data['titlehead'] = 'Halaman Dashboar';
        $data['pagetitle'] = $this->pagetitle;
        $data['breadcrumb'] = ['Index' => url('/')];
        // dd(Auth::user());
        return view('dashboard/index', $data);
    }
}
