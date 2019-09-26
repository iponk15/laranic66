<?php

namespace App\Http\Controllers;

use Auth;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mrt_menu;

class Menus extends Controller
{
    private $url       = 'menus';
    private $pagetitle = 'Data Menu';

    public function __construct(){
        DB::enableQueryLog();
        $enc = Hashids::encode(1);
        $dec = Hashids::decode($enc)[0];
    }

    function index(){
        $data['titlehead'] = 'Pages Menu';
        $data['pagetitle'] = $this->pagetitle;
        $data['url_page']  = $this->url;
        $data['breadcrumb'] = ['Index' => url('/menus')];
        return view('menus/index', $data);
    }

    function preview_menu(Request $request){
    	if(isset($_GET['operation'])) {
		   try {
		     $result = null;
		     
		     switch($_GET['operation']) {
		       case 'get_node':
		         $node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
		         $data = DB::table('mrt_menus')->selectRaw("menu_id AS id, menu_link AS name, menu_nama AS text, (CASE WHEN menu_parent IS NULL THEN '0' ELSE menu_parent END) AS parent_id, menu_icon AS icon, menu_link")->orderBy('menu_order', 'asc')->get();

		         $itemsByReference = array();
		         /*BEGIN BUILD STRUKTUR DATA*/
		         foreach($data as $key => $item) {
		         	$style['style']	= $item->menu_link == '' ? '' : 'text-decoration: underline';
		            $itemsByReference[$item->id] = $item;
		            $itemsByReference[$item->id]->icon = $item->icon == '' ? '' : 'flaticon-'.$item->icon;
		            $itemsByReference[$item->id]->a_attr = $style;
		            $itemsByReference[$item->id]->children = array();
		            $itemsByReference[$item->id]->data = (object)[];
		         }

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
		         // $result = array_values($data);
		         $result = $data->values();
		         break;
		       case 'create_node':
		         $node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
		         
		         $nodeText = isset($_GET['text']) && $_GET['text'] !== '' ? $_GET['text'] : '';

		         DB::table('mrt_menus')->insert(['menu_nama' => $nodeText, 'menu_parent' => $node, 'menu_order' => $_GET['position'], 'menu_createdby' => Auth::id(), 'menu_createddate' => date('Y-m-d H:i:s'), 'menu_ip' => $request->ip()]);
		         $id = DB::getPdo()->lastInsertId();

		         $result = array('id' => $id);
		  
		         break;
		       case 'rename_node':
		         $node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
		         //print_R($_GET);
		         $nodeText = isset($_GET['text']) && $_GET['text'] !== '' ? $_GET['text'] : '';
		         DB::table('mrt_menus')->where('menu_id', $node)->update(['menu_nama' => $nodeText]);
		         break;
		       case 'delete_node':
		         $node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
		         DB::table('mrt_menus')->where('menu_id', $node)->delete();
		         DB::table('mrt_menus')->where('menu_parent', $node)->delete();
		         break;
		       case 'move_node':
			       	DB::beginTransaction();

					try {
						$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
						$parent = $_GET['parent'] === '#' ? NULL : $_GET['parent'];
						$position = $_GET['position'];
						$old_position = $_GET['old_position'];

						/*END GET DATA MENU YANG PARENT SAMA DAN ORDER LEBIH DARI*/
						$dataUpt['menu_parent'] = $parent;
						$dataUpt['menu_order']	 = $position;

						$update = DB::table('mrt_menus')->where('menu_id', $node)->update(['menu_parent' => $parent, 'menu_order' => $position]);
						/*BEGIN GET MENU YANG SUDAH DI UPDATE*/

						/*BEGIN GET DATA MENU & BUILD ARRAY AFTER UPDATE MENU ORDER NODE YANG DI DRAG*/
						$NewMenu = DB::table('mrt_menus')->selectRaw('menu_id, menu_order, menu_nama')->where('menu_parent', $parent)->orderBy('menu_order', 'asc')->get();

						$menuPrior = array();
						foreach ($NewMenu as $key => $value) {
							$menuPrior[$value->menu_id] = $value;
							$menuPrior[$value->menu_id]->prioritas = ($value->menu_id == $node ? ($value->menu_order > $old_position ? 2 : 0) : 1);
						}
						/*BEGIN GET DATA MENU & BUILD ARRAY AFTER UPDATE MENU ORDER NODE YANG DI DRAG*/

						/*BEGIN SORT DATA MENU BERDASARKAN MENU ORDER DAN PRIORITAS*/
						$collection = collect($NewMenu);
						$sorted = $collection->sortBy('menu_order');
						$sorted2 = $sorted->sortBy('prioritas');
						/*END SORT DATA MENU BERDASARKAN MENU ORDER DAN PRIORITAS*/
						$NewMenu = $sorted2;

						for ($i=0; $i < count($NewMenu); $i++) { 
							$NewUpt[]	= array('menu_id' => $NewMenu[$i]->menu_id, 'menu_order' => $i);
						}

						if (isset($NewUpt)) {
							foreach ($NewUpt as $value) {
								DB::table('mrt_menus')->where('menu_id', $value['menu_id'])->update(['menu_order' => $value['menu_order']]);
							}
						}
						
					    DB::commit();
					    // all good
					} catch (\Exception $e) {
					    DB::rollback();
					    // something went wrong
					}
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

    function detail($id){
    	$data['url'] = $this->url;
		$data['id']	 = $id;
		$data['records']	= DB::table('mrt_menus')->where('menu_id', $id)->get();;
        return view('menus/menus_detail', $data);
    }

    function store_parent(Request $req){
        $post= $req->input();
        
        $req->validate(['menu_nama' => 'required',],['menu_nama.required' => 'input cannot be blank!',]);

        $data = new Mrt_menu([
            'menu_nama' => $post['menu_nama']
        ]);

        $insert = $data->save();
        
        if ($insert) {
            $response['status']     = 1;
            $response['message']    = 'Data berhasil disimpan';            
        } else {
            $response['status']     = 0;
            $response['message']    = 'Data gagal disimpan';
        }
        
        echo json_encode($response);
    }

    function update_menus(Request $req, $id){
        $post= $req->input();

        $menus                   = Mrt_menu::find($id);
        $menus->menu_link        = $post['menu_link'];
        $menus->menu_icon = $post['menu_icon'];
        $update = $menus->save();

        if ($update) {
            $response['status']     = 1;
            $response['message']    = 'Data berhasil dirubah';            
        } else {
            $response['status']     = 0;
            $response['message']    = 'Data gagal dirubah';
        }
        
        echo json_encode($response);
    }
}
