<?php

namespace App\Http\Controllers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Validator;
use Auth;

use App\User;

class Users extends Controller
{
    private $url       = 'user';
    private $titlehead = 'Page User Data';
    private $table     = 'users';

    public function __construct(){
        DB::enableQueryLog();
        $enc = Hashids::encode(1);
        $dec = Hashids::decode($enc)[0];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlehead'] = $this->titlehead;
        $data['pagetitle'] = 'Tabel Users';
        $data['breadcrumb'] = [$this->titlehead => url($this->url)];
        $data['url']       = url($this->url);
        return view ('user/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    function select(Request $param){
        $post   = $param->input();
        $getDta = User::selectRaw('id, name, email, email_verified_at, created_at, updated_at');
        $paging = $post['pagination'];
        $search = (!empty($post['query']) ? $post['query'] : null);

        if( isset($post['sort']) ){
            $getDta->orderBy($post['sort']['field'], $post['sort']['sort']);
        }else{
            $getDta->orderBy('created_at', 'DESC');
        }

        // proses searching
        if(!empty($search)){
            foreach ($search as $value => $param) {
                if($value === 'generalSearch'){
                    $getDta->whereRaw("(name LIKE '%".$param."%' OR email LIKE '%".$param."%')");
                }else{
                    if($value !== 0 ){
                        $getDta->where($value, $param);
                    }
                }
            }
            $awal = null;
        }

        $start  = $paging['page'];
        $limit  = $paging['perpage'];
        $awal   = ($start == 1 ? '0' : ($start * $limit) - $limit);
        
        $getDta->offset($awal);
        $getDta->limit($limit);
        $result = $getDta->get();

        $jumlah          = count(User::all());
        $data['records'] = array();
        $i               = 1 + $awal;
        $status          = ['0' => 'Inactive', '1' => 'Active'];

        foreach($result as $key => $value){
            $data['records'][] = [
                'no'               => $i++,
                'name'             => $value->name,
                'email'            => $value->email,
                'updated_at'       => $value->updated_at,
                'action'           => '<a href="'.url( 'user/edit/'.Hashids::encode($value->id) ).'" class="btn btn-primary btn-icon btn-sm ajaxify"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pen"></i></a>&nbsp
                                       <a href="'.url( 'user/destroy/'.Hashids::encode($value->id) ).'" class="btn btn-danger btn-icon btn-sm"  onClick="return f_status(2, this, event)" data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-alt"></i></a>&nbsp'
            ];
        }

        $encode = (object)[
            'meta' => ['page' => $start, 'pages' => null, 'perpage' => $limit, 'total' => $jumlah, 'sort' => 'asc', 'field' => 'id'],
            'data' =>  $data['records']
        ];

        echo json_encode($encode);
    }

    public function create()
    {
        $data['titlehead'] = $this->titlehead;
        $data['pagetitle'] = 'Tabel User';
        $data['breadcumb'] = ['Index' => url($this->url), 'Form Add User' => url($this->url.'/add')];
        $data['url']       = url($this->url);

        return view('user/add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post      = $request->input();
        $validator = Validator::make($post, 
            [
                'name'     => 'required',
                'email'    => 'required|email|unique:pgsql.users,email',
                'password' => 'min:8',
            ],
            [
                'name.required' => 'Form user name cannot blank',
            ]
        );

        if ($validator->fails()) {
          
            $error     = '';
            $validator = $validator->errors()->messages();
            foreach ($validator as $key => $value) {
                $error .= $value[0].'<br>';
            }

            $response['status']  = 0;
            $response['message'] = $error;

            echo json_encode($response); 
            return; 
        }

        $user             = new User();
        $post['password'] = bcrypt($post['password']);
        $insert           = $user->fill($post)->save();
        
        if ($insert) {
            $response['status']  = 1;
            $response['message'] = 'Data saved successfully';            
        } else {
            $response['status']  = 0;
            $response['message'] = 'Data failed to save';
        }
        
        echo json_encode($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlehead'] = $this->titlehead;
        $data['pagetitle'] = 'Form Edit';
        $data['breadcumb'] = ['Index' => url($this->url), 'Form Edit User' => url($this->url.'/edit/'.$id)];
        $data['url']       = url($this->url);
        $data['id']        = $id;
        $data['records']   = User::selectRaw('name,email')->where('id', Hashids::decode($id)[0])->get();
        return view('user/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post      = $request->input();
        $user_id   = Hashids::decode($id)[0];
        $validator = Validator::make($post, 
            [
                'name'  => 'required',
                'email' => 'required|email',
            ],
            ['name.required' => 'Form user name cannot blank']
        );

        if ($validator->fails()) {

            $error = '';
            $validator = $validator->errors()->messages();
            foreach ($validator as $key => $value) {
                $error .= $value[0].'<br>';
            }
            $response['status']  = 0;
            $response['message'] = $error;
            echo json_encode($response); return; 
        }

        if ($post['password'] == '') {
            unset($post['password']);
            $user  = User::find($user_id);
            $user->fill($post);
            $query = $user->save();
        } else {
            $user  = User::find($user_id);
            $post['password']   = bcrypt($post['password']);
            $user->fill($post);
            $query = $user->save();
        }

        if ($query) {
            $response['status']  = 1;
            $response['message'] = 'Data udpate successfully';            
        } else {
            $response['status']  = 0;
            $response['message'] = 'Data failed to udpate';
        }
        
        echo json_encode($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_id = Hashids::decode($id)[0];
        $user    = User::find($user_id);
        $delete  = $user->delete();

        if ($delete) {
            $response['status']  = 1;
            $response['message'] = 'Data has been delete';
        } else {
            $response['status']  = 0;
            $response['message'] = 'Data failed to delete';
        }
        
        echo json_encode($response);
    }

    public function profile()
    {
        $data['titlehead'] = $this->titlehead;
        $data['pagetitle'] = 'Profile Page';
        $data['breadcrumb']= ['Index' => url($this->url)];
        $data['url']       = url($this->url);
        $data['id']        = Auth::id();
        $data['get']       = User::find($data['id']);
        return view('user/profile', $data);
    }

    public function changeProfile(Request $request, $id)
    {
        $post = $request->input();
        // dd($post);
        $user_id = Hashids::decode($id)[0];
        $validator = Validator::make($post, 
                                            [
                                                'name' => 'required',
                                                'email' => 'required|email',
                                            ],
                                            ['name.required'=> 'Form user name cannot blank']
                                    );

        if ($validator->fails()) {

            $error = '';
            $validator = $validator->errors()->messages();
            foreach ($validator as $key => $value) {
                $error .= $value[0].'<br>';
            }
            $response['status']  = 0;
            $response['message'] = $error;
            echo json_encode($response); return; 
        }

        $user  = User::find($user_id);
        $user->fill($post);
        $query = $user->save();

        if ($query) {
            $response['status']  = 1;
            $response['message'] = 'Data update successfully';            
        } else {
            $response['status']  = 0;
            $response['message'] = 'Data failed to udpate';
        }
        
        echo json_encode($response);
    }
}
