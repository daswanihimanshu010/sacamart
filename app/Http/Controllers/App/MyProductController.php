<?php

namespace App\Http\Controllers\App;

//validator is builtin class in laravel
use Validator;
use DB;
use DateTime;
use Hash;
use Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\AppModels\Product;
use Carbon;


class MyProductController extends Controller
{

	//get allcategories
	public function allcategories(Request $request){
    $categoryResponse = Product::allcategories($request);
		print $categoryResponse;
	}

	//getallproducts
	public function getallproducts(Request $request){
    $categoryResponse = Product::getallproducts($request);
		print $categoryResponse;
	}

	// likeproduct
	public function likeproduct(Request $request){
    $categoryResponse = Product::likeproduct($request);
		print $categoryResponse;
	}

  public function notifyproduct(Request $request){
    $categoryResponse = Product::notifyproduct($request);
    print $categoryResponse;
  }

	// likeProduct
	public function unlikeproduct(Request $request){
    $categoryResponse = Product::unlikeproduct($request);
		print $categoryResponse;
	}

	//getfilters
	public function getfilters(Request $request){
    $categoryResponse = Product::getfilters($request);
		print $categoryResponse;
		}

	//getfilterproducts
	public function getfilterproducts(Request $request){
      $categoryResponse = Product::getfilterproducts($request);
			print $categoryResponse;
		}

	//getsearchdata
	public function getsearchdata(Request $request){
    $categoryResponse = Product::getsearchdata($request);
		print $categoryResponse;
	}

	//getquantity
	public function getquantity(Request $request){
    $response = Product::getquantity($request);
		print $response;
	}

	//shippingMethods
	public function shppingbyweight(Request $request){
    $categoryResponse = Product::shppingbyweight($request);
		print $categoryResponse;

	}
    public function findlocation(Request $request){
        //print_r($request->all());die;
         $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=".$request->latitude.",".$request->longitude."&key=AIzaSyA0zH-Je4_9bneU2V1C79spjiadqEVEmGI");
        $json = json_decode($json);
        
        $full_address = $json->results[0]->address_components;
       
        for($j=0;$j<count($full_address);$j++){
            
            if ($full_address[$j]->types[0] == "locality") {
                $geocityname = $full_address[$j]->long_name;
            }

            if ($full_address[$j]->types[0] == "administrative_area_level_1") {
                $geostatename = $full_address[$j]->long_name;
            }

            if ($full_address[$j]->types[0] == "postal_code") {
                $geoareaname = $full_address[$j]->long_name;
            }

        }
         //print_r($full_address);die;
//
        $selected_state = DB::table('states')->where('name', $geostatename)->get();
        //print_r($selected_state);die;
        $stateid =0;
        if($selected_state->count()>0){
          $stateid = $selected_state[0]->id;  
        }
        //echo $stateid;die;
//        
//
        $selected_city = DB::table('cities')->where('name', $geocityname)->get();
        $cityid = 0;
        if($selected_city->count()>0){
            $cityid = $selected_city[0]->id;
        }
        
        $selected_area = DB::table('locations')->where('location', $geoareaname)->get();
        $aread_id =0;
        if($selected_area->count()>0){
            $aread_id = $selected_area[0]->id;
        }
      $datanew = array();
      $datanew['state'] = $geostatename;
      $datanew['state_id'] = $stateid;    
      $datanew['city'] = $geocityname;
      $datanew['city_id'] = $cityid;
      $datanew['area'] = $geoareaname;
      $datanew['area_id'] = $aread_id;

      $responseData = array('success' => '1', 'message' => "Success", 'data' => $datanew);
      $response = json_encode($responseData);
        echo $response;exit;
     
        
    }

}
