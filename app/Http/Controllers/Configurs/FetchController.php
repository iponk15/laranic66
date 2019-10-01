<?php

namespace App\Http\Controllers\Configurs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;

class FetchController extends Controller
{
    function __construct(){
        DB::enableQueryLog();
    }

    function selectModule(Request $request){

        // $q                     = $_GET['q'];
        // $where[$field.' LIKE'] = '%'.$q.'%';
        // $result                = $this->m_global->get( $table, null, $where, '*', null, null, 0, 5, null, 1 );
        // $data                  = [];

        // for ($i=0; $i < count( $result ); $i++) {
        //     $data[$i] = ['id' => $result[$i][$id], 'text' => $result[$i][$field] ];
        // }

        // echo json_encode( ['items' => $data] );

        $post   = $request->input();
        $result = DB::table( 'mrt_menus' )
            ->select( DB::raw('menu_id,menu_nama') )
            ->where('menu_link','<>',null)
            ->where('menu_nama','ILIKE','%'.$post['q'].'%')
            ->get();

        $data = [];

        for ($i=0; $i < count( $result ); $i++) {
            $data[$i] = ['id' => $result[$i]->menu_id, 'text' => $result[$i]->menu_nama ];
        }

        echo json_encode( ['items' => $data] );
        
    }
}
