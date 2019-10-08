<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Auth;

class Dashboard extends Controller
{
    private $url       = 'dasboard';
    private $pagetitle = 'Data Dashboard';

    public function __construct(){
        $this->middleware('auth');
    }

    function index(){

        $data['titlehead']  = 'Halaman Dashboar';
        $data['pagetitle']  = $this->pagetitle;
        $data['breadcrumb'] = ['Index' => url('/')];
        $data['redisData']  = json_decode(Redis::get('laranic_ses'));


        return view('dashboard/index', $data);
    }
}
