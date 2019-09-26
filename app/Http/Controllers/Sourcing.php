<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Mrt_sourcing;
use Validator;

class Sourcing extends Controller
{
    private $url       = 'sourcing';
    private $titlehead = 'List data sourcing';

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
        $data['pagetitle'] = 'Tabel List Sourcing';
        $data['breadcrumb'] = ['Index' => url($this->url)];
        $data['url']       = url($this->url);

        return view($this->url.'/index', $data);
    }

    function select(Request $param){
        $post   = $param->input();
        $getDta = Mrt_sourcing::selectRaw('sourcing_id,sourcing_no_inv,sourcing_title,sourcing_startdate,sourcing_enddate,sourcing_category,sourcing_subcategori,sourcing_type');
        $paging = $post['pagination'];
        $search = (!empty($post['query']) ? $post['query'] : null);

        if( isset($post['sort']) ){
            $getDta->orderBy($post['sort']['field'], $post['sort']['sort']);
        }else{
            $getDta->orderBy('sourcing_createddate', 'DESC');
        }

        // proses searching
        if(!empty($search)){
            foreach ($search as $value => $param) {
                if($value === 'generalSearch'){
                    $getDta->whereRaw("(sourcing_no_inv LIKE '%".$param."%' OR sourcing_title LIKE '%".$param."%')");
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

        $jumlah          = count(Mrt_sourcing::all());
        $data['records'] = array();
        $i               = 1 + $awal;
        $status          = ['0' => 'Inactive', '1' => 'Active'];

        foreach($result as $key => $value){
            $data['records'][] = [
                'no'                   => $i++,
                'sourcing_no_inv'      => $value->sourcing_no_inv,
                'sourcing_title'       => $value->sourcing_title,
                'sourcing_startdate'   => date('l, d F Y', strtotime($value->sourcing_startdate)),
                'sourcing_enddate'     => date('l, d F Y', strtotime($value->sourcing_enddate)),
                'sourcing_category'    => $value->sourcing_category,
                'sourcing_subcategori' => $value->sourcing_subcategori,
                'sourcing_type'        => $value->sourcing_type,
                'action'               =>  '<a href="'.url( 'sourcing/view_detail/'.Hashids::encode($value->sourcing_id) ).'" class="btn btn-primary btn-icon btn-sm ajaxify"  data-container="body" data-toggle="kt-tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-pen"></i></a>'
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
        $data['breadcrumb'] = ['Index' => url($this->url), 'Form Add Sourcing' => url($this->url.'/create')];
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
        $post = $request->input();
        $exp  = explode('-', $post['range_date']);
        $str  = date('Y-m-d', strtotime(strtr($exp[0],'/','-')));
        $end  = date('Y-m-d', strtotime(strtr($exp[1],'/','-')));

        $validator = Validator::make($post, 
            [
                'sourcing_no_inv'      => 'required',
                'sourcing_title'       => 'required',
                'sourcing_category'    => 'required',
                'sourcing_subcategori' => 'required',
                'sourcing_type'        => 'required',
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

        $sourcing = new Mrt_sourcing();
        $post['sourcing_startdate']   = $str;
        $post['sourcing_enddate']     = $end;
        $post['sourcing_createdby']   = Auth::id();
        $post['sourcing_createddate'] = date('Y-m-d H:i:s');
        $post['sourcing_ip']          = $request->ip();
        // dd($post);
        $insert                       = $sourcing->fill($post)->save();
        
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
