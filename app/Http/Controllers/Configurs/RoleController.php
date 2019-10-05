<?php

namespace App\Http\Controllers\Configurs;

use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Validator;
use DB;

class RoleController extends Controller
{
    private $titlehead = 'Page Roles Data';
    private $route     = 'configurs.role';
    private $datatable = 'role_datatable';

    public function __construct(){
        DB::enableQueryLog();
        // $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        // $this->middleware('permission:role-create', ['only' => ['create','store']]);
        // $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlehead']  = $this->titlehead;
        $data['pagetitle']  = 'Tabel Roles';
        $data['breadcrumb'] = [ $this->titlehead => route($this->route.'.index') ];
        $data['route']      = $this->route;
        $data['datatable']  = $this->datatable;
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
        $data['pagetitle']  = 'Form Create';
        $data['breadcrumb'] = ['Index' => route($this->route.'.index'), 'Form Create Roles' => route($this->route.'.create')];
        $data['route']      = $this->route;
      
        // olah data permission
        $permission = Permission::selectRaw('permissions.id AS permin_id,permissions.name AS permin_name,permissions.guard_name,mrt_menus.menu_id,mrt_menus.menu_nama')
                        ->leftJoin('mrt_modules','permissions.module_id','mrt_modules.module_id')
                        ->leftJoin('mrt_menus','mrt_modules.module_menu_id','mrt_menus.menu_id')
                        ->get();

        if($permission->isEmpty()){
            $data['permission'] = '';
        }else{
            foreach($permission as $key => $item) {
                $itemsAfterGroup[$item->menu_id]['menu_id'] = $item->menu_id;
                $itemsAfterGroup[$item->menu_id]['menu_nama'] = $item->menu_nama;
                $itemsAfterGroup[$item->menu_id]['list_permission'][] = $item;
            }
            
            $data['permission'] = collect($itemsAfterGroup)->values();
        }

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
        $validator = Validator::make($post, [ 'name' => 'required' ] );

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

        try {
            // insert data untuk role dan permission
            $role = Role::create( ['name' => strip_tags($request->input('name'))] );
            $role->syncPermissions($request->input('permission'));

            DB::commit();

            $response['status']  = 1;
            $response['message'] = 'Data saved successfully';            
        } catch (\Exception $ex) {
            DB::rollback();
            $response['status']  = 0;
            $response['message'] = $ex->getMessage();
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
        $data['breadcrumb'] = ['Index' => route($this->route.'.index'), 'Form Edit Role' => route($this->route.'.edit', ['role' => $id])];
        $data['route']      = $this->route;
        $data['id']         = $id;
        $data['records']    = Role::selectRaw('roles.name')->where('roles.id', Hashids::decode($id)[0])->get();

        // olah data permission
        $permission = DB::table('permissions', 'ps')
                        ->select(
                            'ps.id',
                            'ps.name',
                            'mrt_menus.menu_id AS permin_menu_id',
                            'mrt_menus.menu_nama AS permin_menu_nama',
                            'fk.menu_id',
                            'fk.menu_nama',
                            'fk.role_id',
                            'fk.role_name'
                        )
                        ->leftJoin(
                            DB::raw('(
                                SELECT
                                    p.id AS permin_id,
                                    p.module_id AS permin_module_id,
                                    p.name AS permin_name,
                                    rhp.role_id,
                                    r.name AS role_name,
                                    mms.menu_id,
                                    mms.menu_nama
                                FROM permissions p
                                LEFT JOIN role_has_permissions rhp ON p.id = rhp.permission_id
                                LEFT JOIN roles r ON rhp.role_id = r.id
                                LEFT JOIN mrt_modules mm ON p.module_id = mm.module_id
                                LEFT JOIN mrt_menus mms ON mm.module_menu_id = mms.menu_id
                                WHERE role_id = '.Hashids::decode($id)[0].') fk'
                            ), function($join){ 
                                $join->on('ps.id','=','fk.permin_id');
                                $join->on('ps.module_id','=','fk.permin_module_id');
                            }
                        )
                        ->leftJoin('mrt_modules','ps.module_id','mrt_modules.module_id')
                        ->leftJoin('mrt_menus','mrt_modules.module_menu_id','mrt_menus.menu_id')
                        ->orderby('ps.id','ASC')
                        ->get();

        foreach($permission as $key => $item) {
            $itemsAfterGroup[$item->permin_menu_id]['menu_id']           = $item->permin_menu_id;
	        $itemsAfterGroup[$item->permin_menu_id]['menu_nama']         = $item->permin_menu_nama;
	        $itemsAfterGroup[$item->permin_menu_id]['list_permission'][] = $item;
        }
        
        $data['permission'] = collect($itemsAfterGroup)->values();

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
        $validator = Validator::make($post, [ 'name' => 'required' ] );

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

        try {
            // update data role dan permission cek
            $role       = Role::find(Hashids::decode($id)[0]);
            $role->name = $request->input('name');
            $role->save();

            $role->syncPermissions($request->input('permission'));

            DB::commit();

            $response['status']  = 1;
            $response['message'] = 'Data saved successfully';            
        } catch (\Exception $ex) {
            DB::rollback();
            $response['status']  = 0;
            $response['message'] = $ex->getMessage();
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
        DB::beginTransaction();

        try {
            // start delete table category
            Role::find($role_id)->delete();
            // end delete table category

            DB::commit();

            $response['status']  = 1;
            $response['message'] = 'Data has been delete';
        }catch (\Exception $ex) {
            DB::rollback();
            $response['status']  = 0;
            $response['message'] = $ex->getMessage();
        }
        
        echo json_encode($response);
    }

    function table(Request $param){
        $post   = $param->input();
        $getDta = Role::selectRaw('id, name, guard_name, updated_at, created_at');
        $conDta = Role::selectRaw('count(*) jumlah');
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
                    $getDta->whereRaw("(name ILIKE '%".$param."%' OR guard_name ILIKE '%".$param."%')");
                    $conDta->whereRaw("(name ILIKE '%".$param."%' OR guard_name ILIKE '%".$param."%')");
                }else{
                    if($value !== 0 ){
                        $getDta->where($value, $param);
                        $conDta->where($value, $param);
                    }
                }
            }
            $awal = null;
        }

        $start = $paging['page'];
        $limit = $paging['perpage'];
        $awal  = ($start == 1 ? '0' : ($start * $limit) - $limit);
        
        $getDta->offset($awal);
        $getDta->limit($limit);
        $result = $getDta->get();

        $jumlah          = $conDta->get()[0]->jumlah;
        $data['records'] = array();
        $i               = 1 + $awal;

        foreach($result as $key => $value){
            $data['records'][] = [
                'no'         => $i++,
                'name'       => $value->name,
                'guard_name' => $value->guard_name,
                'updated_at' => date('d F Y H:i:s', strtotime($value->updated_at)),
                'action'     => '<a href="'.route($this->route.'.edit', ['role' => Hashids::encode($value->id)] ).'" class="btn btn-primary btn-icon btn-sm ajaxify"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pen"></i></a>&nbsp
							     <a href="'.route($this->route.'.destroy', ['role_id' => Hashids::encode($value->id)] ).'" data-method="POST" class="btn btn-danger btn-icon btn-sm"  onClick="return f_status(2, this, event)" data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-alt"></i></a>&nbsp'
            ];
        }

        $encode = (object)[
            'meta' => ['page' => $start, 'pages' => null, 'perpage' => $limit, 'total' => $jumlah, 'sort' => 'asc', 'field' => 'id'],
            'data' =>  $data['records']
        ];

        echo json_encode($encode);
    }
}
