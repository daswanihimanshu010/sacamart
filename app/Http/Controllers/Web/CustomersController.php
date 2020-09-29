<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\AlertController;
use App\Models\Web\Cart;
use App\Models\Web\Currency;
use App\Models\Web\Customer;
use App\Models\Web\Index;
use App\Models\Web\Languages;
use App\Models\Web\Products;
use App\Models\Web\Categories;
use App\User;
use App\Vendor;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Client;

use Lang;
use Session;
use Socialite;
use Validator;
use Hash;

class CustomersController extends Controller
{

    public function __construct(
        Index $index,
        Languages $languages,
        Products $products,
        Currency $currency,
        Customer $customer,
        Categories $category,
        Cart $cart
    ) {
        $this->index = $index;
        $this->languages = $languages;
        $this->products = $products;
        $this->category = $category;
        $this->currencies = $currency;
        $this->customer = $customer;
        $this->cart = $cart;
        $this->theme = new ThemeController();
    }

    public function signup(Request $request)
    {
        $final_theme = $this->theme->theme();
        if (auth()->guard('customer')->check()) {
            return redirect('/');
        } else {
            $title = array('pageTitle' => Lang::get("website.Sign Up"));
            $result = array();
            $result['commonContent'] = $this->index->commonContent();
            return view("login", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);
        }
    }

     public function getStateList(Request $request)
    {
        $states = DB::table("states")
        ->where("country_id",$request->country_id)
        ->pluck("name","id");
        return response()->json($states);
    }
    
    public function getAreaList(Request $request)
    {
        $states = DB::table("locations")
        ->where("city_id",$request->city)
        ->pluck("location as name","id");
        return response()->json($states);
    }

    
    
    public function getCityList(Request $request)
    {
        $cities = DB::table("cities")
        ->where("state_id",$request->country_id)
        ->pluck("name","id");
        return response()->json($cities);
    } 
    public function login(Request $request)
    {
        $result = array();
        if (auth()->guard('customer')->check()) {
            return redirect('/');
        } else {
            $result['cart'] = $this->cart->myCart($result);

            if (count($result['cart']) != 0) {
                $result['checkout_button'] = 1;
            } else {
                $result['checkout_button'] = 0;

            }
            $previous_url = Session::get('_previous.url');

            $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $ref = rtrim($ref, '/');

            session(['previous' => $previous_url]);

            $title = array('pageTitle' => Lang::get("website.Login"));
            $final_theme = $this->theme->theme();

            $result['commonContent'] = $this->index->commonContent();
            return view("auth.login", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);
        }

    }

    public function processLogin(Request $request)
    {
        $old_session = Session::getId();

        $result = array();

        //check authentication of email and password
        $customerInfo = array("email" => $request->email, "password" => $request->password);

        if (auth()->guard('customer')->attempt($customerInfo)) {
            $customer = auth()->guard('customer')->user();
            if ($customer->role_id != 2) {
                $record = DB::table('settings')->where('id', 94)->first();
                if ($record->value == 'Maintenance' && $customer->role_id == 1) {
                    auth()->attempt($customerInfo);
                } else {
                    Auth::guard('customer')->logout();
                    return redirect('login')->with('loginError', Lang::get("website.You Are Not Allowed With These Credentials!"));
                }
            }
            $result = $this->customer->processLogin($request, $old_session);
            if (!empty(session('previous'))) {
                return Redirect::to(session('previous'));
            } else {
                
                Session::forget('guest_checkout');
                return redirect()->intended('/')->with('result', $result);
            }
        } else {
            return redirect('login')->with('loginError', Lang::get("website.Email or password is incorrect"));
        }
        //}
    }

    public function verifyOTP(Request $request)
    {
        $old_session = Session::getId();

        $result = array();
        $system_otp = session('random_num');
        //check authentication of email and password
        $otp = $request->otp;


        if ($system_otp == $otp) {
            $customer = auth()->guard('customer')->user();
            $result = $this->customer->processOtpLogin($request, $old_session);
            

            if ($result == null) {
                return redirect('login')->with('loginError', "No account found. Please register!!");
            }
            
            if (!empty(session('previous'))) {
                return Redirect::to(session('previous'));
            } else {
                
                Session::forget('guest_checkout');
                return redirect()->intended('/')->with('result', $result);
            }
        } else {

            return redirect('login')->with('loginError', "Wrong OTP Entered!!");
        }
        //}
    }

    public function addToCompare(Request $request)
    {
        $cartResponse = $this->customer->addToCompare($request);
        return $cartResponse;
    }

    public function DeleteCompare($id)
    {
        $Response = $this->customer->DeleteCompare($id);
        return redirect()->back()->with($Response);
    }

    public function Compare()
    {
        $result = array();
        $final_theme = $this->theme->theme();
        $result['commonContent'] = $this->index->commonContent();
        $compare = $this->customer->Compare();
        $results = array();
        foreach ($compare as $com) {
            $data = array('products_id' => $com->product_ids, 'page_number' => '0', 'type' => 'compare', 'limit' => '15', 'min_price' => '', 'max_price' => '');
            $newest_products = $this->products->products($data);
            array_push($results, $newest_products);
        }
        $result['products'] = $results;
        return view('web.compare', ['result' => $result, 'final_theme' => $final_theme]);
    }

    public function profile()
    {
        $title = array('pageTitle' => Lang::get("website.Profile"));
        $result['commonContent'] = $this->index->commonContent();
        $final_theme = $this->theme->theme();
        return view('web.profile', ['result' => $result, 'title' => $title, 'final_theme' => $final_theme]);
    }

    public function updateMyProfile(Request $request)
    {
        $message = $this->customer->updateMyProfile($request);
        return redirect()->back()->with('success', $message);

    }

    public function changePassword()
    {
        $title = array('pageTitle' => Lang::get("website.Change Password"));
        $result['commonContent'] = $this->index->commonContent();
        $final_theme = $this->theme->theme();
        return view('web.changepassword', ['result' => $result, 'title' => $title, 'final_theme' => $final_theme]);
    }

    public function updateMyPassword(Request $request)
    {
        $password = Auth::guard('customer')->user()->password;
        if (Hash::check($request->current_password, $password)) {
            $message = $this->customer->updateMyPassword($request);
            return redirect()->back()->with('success', $message);
        }else{
            return redirect()->back()->with('error', lang::get("website.Current password is invalid"));
        }
    }

    public function logout(REQUEST $request)
    {
        Auth::guard('customer')->logout();
        session()->flush();
        $request->session()->forget('customers_id');
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    public function socialLogin($social)
    {
        return Socialite::driver($social)->redirect();
    }

    public function handleSocialLoginCallback($social)
    {
        $result = $this->customer->handleSocialLoginCallback($social);
        if (!empty($result)) {
            return redirect()->intended('/')->with('result', $result);
        }
    }

    public function createRandomPassword()
    {
        $pass = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        return $pass;
    }

    public function likeMyProduct(Request $request)
    {
        $cartResponse = $this->customer->likeMyProduct($request);
        return $cartResponse;
    }

    public function notifyProduct(Request $request)
    {
        $cartResponse = $this->customer->notifyProduct($request);
        return $cartResponse;
    }

    public function unlikeMyProduct(Request $request, $id)
    {

        if (!empty(auth()->guard('customer')->user()->id)) {
            $this->customer->unlikeMyProduct($id);
            $message = Lang::get("website.Product is unliked");
            return redirect()->back()->with('success', $message);
        } else {
            return redirect('login')->with('loginError', 'Please login to like product!');
        }

    }

    public function wishlist(Request $request)
    {
        $title = array('pageTitle' => Lang::get("website.Wishlist"));
        $final_theme = $this->theme->theme();
        $result = $this->customer->wishlist($request);
        return view("web.wishlist", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);
    }

    public function loadMoreWishlist(Request $request)
    {

        $limit = $request->limit;

        $data = array('page_number' => $request->page_number, 'type' => 'wishlist', 'limit' => $limit, 'categories_id' => '', 'search' => '', 'min_price' => '', 'max_price' => '');
        $products = $this->products->products($data);
        $result['products'] = $products;

        $cart = '';
        $myVar = new CartController();
        $result['cartArray'] = $this->products->cartIdArray($cart);
        $result['limit'] = $limit;
        return view("web.wishlistproducts")->with('result', $result);

    }

    public function forgotPassword()
    {
        if (auth()->guard('customer')->check()) {
            return redirect('/');
        } else {

            $title = array('pageTitle' => Lang::get("website.Forgot Password"));
            $final_theme = $this->theme->theme();
            $result = array();
            $result['commonContent'] = $this->index->commonContent();
            return view("web.forgotpassword", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);
        }
    }

    public function processPassword(Request $request)
    {
        $title = array('pageTitle' => Lang::get("website.Forgot Password"));

        $password = $this->createRandomPassword();

        $email = $request->email;
        $postData = array();

        //check email exist
        $existUser = $this->customer->ExistUser($email);
        if (count($existUser) > 0) {
            $this->customer->UpdateExistUser($email, $password);
            $existUser[0]->password = $password;

            $myVar = new AlertController();
            $alertSetting = $myVar->forgotPasswordAlert($existUser);

            return redirect('login')->with('success', Lang::get("website.Password has been sent to your email address"));
        } else {
            return redirect('forgotPassword')->with('error', Lang::get("website.Email address does not exist"));
        }

    }

    public function recoverPassword()
    {
        $title = array('pageTitle' => Lang::get("website.Forgot Password"));
        $final_theme = $this->theme->theme();
        return view("web.recoverPassword", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);
    }

    public function loginotp(Request $request)
    {
        $title = array('pageTitle' => "Submit Otp");
        $final_theme = $this->theme->theme();

        $result = array();
        $result['commonContent'] = $this->index->commonContent();
        $result['mobile_number'] = $request->mobile_number;

        $random_num = rand ( 1000 , 9999 );
        $msg = "Your One time Otp for Sacamart login is ".$random_num;

        session(['random_num' => $random_num]);

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.smsgateway.center/SMSApi/rest/send",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "userId=sacmrt&password=Sacamart@1!&mobile=91".$request->mobile_number."&msg=".$msg."&senderId=sacmrt&msgType=text&duplicateCheck=true&format=json&sendMethod=simpleMsg",
          CURLOPT_HTTPHEADER => array(
            "apikey: somerandomuniquekey",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return view("auth.forgotpassword", ['title' => $title, 'final_theme' => $final_theme])->with('result', $result);
    }

    public function subscribeNotification(Request $request)
    {

        $setting = $this->index->commonContent();

        /* Desktop */
        $type = 3;

        session(['device_id' => $request->device_id]);

        if (auth()->guard('customer')->check()) {

            $device_data = array(
                'device_id' => $request->device_id,
                'device_type' => $type,
                'created_at' => time(),
                'updated_at' => time(),
                'ram' => '',
                'status' => '1',
                'processor' => '',
                'device_os' => '',
                'location' => '',
                'device_model' => '',
                'user_id' => auth()->guard('customers')->user()->id,
                'manufacturer' => '',
            );

        } else {

            $device_data = array(
                'device_id' => $request->device_id,
                'device_type' => $type,
                'created_at' => time(),
                'updated_at' => time(),
                'ram' => '',
                'status' => '1',
                'processor' => '',
                'device_os' => '',
                'location' => '',
                'device_model' => '',
                'manufacturer' => '',
            );

        }
        $this->customer->updateDevice($request, $device_data);
        print 'success';
    }

    public function signupProcess(Request $request)
    {

        
        $old_session = Session::getId();

        $firstName = $request->firstName;
        $lastName = $request->lastName;
        $gender = $request->gender;
        $email = $request->email;
        $mobile_number = $request->mobile_number;
        $password = $request->password;
        $date = date('y-m-d h:i:s');
        //        //validation start
        $validator = Validator::make(
            array(
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'customers_gender' => $request->gender,
                'email' => $request->email,
                'password' => $request->password,
                're_password' => $request->re_password,

            ), array(
                'firstName' => 'required ',
                'lastName' => 'required',
                'customers_gender' => 'required',
                'email' => 'required | email',
                'password' => 'required',
                're_password' => 'required | same:password',
            )
        );
        if ($validator->fails()) {
            return redirect('login')->withErrors($validator)->withInput();
        } else {
           
            $res = $this->customer->signupProcess($request);
            //dd($res);die;
            //eheck email already exit
            if ($res['email'] == "true") {
                return redirect('/login')->withInput($request->input())->with('error', Lang::get("website.Email already exist"));
            } else {
                if ($res['insert'] == "true") {
                    if ($res['auth'] == "true") {
                        $result = $res['result'];
                        Session::forget('guest_checkout');
                        return redirect()->intended('/')->with('result', $result);
                    } else {
                        return redirect('login')->with('loginError', Lang::get("website.Email or password is incorrect"));
                    }
                } else {
                    return redirect('/login')->with('error', Lang::get("website.something is wrong"));
                }
            }

        }
    }

    public function vendorRegister(Request $request)
    {
        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();
        $category = DB::table('categories')->where('parent_id',0)->get();
        $categoryids = array(0);
        foreach($category as $singlecat){
           $categoryids[] = $singlecat->categories_id; 
        }
        $categorydata = DB::table('categories_description')->where('language_id',1)->whereIn('categories_id',$categoryids)->get();
        //print_r($categorydata);die;
        
        $result = array();
        
        $categories = $this->category->recursivecategories();
        //print_r($categories);die;
        $parent_id = array();
        $option = '<ul class="list-group list-group-root well">';

        foreach ($categories as $parents) {

            if (in_array($parents->categories_id, $parent_id)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }

            $option .= '<li href="#" class="list-group-item">
          <label style="width:100%">
            <input id="categories_' . $parents->categories_id . '" ' . $checked . ' type="checkbox" class=" required_one categories sub_categories" name="category[]" value="' . $parents->categories_id . '">
          ' . $parents->categories_name . '
          </label></li>';

            if (isset($parents->childs)) {
                $option .= '<ul class="list-group">
          <li class="list-group-item">';
                $option .= $this->childcat($parents->childs, $parent_id);
                $option .= '</li></ul>';
            }
        }
        $option .= '</ul>';
        $result = array();
        $result['categories'] = $option;
        
        $final_theme = $this->theme->theme();
        if (auth()->guard('customer')->check()) {
            return redirect('/');
        } else {
            $title = array('pageTitle' => 'Vendor Registration');
            
            $result['commonContent'] = $this->index->commonContent();
            return view("web.vendor_register", ['title' => $title, 'final_theme' => $final_theme])->with(['result'=>$result,'countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'category' => $categorydata]);
        }
    }
    
    public function childcat($childs, $parent_id)
    {

        $contents = '';
        foreach ($childs as $key => $child) {

            if (in_array($child->categories_id, $parent_id)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }

            $contents .= '<label> <input id="categories_' . $child->categories_id . '" parents_id="' . $child->parent_id . '"  type="checkbox" name="subcategory[]" class="required_one sub_categories categories sub_categories_' . $child->parent_id . '" value="' . $child->categories_id . '" ' . $checked . '> ' . $child->categories_name . '</label>';

            if (isset($child->childs)) {
                $contents .= '<ul class="list-group">
        <li class="list-group-item">';
                $contents .= $this->childcat($child->childs, $parent_id);
                $contents .= "</li></ul>";
            }

        }
        return $contents;
    }
    
    public function storevendor(Request $request)
    {
        
//       echo "<pre>";
//        print_r($request->all());die;
        $validator = Validator::make($request->all(), [
                    'fullname' => 'required ',
//                    'email' => 'required | email|unique:users,email',
//                    'phonenumber' => 'required|numeric|digits:10',
                    'password' => 'required',
                    'country_id' => 'required',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'nameofbusiness' => 'required',
                    'businessaddress' => 'required',
                    'category' => 'required',
                    'doucmument' => 'required',
                    'pincode' => 'required'
                ]);
        if ($validator->fails()) {
             return redirect('vendor-registration')->withErrors($validator)->withInput();
            //return Redirect::back()->withErrors($validator)->withInput();
        } else {
            //dd($request->all());die;
            

            if($request->hasFile('doucmument'))
            {
                foreach($request->file('doucmument') as $image)
                {
                    $name=$image->getClientOriginalName();
                    $image->move(public_path().'/doucmument/', time().'_'.$name);  
                    $images_data[] = $name;  
                }
            }
             


            $checkemail = DB::table('vendors')->where('email', $request->email)->first();
            
            if(!empty($checkemail)){
                return redirect('/vendor-registration')->withInput($request->input())->with('error', Lang::get("website.Email already exist"));
            }
            $data = array(
                'name' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phonenumber,
                'password' =>  Hash::make($request->password),
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'business_name' => $request->nameofbusiness,
                'business_address' => $request->businessaddress,
                //'product_category_id' => $request->category;,
                'doucmument' => json_encode($images_data),  
                'pincode' => $request->pincode
            );
            
           
            $res = DB::table('vendors')->insertGetId($data);
            if(!empty($res)){
                foreach ($request->category as $key => $value) {
                    $datacategory = array(
                       'category_id' => $value,
                       'user_id' => $res,
                    );
                   DB::table('vendor_category')->insert($datacategory);
                }
            if(!empty($request->selectedareas)){
                $mycitys = explode(',',$request->selectedareas);
                foreach ($mycitys as $values) {
                    if(!empty($values)){
                        $datalocation = array(
                           'country_id' => $request->country_id,
                           'state_id' => $request->state_id, 
                           'city_id' => $request->city_id,
                           'location_name' => $values,
                           'vendor_id' => $res,
                        );
                        DB::table('vendors_location')->insert($datalocation);
                    }
                    
                }
            }

            $client = new Client(
                ['headers' => [
                    'Content-Type' => 'application/json'
                    ]
                ]
            );

            $body['email'] = "sacaimpex@gmail.com";
            $body['password'] = "Shiprocket!@#$5";

            $response = $client->post("https://apiv2.shiprocket.in/v1/external/auth/login", [ 'body' => json_encode($body) ]);

            $body = json_decode($response->getBody(), true);

            

            $vendor_client = new Client(
                ['headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$body['token']
                    ]
                ]
            );

            $vendor_city = DB::table('cities')->where('id',$request->city_id)->get();
            $vendor_state = DB::table('states')->where('id',$request->state_id)->get();

            $body1['pickup_location'] = "V".$res;
            $body1['name'] = $request->fullname;
            $body1['email'] = $request->email;
            $body1['phone'] = $request->phonenumber;
            $body1['address'] = $request->businessaddress;
            $body1['address_2'] = $request->businessaddress;
            $body1['city'] = json_decode($vendor_city, true)[0]['name'];
            $body1['state'] = json_decode($vendor_state, true)[0]['name'];
            $body1['country'] = "India";
            $body1['pin_code'] = $request->pincode;

            $response1 = $vendor_client->post("https://apiv2.shiprocket.in/v1/external/settings/company/addpickup", [ 'body' => json_encode($body1) ]);

            $body1 = json_decode($response1->getBody(), true);

            $pickup_code = $body1['address']['pickup_code'];
            $company_id = $body1['address']['company_id'];

            DB::table('vendors')
              ->where('id', $res)
              ->update([
                'pickup_code' => $pickup_code,
                'company_id' => $company_id
            ]);
                
            }
            return redirect()->back()->with('success',"Registration Successfully and your account send to approvel.");
           
        }
    }
    
    
//     public function vendorRegister(Request $request)
//    {
//        
////        $countrylist = DB::table('country')->get();
////        print_r($countrylist);die;
////        $statelist = DB::table('states')->get();
////        $citylist = DB::table('cities')->get();
////        
////        $category = DB::table('categories')->where('parent_id',0)->get();
//        
//        $final_theme = $this->theme->theme();
//        if (auth()->guard('customer')->check()) {
//            return redirect('/');
//        } else {
//            $title = array('pageTitle' => 'Vendor Registration');
//            $result = array();
//            $result['commonContent'] = $this->index->commonContent();
//            //'countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'category' => $category
//            return view("web.vendor_register", ['title' => $title, 'final_theme' => $final_theme])->with(['result'=>$result]);
//        }
//    }

}
