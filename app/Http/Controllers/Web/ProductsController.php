<?php
namespace App\Http\Controllers\Web;

//validator is builtin class in laravel
use App\Models\Web\Currency;
use App\Models\Web\Index;
//for password encryption or hash protected
use App\Models\Web\Languages;

//for authenitcate login data
use App\Models\Web\Products;
use Auth;

//for requesting a value
use DB;
//for Carbon a value
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Lang;
use Session;
//custom By Rp
use App\Models\Web\City;
use App\Models\Web\States;
use App\Models\Web\Location;
use App\Models\Web\Vendor_location; 
use App\Models\Web\Vendors;
use Response;
//email

class ProductsController extends Controller
{
    public function __construct(
        Index $index,
        Languages $languages,
        Products $products,
        Currency $currency
    ) {
        $this->index = $index;
        $this->languages = $languages;
        $this->products = $products;
        $this->currencies = $currency;
        $this->theme = new ThemeController();
    }

    public function reviews(Request $request)
    {
        if (Auth::guard('customer')->check()) {
            $check = DB::table('reviews')
                ->where('customers_id', Auth::guard('customer')->user()->id)
                ->where('products_id', $request->products_id)
                ->first();

            if ($check) {
                return 'already_commented';
            }
            $id = DB::table('reviews')->insertGetId([
                'products_id' => $request->products_id,
                'reviews_rating' => $request->rating,
                'customers_id' => Auth::guard('customer')->user()->id,
                'customers_name' => Auth::guard('customer')->user()->first_name,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
 
            DB::table('reviews_description')
                ->insert([
                    'review_id' => $id,
                    'language_id' => Session::get('language_id'),
                    'reviews_text' => $request->reviews_text,
                ]);
            return 'done';
        } else {
            return 'not_login';

        }
    }

    //shop
    public function shop(Request $request)
    {  
        $area = 5;
        $title = array('pageTitle' => Lang::get('website.Shop'));
        $result = array();

        $result['commonContent'] = $this->index->commonContent();
        $final_theme = $this->theme->theme();
        if (!empty($request->page)) {
            $page_number = $request->page;
        } else {
            $page_number = 0;
        }

        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 15;
        }

        if (!empty($request->type)) {
            $type = $request->type;
        } else {
            $type = '';
        }

        //min_max_price
        if (!empty($request->price)) {
            $d = explode(";", $request->price);
            $min_price = $d[0];
            $max_price = $d[1];
        } else {
            $min_price = '';
            $max_price = '';
        }
        //category
        if (!empty($request->category) and $request->category != 'all') {
            $category = $this->products->getCategories($request);

            $categories_id = $category[0]->categories_id;
            //for main
            if ($category[0]->parent_id == 0) {
                $category_name = $category[0]->categories_name;
                $sub_category_name = '';
                $category_slug = '';
            } else {
                //for sub
                $main_category = $this->products->getMainCategories($category[0]->parent_id);

                $category_slug = $main_category[0]->categories_slug;
                $category_name = $main_category[0]->categories_name;
                $sub_category_name = $category[0]->categories_name;
            }

        } else {
            $categories_id = '';
            $category_name = '';
            $sub_category_name = '';
            $category_slug = '';
        }

        $result['category_name'] = $category_name;
        $result['category_slug'] = $category_slug;
        $result['sub_category_name'] = $sub_category_name;

        //search value
        if (!empty($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        
        $filters = array();
        if (!empty($request->filters_applied) and $request->filters_applied == 1) {
            $index = 0;
            $options = array();
            $option_values = array();

            $option = $this->products->getOptions();

            foreach ($option as $key => $options_data) {
                $option_name = str_replace(' ', '_', $options_data->products_options_name);

                if (!empty($request->$option_name)) {
                    $index2 = 0;
                    $values = array();
                    foreach ($request->$option_name as $value) {
                        $value = $this->products->getOptionsValues($value);
                        $option_values[] = $value[0]->products_options_values_id;
                    }
                    $options[] = $options_data->products_options_id;
                }
            }

            $filters['options_count'] = count($options);

            $filters['options'] = implode($options, ',');
            $filters['option_value'] = implode($option_values, ',');

            $filters['filter_attribute']['options'] = $options;
            $filters['filter_attribute']['option_values'] = $option_values;

            $result['filter_attribute']['options'] = $options;
            $result['filter_attribute']['option_values'] = $option_values;
        }
       
        
         
        $data = array('page_number' => $page_number, 'type' => $type, 'limit' => $limit,
            'categories_id' => $categories_id, 'search' => $search,
            'filters' => $filters, 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price, 'area' => $area,'requestdata'=>$request->all());

        $products = $this->products->products($data);
       //echo '<pre>'; print_r($products); echo '</pre>';
        $result['products'] = $products;

        $data = array('limit' => $limit, 'categories_id' => $categories_id);
        $filters = $this->filters($data);
        
        $vendors = array();
        if(!empty($_SESSION["your_state"]) && !empty($_SESSION["your_city"]) && !empty($_SESSION["your_area"])){
            $locationvendor = $this->findvendorwithloc();
            //print_r($locationvendor);die;
           // 
            
            $vendors = DB::table('vendors')->whereIn('id',$locationvendor)->where('deleted_at',0)->where('status',1)->get();
            //echo "<pre>"; print_r($vendors);die;
            $dataarrayall = array();
            foreach($vendors as $keyskljlk){
                $dataarray = array();
                $dataarray['value'] = $keyskljlk->business_name.' ('.$keyskljlk->name .')';
                $dataarray['value_id'] = $keyskljlk->user_id;
                $dataarray['checked'] = '';
                if(isset($request->Vendor)){
                    if(in_array($keyskljlk->user_id,$request->Vendor)){
                      $dataarray['checked'] = 'checked';  
                    }
                }
                $dataarrayall[] = $dataarray;
            }
            $filters['attr_data'][] = array(
                        'option'=>array(
                            'name'=>'Vendor'
                        ),
                        'values'=>$dataarrayall
             
            );
            
        }
         //echo "<pre>"; print_r($filters);die;
        $result['filters'] = $filters;

        $cart = '';
        $result['cartArray'] = $this->products->cartIdArray($cart);

        if ($limit > $result['products']['total_record']) {
            $result['limit'] = $result['products']['total_record'];
        } else {
            $result['limit'] = $limit;
        }
       
        
       
        //liked products
        $result['liked_products'] = $this->products->likedProducts();
        $result['categories'] = $this->products->categories();
        $result['vendors'] = $vendors;

        $result['min_price'] = $min_price;
        //$result['vendors'] = $vendors;
        $result['max_price'] = $max_price;
//echo "<pre>";
     //  print_r($result);die;
        return view("web.shop", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);

    }
    function findvendorwithloc(){
        
         $locationid = $_SESSION["your_area"];
         $product = DB::table('vendors_location')->where('location_name', '=', $locationid)->get();
         $userporudctsarray = array(223123102);
         foreach($product as $singleprd){
            $userporudctsarray[$singleprd->vendor_id] =$singleprd->vendor_id;  
         }
         return $userporudctsarray;
      
    
    }

    public function filterProducts(Request $request)
    {

        //min_price
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        //max_price
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }

        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 15;
        }

        if (!empty($request->type)) {
            $type = $request->type;
        } else {
            $type = '';
        }

        //if(!empty($request->category_id)){
        if (!empty($request->category) and $request->category != 'all') {
            $category = DB::table('categories')->leftJoin('categories_description', 'categories_description.categories_id', '=', 'categories.categories_id')->where('categories_slug', $request->category)->where('language_id', Session::get('language_id'))->get();

            $categories_id = $category[0]->categories_id;
        } else {
            $categories_id = '';
        }

        //search value
        if (!empty($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }

        //min_price
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        //max_price
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }

        if (!empty($request->filters_applied) and $request->filters_applied == 1) {
            $filters['options_count'] = count($request->options_value);
            $filters['options'] = $request->options;
            $filters['option_value'] = $request->options_value;
        } else {
            $filters = array();
        }

        $data = array('page_number' => $request->page_number, 'type' => $type, 'limit' => $limit, 'categories_id' => $categories_id, 'search' => $search, 'filters' => $filters, 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $products = $this->products->products($data);

        $result = array();
        $result['commonContent'] = $this->index->commonContent();
        
        $result['products'] = $products;

        $cart = '';
        $result['cartArray'] = $this->products->cartIdArray($cart);
        $result['limit'] = $limit;
        return view("web.filterproducts")->with('result', $result);

    }

    public function ModalShow(Request $request)
    {
        $result = array();
        $result['commonContent'] = $this->index->commonContent();
        $final_theme = $this->theme->theme();
        //min_price
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        //max_price
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }

        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 15;
        }

        $products = $this->products->getProductsById($request->products_id);

        $products = $this->products->getProductsBySlug($products[0]->products_slug);
        //category
        $category = $this->products->getCategoryByParent($products[0]->products_id);

        if (!empty($category) and count($category) > 0) {
            $category_slug = $category[0]->categories_slug;
            $category_name = $category[0]->categories_name;
        } else {
            $category_slug = '';
            $category_name = '';
        }
        $sub_category = $this->products->getSubCategoryByParent($products[0]->products_id);

        if (!empty($sub_category) and count($sub_category) > 0) {
            $sub_category_name = $sub_category[0]->categories_name;
            $sub_category_slug = $sub_category[0]->categories_slug;
        } else {
            $sub_category_name = '';
            $sub_category_slug = '';
        }

        $result['category_name'] = $category_name;
        $result['category_slug'] = $category_slug;
        $result['sub_category_name'] = $sub_category_name;
        $result['sub_category_slug'] = $sub_category_slug;

        $isFlash = $this->products->getFlashSale($products[0]->products_id);

        if (!empty($isFlash) and count($isFlash) > 0) {
            $type = "flashsale";
        } else {
            $type = "";
        }

        $data = array('page_number' => '0', 'type' => $type, 'products_id' => $products[0]->products_id, 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $detail = $this->products->products($data);
        $result['detail'] = $detail;
        $postCategoryId = '';
        if (!empty($result['detail']['product_data'][0]->categories) and count($result['detail']['product_data'][0]->categories) > 0) {
            $i = 0;
            foreach ($result['detail']['product_data'][0]->categories as $postCategory) {
                if ($i == 0) {
                    $postCategoryId = $postCategory->categories_id;
                    $i++;
                }
            }
        }

        $data = array('page_number' => '0', 'type' => '', 'categories_id' => $postCategoryId, 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $simliar_products = $this->products->products($data);
        $result['simliar_products'] = $simliar_products;

        $cart = '';
        $result['cartArray'] = $this->products->cartIdArray($cart);

        //liked products
        $result['liked_products'] = $this->products->likedProducts();
        return view("web.common.modal1")->with('result', $result);
    }

    //access object for custom pagination
    public function accessObjectArray($var)
    {
        return $var;
    }

    //productDetail
    public function productDetail(Request $request)
    {

        $title = array('pageTitle' => Lang::get('website.Product Detail'));
        $result = array();
        $result['commonContent'] = $this->index->commonContent();
        $final_theme = $this->theme->theme();
        //min_price
        if (!empty($request->min_price)) {
            $min_price = $request->min_price;
        } else {
            $min_price = '';
        }

        //max_price
        if (!empty($request->max_price)) {
            $max_price = $request->max_price;
        } else {
            $max_price = '';
        }

        if (!empty($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 15;
        }

        $products = $this->products->getProductsBySlug($request->slug);

        //category
        $category = $this->products->getCategoryByParent($products[0]->products_id);

        if (!empty($category) and count($category) > 0) {
            $category_slug = $category[0]->categories_slug;
            $category_name = $category[0]->categories_name;
        } else {
            $category_slug = '';
            $category_name = '';
        }
        $sub_category = $this->products->getSubCategoryByParent($products[0]->products_id);

        if (!empty($sub_category) and count($sub_category) > 0) {
            $sub_category_name = $sub_category[0]->categories_name;
            $sub_category_slug = $sub_category[0]->categories_slug;
        } else {
            $sub_category_name = '';
            $sub_category_slug = '';
        }

        $result['category_name'] = $category_name;
        $result['category_slug'] = $category_slug;
        $result['sub_category_name'] = $sub_category_name;
        $result['sub_category_slug'] = $sub_category_slug;

        $isFlash = $this->products->getFlashSale($products[0]->products_id);

        if (!empty($isFlash) and count($isFlash) > 0) {
            $type = "flashsale";
        } else {
            $type = "";
        }
        $postCategoryId = '';
        $data = array('page_number' => '0', 'type' => $type, 'products_id' => $products[0]->products_id, 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $detail = $this->products->products($data);
        $result['detail'] = $detail;
        if (!empty($result['detail']['product_data'][0]->categories) and count($result['detail']['product_data'][0]->categories) > 0) {
            $i = 0;
            foreach ($result['detail']['product_data'][0]->categories as $postCategory) {
                if ($i == 0) {
                    $postCategoryId = $postCategory->categories_id;
                    $i++;
                }
            }
        }

        $data = array('page_number' => '0', 'type' => '', 'categories_id' => $postCategoryId, 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $simliar_products = $this->products->products($data);
        $result['simliar_products'] = $simliar_products;

        $vendor_data = array('page_number' => '0', 'type' => '', 'vendor_id' => $products[0]->user_id, 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);

        $vendor_products = $this->products->products($vendor_data);
        $result['vendor_products'] = $vendor_products;

        $cart = '';
        $result['cartArray'] = $this->products->cartIdArray($cart);

        //liked products
        $result['liked_products'] = $this->products->likedProducts();

        $data = array('page_number' => '0', 'type' => 'topseller', 'limit' => $limit, 'min_price' => $min_price, 'max_price' => $max_price);
        $top_seller = $this->products->products($data);
		$result['top_seller'] = $top_seller;
		
		
		//dd($result);
        return view("web.detail", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);
    }

    //filters
    public function filters($data)
    {
        $response = $this->products->filters($data);
        return ($response);
    }

    //getquantity
    public function getquantity(Request $request)
    {
        $data = array();
        $data['products_id'] = $request->products_id;
        $data['attributes'] = $request->attributeid;

        $result = $this->products->productQuantity($data);
        print_r(json_encode($result));
    }
    
    public function getState(Request $request){ 
        session_start(); 
        $data = $request->all();
        
        $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=".$data['latitude'].",".$data['longitude']."&key=AIzaSyA0zH-Je4_9bneU2V1C79spjiadqEVEmGI");
        $json = json_decode($json);



        
        $full_address = $json->results[0]->address_components;



        for($j=0;$j<count($full_address);$j++){
            
            if ($full_address[$j]->types[0] == "administrative_area_level_2") {
                $_SESSION["geocityname"] = $full_address[$j]->long_name;
            }

            if ($full_address[$j]->types[0] == "administrative_area_level_1") {
                $_SESSION["geostatename"] = $full_address[$j]->long_name;
            }

            if ($full_address[$j]->types[0] == "postal_code") {
                $_SESSION["geoareaname"] = $full_address[$j]->long_name;
            }

        }

        /* echo "<pre>"; 
         print_r($_SESSION["geoareaname"]);
         die;*/

        $selected_state = States::where('name', $_SESSION["geostatename"])->get();



        $_SESSION["geostatename"] = $selected_state[0]['id'];

        $selected_city = City::where('name', $_SESSION["geocityname"])->get();

           

        $_SESSION["geocityname"] = $selected_city[0]['id'];

       
         $checklocation = Location::where('location', $_SESSION["geoareaname"])->where('deleted_at', 0)->first();
         if(empty($checklocation)){
            $location = Location::insert(['country_id' => $data['country_id'], 'state_id' => $selected_state[0]['id'], 'city_id' => $selected_city[0]['id'], 'location' => $_SESSION["geoareaname"]]);
         }else{
          $selected_area = Location::where('location', $_SESSION["geoareaname"])->get();
          $_SESSION["geoareaname"] = $selected_area[0]['id'];
         }

        if (!empty($data)) {
            $data = States::where('country_id', $data['country_id'])->get();
            return Response::json(['status' => true, 'data' => $data]);
        }


        return Response::json(['status' => true, 'data' => $data]);
    }
    
    public function getCity(Request $request){ 
        session_start(); 
        $data = $request->all();

        if (!empty($data)) {
            $data = City::where('state_id', $data['state_id'])->get();
            return Response::json(['status' => true, 'data' => $data]);
        }
    }
    
    public function getArea(Request $request){ 
        session_start(); 
        $data = $request->all();


        if (!empty($data)) {
            $data = Location::where('city_id', $data['city_id'])->get();
            return Response::json(['status' => true, 'data' => $data]);
        }
    }

    public function autoDetect(Request $request){ 
         session_start(); 
         $state = $_SESSION["geostatename"]; 
         $city = $_SESSION["geocityname"];
         $area = $_SESSION["geoareaname"];
         
         $_SESSION["your_state"]=$state;
         $_SESSION["your_city"]=$city;
         $_SESSION["your_area"]=$area;
         //$request->session()->put('your_area', $area);
         //echo $request->session()->get('your_area');
                        $vendors = Vendor_location::with('vendor')->where('location_name',$area)->get()->toArray();
                        $venId = array();
                        $venData = array();
                        foreach($vendors as $ven){ 
                            if(@$ven['vendor']['user_id']!=0){
                            $venId[] = @$ven['vendor']['user_id'];
                            }
                        }
                        $products = Products::whereIn('user_id', $venId)->get()->toArray();
        
        
        
//         session(['out_of_stock' => 0]);
//            $baskit_id = $request->id;

                        $checkcart = DB::table('customers_basket')
                                ->where('customers_basket.session_id', '=', Session::getId())
                                ->get();
                    if($checkcart->count()>0){
                        foreach($checkcart as $singlecart){
                            DB::table('customers_basket')->where([
                                ['customers_basket.customers_basket_id', '=', $singlecart->customers_basket_id],
                            ])->delete();

                            DB::table('customers_basket_attributes')->where([
                                ['customers_basket_id', '=', $singlecart->customers_basket_id],
                            ])->delete();
                        }
                    }
          
                        return redirect()->back();
    }
    
    public function getProductsOfCity(Request $request){ 
         $data = $request->all();
         $state = $data['state']; 
         $city = $data['city'];
         $area = $data['area'];
         session_start(); 
         $_SESSION["your_state"]=$state;
         $_SESSION["your_city"]=$city;
         $_SESSION["your_area"]=$area;
         //$request->session()->put('your_area', $area);
         //echo $request->session()->get('your_area');
                        $vendors = Vendor_location::with('vendor')->where('location_name',$area)->get()->toArray();
                        $venId = array();
                        $venData = array();
                        foreach($vendors as $ven){ 
                            if(@$ven['vendor']['user_id']!=0){
                            $venId[] = @$ven['vendor']['user_id'];
                            }
                        }
                        $products = Products::whereIn('user_id', $venId)->get()->toArray();
        
        
        
//         session(['out_of_stock' => 0]);
//            $baskit_id = $request->id;

                        $checkcart = DB::table('customers_basket')
                                ->where('customers_basket.session_id', '=', Session::getId())
                                ->get();
                    if($checkcart->count()>0){
                        foreach($checkcart as $singlecart){
                            DB::table('customers_basket')->where([
                                ['customers_basket.customers_basket_id', '=', $singlecart->customers_basket_id],
                            ])->delete();

                            DB::table('customers_basket_attributes')->where([
                                ['customers_basket_id', '=', $singlecart->customers_basket_id],
                            ])->delete();
                        }
                    }
          
                        return redirect()->back();
    }


}