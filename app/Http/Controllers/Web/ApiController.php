<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\AlertController;
use App\Models\Web\Cart;
use App\Models\Web\Currency;
use App\Models\Web\Customer;
use App\Models\Web\Index;
use App\Models\Web\Languages;
use App\Models\Web\Products;
use App\User;
use App\Vendor;
use App\Models\ApiLog;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Config;
use Mail;
use Lang;
use Socialite;
use Validator;
use Hash;
use Carbon\Carbon;

class ApiController extends Controller
{
	public function __construct(Request $request){


		if (isset($_SERVER['HTTP_ORIGIN'])) {
			   header("Access-Control-Allow-Origin:".$_SERVER['HTTP_ORIGIN']);
			   header('Access-Control-Allow-Credentials: true');
			   header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
          if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

		        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

		        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		            header("Access-Control-Allow-Headers:".$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
			 }


            foreach($_SERVER as $key=>$value) { 
	                if (substr($key,0,5)=="HTTP_") { 
	                    $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
	                    $out[$key]=$value; 
	                }else{ 
	                    $out[$key]=$value; 
	        		} 
            } 
          
       
        if(trim($out['Api-Key'])=='123456asdfa741258tguuu'){
        }else{
        	echo 'invalid request';die;
        }
  		
		$url =  \URL::current();
		$data =  json_encode($request->all());
		$ApiLog = new ApiLog();
		$ApiLog->input_headers = json_encode($out);
		$ApiLog->url = $url;
		$ApiLog->input_date = $data;
		$ApiLog->save();
	}

	function app_vendor_login(Request $request){

		if(empty(trim($request->input('username')))){
			return response()->json(['status'=>0,'message'=>'Please enter username','data'=>array('id'=>0)]);
		}elseif(empty(trim($request->input('password')))){
			return response()->json(['status'=>0,'message'=>'Please enter password','data'=>array('id'=>0)]);
		}
		elseif(empty(trim($request->input('role_id')))){
			return response()->json(['status'=>0,'message'=>'Please enter role id','data'=>array('id'=>0)]);
		}
		$username = $request->input('username');
		$password = $request->input('password');
		$roleId  = $request->input('role_id');
		/*$mobcheck = User::where('mobile',$username)->get();
		if(!empty($mobcheck[0]->id)){
			$username = $mobcheck[0]->email;
		}*/
		if (Auth::guard('web')->attempt(['email' => $username, 'password' => $password ], $request->remember)) {
			 
			/****************update userid in cart table*********************/
			$device_id = $request->input('device_id');
			/*$checkuserId = CartItem::where('session_id',$device_id)->get();
			foreach ($checkuserId as  $value) {
			  $cardupdate = CartItem::find($value->id);
			  $cardupdate->user_id = auth()->user()->id;
			  $cardupdate->save();
			}*/
			/**************************************************************/
			$data['id'] = auth()->user()->id;
			$data['name'] = auth()->user()->first_name;
			$data['mobile'] = auth()->user()->phone;
			$data['email'] = auth()->user()->email;


			$verdedata 					=  DB::table('vendors')->where('user_id', auth()->user()->id)->first();


			if($verdedata){
				$data['country'] 			= $this->getcountryName($verdedata->country_id);
				$data['state'] 				= $this->getstateName($verdedata->state_id);
				$data['city'] 				= $this->getcityName($verdedata->city_id);
				$data['business_name'] 		= $verdedata->business_name;
				$data['business_address'] 	= $verdedata->business_address;
				$doucmument 				= json_decode($verdedata->doucmument, true);

				if(!empty($doucmument)){
					$docarry = array();
				    foreach ($doucmument as $doucmumentkey => $doucmumentvalue) {
				    	$docarry[]  = asset('doucmument/'.$doucmumentvalue);
				     }
				     $data['doucmument'] 		= $docarry;
				}else{
					 $data['doucmument'] 		= [];
				}
				 
				$data['account_no'] 		= ($verdedata->account_no) ? $verdedata->account_no :'';
				$data['bank_name'] 			= ($verdedata->bank_name) ? $verdedata->bank_name:'';
				$data['ifsc_code'] 			= ($verdedata->ifsc_code) ? $verdedata->ifsc_code:'';
				$data['branch_address'] 	= ($verdedata->branch_address) ? $verdedata->branch_address:'';
				$data['account_no'] 		= ($verdedata->account_no) ? $verdedata->account_no:'';
				$data['branch_address']     = ($verdedata->branch_address) ? $verdedata->branch_address:'';

				$verorlocation			    =  DB::table('vendors_location')->where('vendor_id', $verdedata->id)->get();
				$locationarray = array();
				foreach ($verorlocation as $verorlocationkey => $verorlocationvalue) {
				 	$singlelocation = array();
				 	$singlelocation['location_name'] = $verorlocationvalue->location_name;
				 	$singlelocation['shipping_fees'] = ($verorlocationvalue->shipping_fees) ? $verorlocationvalue->shipping_fees:0;
				 	$singlelocation['shipping_note'] = ($verorlocationvalue->shipping_note) ? $verorlocationvalue->shipping_note:'';
				 	$singlelocation['country'] 	 = $this->getcountryName($verorlocationvalue->country_id);
				 	$singlelocation['state'] 	 = $this->getstateName($verorlocationvalue->state_id);
				 	$singlelocation['city'] 	 = $this->getcityName($verorlocationvalue->city_id);
				 	$singlelocation['location']  = $this->getlocationName($verorlocationvalue->location_name);
				 	$locationarray[] = $singlelocation;
				 } 

			 
				$verorcategory			    =  DB::table('vendor_category')->where('user_id', $verdedata->id)->get();
				$vendorcatecoryarray = array();
				foreach ($verorcategory as $verorlocationkey => $verorlocationvalue) {
				 	$singcatery = array();
				 	$singcatery['id'] = $verorlocationvalue->category_id;
				 	$singcatery['name'] = $this->getcategoryName($verorlocationvalue->category_id);
				 	$singcatery['slug'] = $this->getcategorySlugName($verorlocationvalue->category_id);
				 	$vendorcatecoryarray[] = $singcatery;
				 } 

				 $data['vendor_locations']   = $locationarray;
				 $data['vendor_categories']   = $vendorcatecoryarray;

		   }


			

			return response()->json(['status'=>1,'message'=>'Success','data'=>$data]);
		  }else{ 
			return response()->json(['status'=>0,'message'=>'Invalid username or password','data'=>array('id'=>0)]);
		  }
	}

	function app_vendor_product_listing(Request $request){
		if(empty(trim($request->input('userid')))){
			return response()->json(['status'=>0,'message'=>'Please enter userid','data'=>array('id'=>0)]);
		}
		elseif(empty(trim($request->input('role_id')))){
			return response()->json(['status'=>0,'message'=>'Please enter role id','data'=>array('id'=>0)]);
		}
		$user_id = $request->input('userid');
		$roleId  = $request->input('role_id');
		$language_id = '1';


		$products = DB::table('products')
		->where('user_id', $user_id)
		->get();
		$data = array(); 
		foreach ($products as $key => $value) {
			$singleproduct = array();
			$productdata  =  $this->getproductbyId($value->products_id);

			$singleproduct['product_id'] = $value->products_id;
			$categoryId  								= $this->getproductcategoryId($value->products_id);
			$categoryname 								= $this->getproductcategoryName($categoryId);
			$singleproduct['category_name'] 			= $categoryname;
			$singleproduct['prodcut_name'] 				= ($productdata['prodcut_name']) ? $productdata['prodcut_name']:'';
			$singleproduct['products_description'] 		= ($productdata['products_description']) ? $productdata['products_description']:'';
			$singleproduct['products_viewed'] 			= ($productdata['products_viewed']) ? $productdata['products_viewed']:0;
			
			$singleproduct['language_id'] 				= ($productdata['language_id']) ? $productdata['language_id']:'';
			$singleproduct['products_url'] 				= ($productdata['products_url']) ? $productdata['products_url']:'';
			$singleproduct['products_quantity'] 		= ($value->products_quantity) ? $value->products_quantity:'';
			$singleproduct['products_model'] 			= ($value->products_model) ? $value->products_model:'';
			$productimage 								= $this->getimagebyId($value->products_image);
			$singleproduct['products_image'] 			= ($productimage) ? $productimage:'';
			$singleproduct['products_price'] 			= ($value->products_price) ? $value->products_price:'';
			$singleproduct['products_weight'] 			= ($value->products_weight) ? $value->products_weight:'';
			$singleproduct['products_weight_unit'] 		= ($value->products_weight_unit) ? $value->products_weight_unit:'';
			$singleproduct['products_slug'] 			= ($value->products_slug) ? $value->products_slug:'';
			$singleproduct['products_type'] 			= ($value->products_type) ? $value->products_type:'';
			$singleproduct['created_at'] 				= $value->created_at;
			$singleproduct['updated_at'] 				= $value->updated_at;
			$data[] = $singleproduct;
		}

		if(!empty($products)){
	   		return response()->json(['status'=>1,'message'=>'Success','data'=>$data]);	
		}else{
			return response()->json(['status'=>1,'message'=>'No Products Found','data'=>0]);
		}

	}

	function app_vendor_orders_listing(Request $request){

		if(empty(trim($request->input('userid')))){
			return response()->json(['status'=>0,'message'=>'Please enter userid','data'=>array('id'=>0)]);
		}
		elseif(empty(trim($request->input('role_id')))){
			return response()->json(['status'=>0,'message'=>'Please enter role id','data'=>array('id'=>0)]);
		}elseif(empty(trim($request->input('order_type')))){
			return response()->json(['status'=>0,'message'=>'Please enter order type','data'=>array('id'=>0)]);
		}
		$user_id = $request->input('userid');
		$roleId  = $request->input('role_id');
		$type  = $request->input('order_type');
		$language_id = '1';
        $myorderids = $this->getuservenderproducts($user_id);

		 $orders = DB::table('orders')->orderBy('created_at', 'DESC');
		 $statusids =array();
                if($type=='completed'){
                    $statusids = array(2,3,4);
                }elseif($type=='process'){
                    $statusids = array(1,5,6,7);
                }
                if(count($statusids)<1){
                   $ordersbystatus = DB::table('orders_status_history')->where('orders_status_id', '=', $request->status)->whereIn('orders_id',$myorderids)->get(); 
                }else{
                   $ordersbystatus = DB::table('orders_status_history')->whereIn('orders_id',$myorderids)->whereIn('orders_status_id', $statusids)->get(); 
                    $myorderids = array();
                    foreach($ordersbystatus as $singleordk){
                       $myorderids[] =  $singleordk->orders_id;
                    }
                }
            if(isset($request->status) && !empty($request->status)){
                $ordersbystatus = DB::table('orders_status_history')->where('orders_status_id', '=', $request->status)->whereIn('orders_id',$myorderids)->get();
                $myorderids = array();
                foreach($ordersbystatus as $singleordk){
                   $myorderids[] =  $singleordk->orders_id;
                }
            }


            $orders = $orders->where('customers_id', '!=', '')->whereIn('orders_id',$myorderids)->orderby('orders_id','DESC')->get();

            

            $index = 0;
            $total_price = array();

            foreach ($orders as $orders_data) {

            $order_products_array = DB::table('orders_products')->select('orders_products.products_id', 'orders_products.products_name', 'orders_products.final_price', 'orders_products.products_quantity')->where('orders_products.orders_id', '=', $orders_data->orders_id)->get();

            $orders[$index]->order_products_array = $order_products_array;

            $orders_products = DB::table('orders_products')->sum('final_price');

            $orders[$index]->total_price = $orders_products;

            $orders_status_history = DB::table('orders_status_history')
                ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
                ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
                ->select('orders_status_description.orders_status_name', 'orders_status_description.orders_status_id')
                ->where('orders_status_description.language_id', '=', $language_id)
                ->where('orders_id', '=', $orders_data->orders_id)
                ->where('role_id', '<=', 2)
                ->orderby('orders_status_history.orders_status_history_id', 'DESC')->limit(1)->get();

            $orders[$index]->orders_status_id = $orders_status_history[0]->orders_status_id;
            $orders[$index]->orders_status = $orders_status_history[0]->orders_status_name;
            $index++;

        }

            $orderarray = array();
            foreach ($orders as $key => $value) {
             	$signlearray['orders_id'] 		= $value->orders_id;
             	$signlearray['customers_id'] 	= $value->customers_id;
             	$signlearray['customers_name'] 	= $value->customers_name;
             	$signlearray['date_purchased'] 	= $value->date_purchased;
             	$signlearray['currency'] 		= $value->currency;
             	$signlearray['order_price']     = $value->order_price;
             	$signlearray['orders_status'] 	= $value->orders_status;
             	$signlearray['orders_status_id'] 	= $value->orders_status_id;
             	$signlearray['shipping_cost'] 	= $value->shipping_cost;
             	$signlearray['billing_address'] 	= $value->billing_street_address;
             	$signlearray['billing_state'] 	= $value->billing_city. ", ". $value->billing_state;
             	$signlearray['orders_products'] 	= $value->order_products_array;
             	$orderarray[] = $signlearray;

             } 
            return response()->json(['status'=>1,'message'=>'Success','data'=>$orderarray]);	
		
	}

	function app_vendor_update_qunty(Request $request)
	{
		if(empty(trim($request->input('userid')))){
			return response()->json(['status'=>0,'message'=>'Please enter userid','data'=>array('id'=>0)]);
		}elseif(empty(trim($request->input('qnty')))){
			return response()->json(['status'=>0,'message'=>'Please enter quantity ','data'=>array('id'=>0)]);
		}elseif(empty(trim($request->input('product_id')))){
			return response()->json(['status'=>0,'message'=>'Please enter product id','data'=>array('id'=>0)]);
		}
		$user_id = $request->input('userid');
		$product_id = $request->input('product_id');
		$qnty    = $request->input('qnty');

		$data =  DB::table('products')->where(['user_id' => $user_id, 'products_id' => $product_id])->update(['products_quantity' => $qnty]);
		if($data == true){
	   		return response()->json(['status'=>1,'message'=>'Success updated','data'=>0]);	
		}else{
			return response()->json(['status'=>0,'message'=>'something probelm updated','data'=>0]);
		}		
	}

	function app_update_product_price(Request $request)
	{
		if(empty(trim($request->input('userid')))){
			return response()->json(['status'=>0,'message'=>'Please enter userid','data'=>array('id'=>0)]);
		}elseif(empty(trim($request->input('price')))){
			return response()->json(['status'=>0,'message'=>'Please enter price ','data'=>array('id'=>0)]);
		}elseif(empty(trim($request->input('product_id')))){
			return response()->json(['status'=>0,'message'=>'Please enter product id','data'=>array('id'=>0)]);
		}
		$user_id = $request->input('userid');
		$product_id = $request->input('product_id');
		$price    = $request->input('price');

		$data =  DB::table('products')->where(['user_id' => $user_id, 'products_id' => $product_id])->update(['products_price' => $price]);
		if($data == true){
	   		return response()->json(['status'=>1,'message'=>'Success updated','data'=>0]);	
		}else{
			return response()->json(['status'=>0,'message'=>'something probelm','data'=>0]);
		}		
	}

	function app_update_order_status(Request $request)
	{
		if(empty(trim($request->input('orders_status')))){
			return response()->json(['status'=>0,'message'=>'Please enter orders_status','data'=>array('id'=>0)]);
		}elseif(empty(trim($request->input('old_orders_status')))){
			return response()->json(['status'=>0,'message'=>'Please enter old_orders_status ','data'=>array('id'=>0)]);
		}elseif(empty(trim($request->input('orders_id')))){
			return response()->json(['status'=>0,'message'=>'Please enter orders_id','data'=>array('id'=>0)]);
		}elseif(empty(trim($request->input('userid')))){
			return response()->json(['status'=>0,'message'=>'Please enter userid','data'=>array('id'=>0)]);
		}

		$orders_status = $request->input('orders_status');
		$old_orders_status = $request->input('old_orders_status');
		$orders_id    = $request->input('orders_id');
		$userid = $request->input('userid');

		if ($old_orders_status == $orders_status) {
            return response()->json(['status'=>2,'message'=>'Cannot update to same status','data'=>0]);
        } else {
            //update order
            $date_added = date('Y-m-d h:i:s');

            $orders_status = $request->input('orders_status');
        	$old_orders_status = $request->input('old_orders_status');
        	

        	$comments = "comment";
        	$orders_id = $request->input('orders_id');

        	$status = DB::table('orders_status')->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
            ->where('orders_status_description.language_id', '=', 1)->where('role_id', '<=', 2)->where('orders_status_description.orders_status_id', '=', $orders_status)->get();

            $orders_history_id = DB::table('orders_status_history')->insertGetId(
            ['orders_id' => $orders_id,
                'orders_status_id' => $orders_status,
                'date_added' => $date_added,
                'customer_notified' => '1',
                'comments' => $comments,
            ]);

            if ($orders_status == '2') {

	            $orders_products = DB::table('orders_products')->where('orders_id', '=', $orders_id)->get();

	            foreach ($orders_products as $products_data) {
	                DB::table('products')->where('products_id', $products_data->products_id)->update([
	                    'products_quantity' => DB::raw('products_quantity - "' . $products_data->products_quantity . '"'),
	                    'products_ordered' => DB::raw('products_ordered + 1'),
	                ]);
	            }
        	}

        	if ($orders_status == '3') {

            $orders_products = DB::table('orders_products')->where('orders_id', '=', $orders_id)->get();

            foreach ($orders_products as $products_data) {

                $product_detail = DB::table('products')->where('products_id', $products_data->products_id)->first();
                $date_added = date('Y-m-d h:i:s');
                $inventory_ref_id = DB::table('inventory')->insertGetId([
                    'products_id' => $products_data->products_id,
                    'stock' => $products_data->products_quantity,
                    'admin_id' => $userid,
                    'created_at' => $date_added,
                    'stock_type' => 'in',

                ]);
                //dd($product_detail);
                if ($product_detail->products_type == 1) {
                    $product_attribute = DB::table('orders_products_attributes')
                        ->where([
                            ['orders_products_id', '=', $products_data->orders_products_id],
                            ['orders_id', '=', $products_data->orders_id],
                        ])
                        ->get();

                    foreach ($product_attribute as $attribute) {
                        //dd($attribute->products_options,$attribute->products_options_values);
                        $prodocuts_attributes = DB::table('products_attributes')
                            ->join('products_options_descriptions', 'products_options_descriptions.products_options_id', '=', 'products_attributes.options_id')
                            ->join('products_options_values_descriptions', 'products_options_values_descriptions.products_options_values_id', '=', 'options_values_id')
                            ->where('products_options_values_descriptions.options_values_name', $attribute->products_options_values)
                            ->where('products_options_descriptions.options_name', $attribute->products_options)
                            ->select('products_attributes.products_attributes_id')
                            ->first();

                        DB::table('inventory_detail')->insert([
                            'inventory_ref_id' => $inventory_ref_id,
                            'products_id' => $products_data->products_id,
                            'attribute_id' => $prodocuts_attributes->products_attributes_id,
                        ]);

                    }

                }
            }
        }

        $orders = DB::table('orders')->where('orders_id', '=', $orders_id)
            ->where('customers_id', '!=', '')->get();

        $data = array();
        $data['customers_id'] = $orders[0]->customers_id;
        $data['orders_id'] = $orders_id;
        $data['status'] = $status[0]->orders_status_name;



            return response()->json(['status'=>1,'message'=>'Order Status Updated','data'=>$data]);
        }

	}

    function getcountryName($id){
    	$country =  DB::table('country')->where('id', $id)->first();
    	return $country->name;
    }

    function getstateName($id){
    	$state =  DB::table('states')->where('id', $id)->first();
    	return $state->name;
    }

    function getcityName($id){
    	$city =  DB::table('cities')->where('id', $id)->first();
    	return $city->name;
    }

    function getlocationName($id){
    	$city =  DB::table('locations')->where('id', $id)->first();
    	return $city->location;
    }
    function getcategoryName($id){
    	$category =  DB::table('categories_description')->where('categories_id', $id)->first();
    	return $category->categories_name;
    }
    function getcategorySlugName($id){
    	$category =  DB::table('categories')->where('categories_id', $id)->first();
    	return $category->categories_slug;
    }
    function getproductbyId($id){
    	$name =  DB::table('products_description')->where('products_id', $id)->first();
    	return ['prodcut_name' => $name->products_name, 'products_description' => $name->products_description, 'language_id' => $name->language_id, 'products_url' => $name->products_url, 'products_viewed' => $name->products_viewed] ;
    }

    function getproductcategoryId($id){
      $categoryid = DB::table('products_to_categories')->where('products_id', $id)->first();
      if (isset($categoryid->categories_id)) {
    		return $categoryid->categories_id;
    	}
      return $categoryid;
    }

    function getproductcategoryName($id){
    	$productsid =  DB::table('categories_description')->where('categories_id', $id)->first();
    	if (isset($productsid->categories_name)) {
    		return $productsid->categories_name;
    	}
    	return $productsid;
    }

    function getimagebyId($id){
    	$image =  DB::table('image_categories')->where('image_id', $id)->first();
    	return $image->path;
    }

    function getuservenderproducts($user_id=0){
         $product = DB::table('products')->where('user_id', '=', $user_id)->get();
         $userporudctsarray = array(0);
         foreach($product as $singleprd){
            $userporudctsarray[] =$singleprd->products_id;  
         }
        $allorderids = array(0);
        $orderids = DB::table('orders_products')->whereIn('products_id',$userporudctsarray)->get();
        foreach($orderids as $singleorder){
            $allorderids[$singleorder->orders_id] = $singleorder->orders_id;
        }
         return $allorderids;
     } 	
}