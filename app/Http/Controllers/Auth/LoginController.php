<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function actionlogin(Request $request)
    {
        $auth = Auth::attempt($request->only('email', 'password'));
        
        if($auth){
            $request->session()->put( 'laranic_ses', Auth::user() );
            $data['status']  = 1;
        }else{
            $data['status']  = 0;
            $data['flag']    = 'danger';
            $data['message'] = 'Akun yang anda masukan tidak sesuai';
        }
        
        echo json_encode($data);
    }

    public function logout()
    {
    	Auth::logout();
    	return redirect('/login');
    }
}
