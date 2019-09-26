<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Mrt_sub_categori;
use App\Mrt_categori;
use Validator;

class Category extends Controller
{
    private $url       = 'category';
    private $titlehead = 'Page Category & Sub Category';

    public function __construct(){
        DB::enableQueryLog();
        // $enc = Hashids::encode(1);
        // $dec = Hashids::decode($enc)[0];
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $data['titlehead'] = $this->titlehead;
        $data['pagetitle'] = 'Tabel Category & Sub Category';
        $data['breadcrumb'] = ['Index' => url($this->url)];
        $data['url']       = url($this->url);

        return view($this->url . '/index', $data);
    }

    function select(Request $param){
        $post   = $param->input();
        $getDta = Mrt_categori::selectRaw('category_id,category_code,category_name,category_status,category_createdby,category_createddate,name');
        $paging = $post['pagination'];
        $search = (!empty($post['query']) ? $post['query'] : null);

        if( isset($post['sort']) ){
            $getDta->orderBy($post['sort']['field'], $post['sort']['sort']);
        }else{
            $getDta->orderBy('category_createddate', 'DESC');
        }

        // proses searching
        if(!empty($search)){
            foreach ($search as $value => $param) {
                if($value === 'generalSearch'){
                    $getDta->whereRaw("(category_code LIKE '%".$param."%' OR category_name Like '%".$param."%')");
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

        $getDta->leftJoin('users','category_createdby','id');
        $getDta->offset($awal);
        $getDta->limit($limit);
        $result = $getDta->get();

        // dd(DB::getQueryLog());

        $jumlah          = count(Mrt_categori::all());
        $data['records'] = array();
        $i               = 1 + $awal;
        $status          = ['0' => 'INACTIVE', '1' => 'ACTIVE'];

        foreach($result as $key => $value){
            $data['records'][] = [
                'no'                   => $i++,
                'category_code'        => $value->category_code,
                'category_name'        => $value->category_name,
                'category_status'      => '<span class="btn btn-sm btn-label-'.($value->category_status == '0' ? 'danger' : 'success').'">'.$status[$value->category_status].'</span>',
                'category_createdby'   => $value->name,
                'category_createddate' => date('d F Y H:i:s', strtotime($value->category_createddate)),
                'action'               =>  '<a href="'.url( 'category/status/'.Hashids::encode($value->category_id) ).'/'.$value->category_status.'" class="btn btn-outline-hover-'.($value->category_status == "1" ? "info" : "warning").' btn-icon btn-sm"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Change Status" onClick="return f_status(1, this, event)"><i class="fa fa-'.($value->category_status == "1" ? "eye" : "eye-slash").'"></i></a>&nbsp;
                                            <a href="'.url( 'category/edit/'.Hashids::encode($value->category_id) ).'" class="btn btn-primary btn-icon btn-sm ajaxify"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Edit Data"><i class="fa fa-pen"></i></a>&nbsp;
                                            <a href="'.url( 'category/destroy/'.Hashids::encode($value->category_id) ).'" class="btn btn-danger btn-icon btn-sm"  onClick="return f_status(2, this, event)" data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-alt"></i></a>'
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
        $data['pagetitle'] = 'Form Add';
        $data['breadcrumb'] = ['Index' => url($this->url), 'Form Add Category & Sub Category' => url($this->url.'/create')];
        $data['url']       = url($this->url);

        return view($this->url.'/form_add', $data);
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
        $validator = Validator::make($post, [ 'category_code' => 'required|max:2', 'category_name' => 'required' ] );

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
            // insert data untuk cateogry
            $venpol = new Mrt_categori();
            $post['category_status']      = '1';
            $post['category_createdby']   = Auth::id();
            $post['category_createddate'] = date('Y-m-d H:i:s');
            $post['category_ip']          = $request->ip();
            $insertCategory               = $venpol->fill($post)->save();
            $lastId                       = $venpol->category_id;

            // insert data untuk sub cateogry
            foreach ($post['sub_category'] as $key => $value) {
                $dataSubcat[$key]['subcat_category_id'] = $lastId;
                $dataSubcat[$key]['subcat_code']        = $value['subcat_code'];
                $dataSubcat[$key]['subcat_name']        = $value['subcat_name'];
                $dataSubcat[$key]['subcat_status']      = '1';
                $dataSubcat[$key]['subcat_createdby']   = Auth::id();
                $dataSubcat[$key]['subcat_createddate'] = date('Y-m-d H:i:s');
                $dataSubcat[$key]['subcat_ip']          = $request->ip();
            }

            $insertSubCategory = Mrt_sub_categori::insert($dataSubcat);

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
        $data['titlehead'] = $this->titlehead;
        $data['pagetitle'] = 'Form Edit';
        $data['breadcrumb'] = ['Index' => url($this->url), 'Form Edit Category & Sub Category' => url($this->url.'/edit/'.$id)];
        $data['url']       = url($this->url);
        $data['id']        = $id;
        $data['records']   = Mrt_categori::selectRaw('category_code,category_name,subcat_code,subcat_name')
            ->leftJoin('mrt_sub_categoris','category_id','subcat_category_id')
            ->where('category_id', Hashids::decode($id)[0])
            ->get();
        return view($this->url . '/form_edit', $data);
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
        $post        = $request->input();
        $category_id = Hashids::decode($id)[0];
        $validator   = Validator::make($post, 
            [
                'category_code'   => 'required',
                'category_name'  => 'required'
            ]
            // [
            //     write message every errors notif
            // ]
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

        try {
            // start update data untuk category
            $venpol = Mrt_categori::find($category_id);
            $post['category_updatedby'] = Auth::id();
            $post['category_ip']        = $request->ip();
            $venpol->fill($post)->save();
            // end update data untuk category

            // start update data untuk sub category
            Mrt_sub_categori::where('subcat_category_id', $category_id)->delete();

            foreach ($post['sub_category'] as $key => $value) {
                $dataSubcat[$key]['subcat_category_id'] = $category_id;
                $dataSubcat[$key]['subcat_code']        = $value['subcat_code'];
                $dataSubcat[$key]['subcat_name']        = $value['subcat_name'];
                $dataSubcat[$key]['subcat_status']      = '1';
                $dataSubcat[$key]['subcat_createdby']   = Auth::id();
                $dataSubcat[$key]['subcat_createddate'] = date('Y-m-d H:i:s');
                $dataSubcat[$key]['subcat_ip']          = $request->ip();
            }

            $insertSubCategory = Mrt_sub_categori::insert($dataSubcat);

            DB::commit();
            // end update data untuk sub category
            
            $response['status']  = 1;
            $response['message'] = 'Data saved successfully';            
        } catch (\Exception $ex) {
            DB::rollback();
            $response['status']  = 0;
            $response['message'] = $ex->getMessage();
        }

        echo json_encode($response);
    }

    public function status($id, $status){
        DB::beginTransaction();

        try {
            $category_id = Hashids::decode($id)[0];

            // start update status table category
            Mrt_categori::find($category_id)
                ->fill(['category_status' => ($status == "1" ? "0" : "1")])
                ->save();
            // end update status table category

            // start update status table sub category
            Mrt_sub_categori::where('subcat_category_id', $category_id)
                ->update(['subcat_status' => ($status == "1" ? "0" : "1")]);
            // end update status table sub category

            DB::commit();

            $response['status']  = 1;
            $response['message'] = 'Data has been change';
            
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
        DB::beginTransaction();

        try {
            $category_id = Hashids::decode($id)[0];

            // start delete table category
            Mrt_categori::find($category_id)
                ->delete();
            // end delete table category

            // start delete table sub category
            Mrt_sub_categori::where('subcat_category_id', $category_id)
                ->delete();
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
}
