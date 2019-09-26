<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Mrt_roles;

class Roles extends Controller
{
    private $url       = 'roles';
    private $titlehead = 'Page Roles Data';

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
        $data['pagetitle'] = 'Tabel Roles';
        $data['breadcrumb'] = ['Index' => url($this->url)];
        $data['url']       = url($this->url);

        return view('roles/index', $data);
    }

    function select(Request $param){
        $post   = $param->input();
        $getDta = Mrt_roles::selectRaw('role_id,role_name,role_description,role_status,role_lastupdate');
        $paging = $post['pagination'];
        $search = (!empty($post['query']) ? $post['query'] : null);

        if( isset($post['sort']) ){
            $getDta->orderBy($post['sort']['field'], $post['sort']['sort']);
        }else{
            $getDta->orderBy('role_createddate', 'DESC');
        }

        // proses searching
        if(!empty($search)){
            foreach ($search as $value => $param) {
                if($value === 'generalSearch'){
                    $getDta->whereRaw("(role_name LIKE '%".$param."%' OR role_description LIKE '%".$param."%')");
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

        $jumlah          = count(Mrt_roles::all());
        $data['records'] = array();
        $i               = 1 + $awal;
        $status          = ['0' => 'Inactive', '1' => 'Active'];

        foreach($result as $key => $value){
            $data['records'][] = [
                'no'               => $i++,
                'role_name'        => $value->role_name,
                'role_description' => $value->role_description,
                'role_status'      => '<span class="btn btn-sm btn-label-'.($value->role_status == '0' ? 'danger' : 'success').'">'.$status[$value->role_status].'</span>',
                'role_lastupdate'  => $value->role_lastupdate,
                'action'           => '<a href="'.url( 'roles/edit/'.Hashids::encode($value->role_id) ).'" class="btn btn-primary btn-icon btn-sm ajaxify"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pen"></i></a>&nbsp
                                       <a href="'.url( 'roles/destroy/'.Hashids::encode($value->role_id) ).'" class="btn btn-danger btn-icon btn-sm"  onClick="return f_status(2, this, event)" data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-alt"></i></a>&nbsp'
            ];
        }

        $encode = (object)[
            'meta' => ['page' => $start, 'pages' => null, 'perpage' => $limit, 'total' => $jumlah, 'sort' => 'asc', 'field' => 'id'],
            'data' =>  $data['records']
        ];

        echo json_encode($encode);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlehead'] = $this->titlehead;
        $data['pagetitle'] = 'Tabel Roles';
        $data['breadcrumb'] = ['Index' => url($this->url), 'Form Add Roles' => url($this->url.'/create')];
        $data['url']       = url($this->url);

        return view('roles/form_add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = $request->input();
        $validator = Validator::make($post, 
            [
                'role_name'     => 'required',
                'val_jstree'    => 'required',
            ],
            [
                'role_name.required' => 'Form roles name cannot blank',
                'val_jstree.required'=> 'Must select atleast one menu!'
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
        
        DB::beginTransaction();
        $roles = new Mrt_roles([
            'role_name'        => $post['role_name'],
            'role_description' => $post['role_description'],
            'role_createdby'   => Auth::id(),
            'role_status'      => '1',
            'role_ip'          => $request->ip(),
        ]);

        $insert = $roles->save();
        $insert_id = DB::getPdo()->lastInsertId();

        if (!empty($post['val_jstree'])) {
            $val_jstree = json_decode($post['val_jstree']);
            $id_menu = array_column($val_jstree, 'id');
            $parent_menu = array_column($val_jstree, 'parent');
            $group_menu_id = array_unique(array_merge($id_menu, $parent_menu));
            foreach ($group_menu_id as $menu_id) {
                $data['group_role_id']      = $insert_id;
                $data['group_menu_id']      = $menu_id;
                $data['group_createdby']    = Auth::id();
                $data['created_at']         = date('Y-m-d H:i:s');
                $data['group_status']       = '1';
                $data['group_ip']           = $request->ip();
                DB::table('mrt_groups')->insert($data);
            }
        }

        if ($insert) {
            DB::commit();
            $response['status']  = 1;
            $response['message'] = 'Data saved successfully';            
        } else {
            DB::rollback();
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
        $data['breadcrumb'] = ['Index' => url($this->url), 'Form Edit Roles' => url($this->url.'/edit/'.$id)];
        $data['url']       = url($this->url);
        $data['id']        = $id;
        $data['records']   = Mrt_roles::selectRaw('role_name,role_description')->where('role_id', Hashids::decode($id)[0])->get();
        return view('roles/form_edit', $data);
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
        $post    = $request->input();
        $role_id = Hashids::decode($id)[0];
        $validator = Validator::make($post, 
            [
                'role_name'     => 'required',
                'val_jstree'    => 'required',
            ],
            [
                'role_name.required' => 'Form roles name cannot blank',
                'val_jstree.required'=> 'Must select atleast one menu!'
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
        
        DB::beginTransaction();
        $roles                   = Mrt_roles::find($role_id);
        $roles->role_name        = $post['role_name'];
        $roles->role_description = $post['role_description'];
        $update = $roles->save();
        // dd($post);
        if (!empty($post['val_jstree'])) {
            $val_jstree = json_decode($post['val_jstree']);
            $id_menu = array_column($val_jstree, 'id');
            $parent_menu = array_column($val_jstree, 'parent');
            $group_menu_id = array_unique(array_merge($id_menu, $parent_menu));
            DB::table('mrt_groups')->where('group_role_id', $role_id)->delete();
            foreach ($group_menu_id as $menu_id) {
                $data['group_role_id']      = $role_id;
                $data['group_menu_id']      = $menu_id;
                $data['group_createdby']    = Auth::id();
                $data['created_at']         = date('Y-m-d H:i:s');
                $data['group_status']       = '1';
                $data['group_ip']           = $request->ip();
                DB::table('mrt_groups')->insert($data);
            }
        }

        if ($update) {
            DB::commit();
            $response['status']  = 1;
            $response['message'] = 'Data update successfully';            
        } else {
            DB::rollback();
            $response['status']  = 0;
            $response['message'] = 'Data failed to update';
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
        $role_id = Hashids::decode($id)[0];
        $roles   = Mrt_roles::find($role_id);
        $delete  = $roles->delete();

        if ($delete) {
            $response['status']  = 1;
            $response['message'] = 'Data has been delete';
        } else {
            $response['status']  = 0;
            $response['message'] = 'Data failed to delete';
        }
        
        echo json_encode($response);
    }

    public function preview_menu($role_id = null)
    {
        if(isset($_GET['operation'])) {
           try {
             $result = null;             
             switch($_GET['operation']) {
               case 'get_node':
                 $group_menu_id = [];
                 $node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
                 $data = DB::table('mrt_menus');
                 $data->selectRaw("menu_id AS id, menu_link AS name, menu_nama AS text, (CASE WHEN menu_parent IS NULL THEN '0' ELSE menu_parent END) AS parent_id, menu_icon AS icon, menu_link, menu_order");
                 $data->orderBy('menu_order', 'asc');
                 $data = $data->get();

                 
                 if ($role_id) {
                    $selectMenu = Mrt_roles::selectRaw('mrt_groups.*');
                    $join = isset($role_id) ? $selectMenu->leftjoin('mrt_groups', 'group_role_id', '=', 'role_id')->leftjoin('mrt_menus', 'menu_id', '=', 'group_menu_id') : null;
                    $where = isset($role_id) ? $selectMenu->where('group_role_id', Hashids::decode($role_id)[0]) : null;
                    $selectMenu = $selectMenu->get();
                    // echo '<pre>';
                    // print_r( $selectMenu );
                    // echo '</pre>';exit();
                    $group_menu_id = $selectMenu->pluck('group_menu_id')->all();
                    // dd($group_menu_id);
                 }

                 $itemsByReference = array();
                 /*BEGIN BUILD STRUKTUR DATA*/
                 // $group_menu_id = [1,2,3,4];
                 // echo in_array('1', $group_menu_id) ? 'true' : 'false';
                 foreach($data as $key => $item) {
                    $itemsByReference[$item->id] = $item;
                    $itemsByReference[$item->id]->icon = $item->icon == '' ? '' : 'flaticon-'.$item->icon;
                    $itemsByReference[$item->id]->state = in_array($item->id, $group_menu_id) ? ($item->menu_link == '' && $item->parent_id == 0 ? array('opened' => !0) : array('selected' => !0)) : '';
                    $itemsByReference[$item->id]->children = array();
                    $itemsByReference[$item->id]->data = (object)[];
                 }
                 // dd($group_menu_id, $data);
                 /*END BUILD STRUKTUR DATA*/
          
                 /*BEGIN SET GROUP CHILDREN YANG SESUAI DENGAN PARENT*/
                 foreach($data as $key => &$item)
                    if($item->parent_id && isset($itemsByReference[$item->parent_id]))
                     $itemsByReference [$item->parent_id]->children[] = &$item;
                 /*END SET GROUP CHILDREN YANG SESUAI DENGAN PARENT*/
          
                 /*BEGIN HAPUS CHILD YANG TIDAK SESUAI DENGAN PARENT*/
                 foreach($data as $key => &$item) {
                    if($item->parent_id && isset($itemsByReference[$item->parent_id]))
                     unset($data[$key]);
                 }
                 /*END HAPUS CHILD YANG TIDAK SESUAI DENGAN PARENT*/
                 $result = $data->values();
                 break;
               default:
                 throw new Exception('Unsupported operation: ' . $_GET['operation']);
                 break;
             }
             header('Content-Type: application/json; charset=utf-8');
             echo json_encode($result);
           }
           catch (Exception $e) {
             header($_SERVER["SERVER_PROTOCOL"] . ' 500 Server Error');
             header('Status:  500 Server Error');
             echo $e->getMessage();
           }
           die();
        }
    }
}
