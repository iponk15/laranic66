<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Validator;
use App\User;
use Auth;
use DB;

class UsersController extends Controller
{
    private $url       = 'users';
    private $titlehead = 'Page User Data';
    private $table     = 'users';
    private $route     = 'users';

    function __construct(){
        //  $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlehead']  = $this->titlehead;
        $data['pagetitle']  = 'Tabel Users';
        $data['breadcrumb'] = [ $this->titlehead => route($this->route.'.index') ];
        $data['url']        = route($this->route.'.index');
		$data['route']      = $this->route;
		
        
        return view ($this->route.'.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlehead']  = $this->titlehead;
        $data['pagetitle']  = 'Tabel User';
        $data['breadcrumb'] = ['Index' => route($this->route.'.index'), 'Form Add User' => route($this->route.'.create')];
        $data['route']      = $this->route;
        $data['roles']      = Role::pluck('name','name')->all();

        return view($this->route.'.create', $data);
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
        $data['titlehead']  = $this->titlehead;
        $data['pagetitle']  = 'Form Edit';
        $data['breadcrumb'] = ['Index' => route($this->route.'.index'), 'Form Edit User' => route($this->route.'.edit', ['user' => $id])];
        $data['route']      = $this->route;
        $data['id']         = $id;
        $data['records']    = User::selectRaw('name,email')->where('id', Hashids::decode($id)[0])->get();

        return view($this->route.'.edit', $data);
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
	
	function table(Request $param){
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

            $textRole = '';
            foreach ($value->getRoleNames() as $role){
                $textRole .= '<label for="" class="badge badge-info">'.$role.'</label>';
            }
    
            $data['records'][] = [
                'no'         => $i++,
                'name'       => $value->name,
                'email'      => $value->email,
                'role'       => $textRole,
                'updated_at' => $value->updated_at,
                'action'     => '<a href="'.route($this->route.'.edit', ['id' => Hashids::encode($value->id)] ).'" class="btn btn-primary btn-icon btn-sm ajaxify"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pen"></i></a>&nbsp
								 <a href="'.route($this->route.'.destroy', ['id' => Hashids::encode($value->id)] ).'" class="btn btn-danger btn-icon btn-sm"  onClick="return f_status(2, this, event)" data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-alt"></i></a>&nbsp'
            ];
        }

        $encode = (object)[
            'meta' => ['page' => $start, 'pages' => null, 'perpage' => $limit, 'total' => $jumlah, 'sort' => 'asc', 'field' => 'id'],
            'data' =>  $data['records']
        ];

        echo json_encode($encode);
    }
}
