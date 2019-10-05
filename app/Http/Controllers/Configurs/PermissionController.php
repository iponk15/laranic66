<?php
namespace App\Http\Controllers\Configurs;

use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Guard;
use Illuminate\Http\Request;
use App\Models\MrtModule;
use Validator;
use Helper;
use Auth;
use Gate;
class PermissionController extends Controller
{
    private $titlehead = 'Page Permission Data';
    private $route     = 'configurs.permission';

    public function __construct(){
        DB::enableQueryLog();
        $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index','store']]);
        $this->middleware('permission:permission-create', ['only' => ['create','store']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlehead']  = $this->titlehead;
        $data['pagetitle']  = 'Tabel Permission';
        $data['breadcrumb'] = [ $this->titlehead => route($this->route.'.index') ];
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
        $data['pagetitle']  = 'Form Create';
        $data['breadcrumb'] = ['Index' => route($this->route.'.index'), 'Form Create Permission' => route($this->route.'.create')];
		$data['route']      = $this->route;

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
        $validator = Validator::make($post, [ 'module_menu_id' => 'required' ] );

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
            // insert data untuk module
            $modules = new MrtModule();
            $post['module_menu_id']   = $post['module_menu_id'];
            $post['module_createdby'] = Auth::id();
            $post['created_at']       = date('Y-m-d H:i:s');
            $post['module_ip']        = $request->ip();
            
            $modules->fill($post)->save();
            $lastId = $modules->module_id;

            // insert data untuk sub permission
            foreach ($post['permission'] as $permission) {
                Permission::create([
                    'module_id' => $lastId,
                    'name'      => $permission['permission_name']
                ]);
            }

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
        $data['breadcrumb'] = ['Index' => route($this->route.'.index'), 'Form Edit Permission' => route($this->route.'.edit', ['permission' => $id])];
        $data['route']      = $this->route;
        $data['id']         = $id;
        $data['records']    = MrtModule::selectRaw('menu_id,menu_nama,permissions.id AS permin_id,permissions.name AS permin_name,permissions.module_id')
                                ->leftJoin('mrt_menus','module_menu_id','menu_id')
                                ->leftJoin('permissions','mrt_modules.module_id','permissions.module_id')
                                ->where('mrt_modules.module_id', Hashids::decode($id)[0])->get();

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
        $module_id = Hashids::decode($id)[0];
        $validator = Validator::make($post, [ 'module_menu_id' => 'required' ] );

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
            // start update data untuk module
            $module                   = MrtModule::find($module_id);
            $post['module_updatedby'] = Auth::id();
            $post['module_ip']        = $request->ip();
            $module->fill($post)->save();
            // end update data untuk module

            // insert data untuk permission
            foreach ($post['permission'] as $key => $value) {
                DB::table('permissions')
                    ->where('id', $value['permission_id'])
                    ->update(['name' => $value['permission_name']]);
            }

            DB::commit();
            // end update data untuk permission
            
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
    public function destroy(Request $param, $id)
    {
        $module_id = Hashids::decode($id)[0];
        DB::beginTransaction();

        try {
            // start delete table category
            MrtModule::find($module_id)->delete();
            // end delete table category

            // start delete table sub category
            Permission::where('module_id', $module_id)->delete();
            // end delete table sub category

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
        $getDta = MrtModule::selectRaw('module_id, menu_nama, name, module_status, mrt_modules.updated_at');
        $conDta = MrtModule::selectRaw('count(*) jumlah')
            ->leftJoin('mrt_menus','module_menu_id','menu_id')
            ->leftJoin('users','module_createdby','id');
        $paging = $post['pagination'];
        $search = (!empty($post['query']) ? $post['query'] : null);

        if( isset($post['sort']) ){
            $getDta->orderBy($post['sort']['field'], $post['sort']['sort']);
        }else{
            $getDta->orderBy('mrt_modules.created_at', 'DESC');
        }

        // proses searching
        if(!empty($search)){
            foreach ($search as $value => $param) {
                if($value === 'generalSearch'){
                    $getDta->whereRaw("(menu_nama ILIKE '%".$param."%')");
                    $conDta->whereRaw("(menu_nama ILIKE '%".$param."%')");
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
        
        $getDta->leftJoin('mrt_menus','module_menu_id','menu_id');
        $getDta->leftJoin('users','module_createdby','id');
        $getDta->offset($awal);
        $getDta->limit($limit);
        $result = $getDta->get();

        $jumlah          = $conDta->get()[0]->jumlah;
        $data['records'] = array();
        $i               = 1 + $awal;
        $status          = ['0' => 'Inactive', '1' => 'Active'];

        foreach($result as $key => $value){
            $data['records'][] = [
                'no'            => $i++,
                'module_name'   => $value->menu_nama,
                'module_status' => $status[$value->module_status],
                'name'          => $value->name,
                'updated_at'    => date('d F Y H:i:s', strtotime($value->updated_at)),
                'action'        => ( Gate::check('permission-edit') ? '<a href="'.route($this->route.'.edit', ['permission' => Hashids::encode($value->module_id)] ).'" class="btn btn-primary btn-icon btn-sm ajaxify"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pen"></i></a>' : '' ).'&nbsp'.
								   ( Gate::check('permission-delete') ? '<a href="'.route($this->route.'.destroy', ['permission_id' => Hashids::encode($value->module_id)] ).'" data-method="POST" class="btn btn-danger btn-icon btn-sm"  onClick="return f_status(2, this, event)" data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-alt"></i></a>' : '' )
            ];
        }

        $encode = (object)[
            'meta' => ['page' => $start, 'pages' => null, 'perpage' => $limit, 'total' => $jumlah, 'sort' => 'asc', 'field' => 'id'],
            'data' =>  $data['records']
        ];

        echo json_encode($encode);
    }
}
