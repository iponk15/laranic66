<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Mrt_vendor_polic;
use Validator;

class Venpolicy extends Controller
{
    private $url       = 'venpolicy';
    private $titlehead = 'Page Vendor Policy';

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
        $data['pagetitle'] = 'Tabel Vendor Policy';
        $data['breadcumb'] = ['Index' => url($this->url)];
        $data['url']       = url($this->url);

        return view($this->url . '/index', $data);
    }

    function select(Request $param){
        $post   = $param->input();
        $getDta = Mrt_vendor_polic::selectRaw('venpol_id,venpol_type,venpol_title,venpol_status,venpol_createddate,venpol_createdby,name');
        $paging = $post['pagination'];
        $search = (!empty($post['query']) ? $post['query'] : null);

        if( isset($post['sort']) ){
            $getDta->orderBy($post['sort']['field'], $post['sort']['sort']);
        }else{
            $getDta->orderBy('venpol_createddate', 'DESC');
        }

        // proses searching
        if(!empty($search)){
            foreach ($search as $value => $param) {
                if($value === 'generalSearch'){
                    $getDta->whereRaw("(venpol_title LIKE '%".$param."%')");
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
        $getDta->leftJoin('users','venpol_createdby','id');
        $getDta->offset($awal);
        $getDta->limit($limit);
        $result = $getDta->get();

        $jumlah          = count(Mrt_vendor_polic::all());
        $data['records'] = array();
        $i               = 1 + $awal;
        $type            = ['1' => 'General', '2' => 'Open Sourcing', '3' => 'Open Bidding', '4' => 'Direct Selection'];
        $status          = ['0' => 'INACTIVE', '1' => 'ACTIVE'];

        foreach($result as $key => $value){
            $data['records'][] = [
                'no'                 => $i++,
                'venpol_type'        => $type[$value->venpol_type],
                'venpol_title'       => $value->venpol_title,
                'venpol_status'      => '<span class="btn btn-sm btn-label-'.($value->venpol_status == '0' ? 'danger' : 'success').'">'.$status[$value->venpol_status].'</span>',
                'venpol_createdby'   => $value->name,
                'venpol_createddate' => date('d F Y H:i:s', strtotime($value->venpol_createddate)),
                'action'             =>  '<a href="'.url( 'venpolicy/status/'.Hashids::encode($value->venpol_id) ).'/'.$value->venpol_status.'" class="btn btn-outline-hover-'.($value->venpol_status == "1" ? "info" : "warning").' btn-icon btn-sm"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="View Detail" onClick="return f_status(1, this, event)"><i class="fa fa-'.($value->venpol_status == "1" ? "eye" : "eye-slash").'"></i></a>&nbsp;
                                          <a href="'.url( 'venpolicy/edit/'.Hashids::encode($value->venpol_id) ).'" class="btn btn-primary btn-icon btn-sm ajaxify"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-pen"></i></a>&nbsp;
                                          <a href="'.url( 'venpolicy/destroy/'.Hashids::encode($value->venpol_id) ).'" class="btn btn-danger btn-icon btn-sm"  onClick="return f_status(2, this, event)" data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-alt"></i></a>'
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
        $data['breadcumb'] = ['Index' => url($this->url), 'Form Add Vendor Policy' => url($this->url.'/create')];
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
        $validator = Validator::make($post, 
            [
                'venpol_type'   => 'required',
                'venpol_title'  => 'required',
                'venpol_status' => 'required'
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

        $venpol = new Mrt_vendor_polic();
        $post['venpol_createdby']   = Auth::id();
        $post['venpol_createddate'] = date('Y-m-d H:i:s');
        $post['venpol_ip']          = $request->ip();
        $insert                     = $venpol->fill($post)->save();
        
        if ($insert) {
            $response['status']  = 1;
            $response['message'] = 'Data saved successfully';            
        } else {
            $response['status']  = 0;
            $response['message'] = "Data failed to save";
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
        $data['breadcumb'] = ['Index' => url($this->url), 'Form Edit Vendor Policy' => url($this->url.'/edit/'.$id)];
        $data['url']       = url($this->url);
        $data['id']        = $id;
        $data['records']   = Mrt_vendor_polic::selectRaw('venpol_type,venpol_title,venpol_content,venpol_status')->where('venpol_id', Hashids::decode($id)[0])->get();
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
        $post      = $request->input();
        $venpol_id = Hashids::decode($id)[0];
        $validator = Validator::make($post, 
            [
                'venpol_type'   => 'required',
                'venpol_title'  => 'required',
                'venpol_status' => 'required'
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

        $venpol = Mrt_vendor_polic::find($venpol_id);
        $post['venpol_updatedby']   = Auth::id();
        $post['venpol_ip']          = $request->ip();
        $insert                     = $venpol->fill($post)->save();
        
        if ($insert) {
            $response['status']  = 1;
            $response['message'] = 'Data saved successfully';            
        } else {
            $response['status']  = 0;
            $response['message'] = "Data failed to save";
        }
        
        echo json_encode($response);
    }

    public function status($id, $status){
        $venpol_id = Hashids::decode($id)[0];
        $venpolicy = Mrt_vendor_polic::find($venpol_id);
        $status    = ['venpol_status' => ($status == "1" ? "0" : "1")];

        $venpolicy->fill($status);
        $change = $venpolicy->save();

        if ($change) {
            $response['status']  = 1;
            $response['message'] = 'Data has been change';
        } else {
            $response['status']  = 0;
            $response['message'] = 'Data failed to change';
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
        $venpol_id = Hashids::decode($id)[0];
        $venpol    = Mrt_vendor_polic::find($venpol_id);
        $delete    = $venpol->delete();

        if ($delete) {
            $response['status']  = 1;
            $response['message'] = 'Data has been delete';
        } else {
            $response['status']  = 0;
            $response['message'] = 'Data failed to delete';
        }
        
        echo json_encode($response);
    }
}
