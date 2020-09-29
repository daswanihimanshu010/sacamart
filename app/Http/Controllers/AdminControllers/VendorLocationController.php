<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\AdminControllers\SiteSettingController;
use App\Http\Controllers\Controller;
use App\Models\Core\Coupon;
use App\Models\Core\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use App\Models\Core\Languages;
use Auth;

class VendorLocationController extends Controller
{
    //
    public function __construct(Coupon $coupon, Setting $setting, Languages $language)
    {
        $this->Coupon = $coupon;
        $this->myVarSetting = new SiteSettingController($setting);
        $this->Setting = $setting;
        $this->language = $language;
    }

    public function display(Request $request)
    {

        $id = Auth::user()->id;
        $vendorId = DB::table('vendors')->where('user_id', $id)->first();
        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();

        //'locations.location_name', 

        $vendors = DB::table('vendors_location')
        ->leftjoin('locations', 'vendors_location.location_name', '=', 'locations.id')
        ->leftjoin('country', 'vendors_location.country_id', '=', 'country.id')
        ->leftjoin('states', 'vendors_location.state_id', '=', 'states.id')
        ->leftjoin('cities', 'vendors_location.city_id', '=', 'cities.id')
        ->select('vendors_location.id', 'locations.location as location_name', 'vendors_location.shipping_fees', 'country.name as country_name', 'states.name as state_name', 'cities.name as city_name', 'vendors_location.status', 'vendors_location.created_at')
        ->where('vendors_location.vendor_id', $vendorId->id)
        ->where('vendors_location.deleted_at', 0)
        ->paginate('10');

        $title = array('pageTitle' => 'Vendor Address');
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors_location.index", $title)->with('result', $result)->with(['vendors' => $vendors, 'countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist]);

    }



    public function filter(Request $request)
    {

        $result = array();
        $message = array();
        $title = array('pageTitle' => Lang::get("labels.EditSubCategories"));
        $name = $request->FilterBy;
        $param = $request->parameter;
        switch ($name) {
            case 'Code':$coupons = Coupon::sortable()->where('code', 'LIKE', '%' . $param . '%')
                    ->orderBy('created_at', 'DESC')
                    ->paginate(7);

                break;
            case 'CouponType':$coupons = Coupon::sortable()->where('discount_type', 'LIKE', '%' . $param . '%')
                    ->orderBy('created_at', 'DESC')
                    ->paginate(7);

                break;
            case 'CouponAmount':
                $coupons = Coupon::sortable()->where('amount', 'LIKE', '%' . $param . '%')
                    ->orderBy('created_at', 'DESC')
                    ->paginate(7);

                break;
            case 'Description':
                $coupons = Coupon::sortable()->where('description', 'LIKE', '%' . $param . '%')
                    ->orderBy('created_at', 'DESC')
                    ->paginate(7);

                break;
            default:

                break;
        }

        $result['coupons'] = $coupons;
        //get function from other controller
        $result['currency'] = $this->myVarSetting->getSetting();
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.coupons.index", $title)->with('result', $result)->with('coupons', $coupons)->with('name', $name)->with('param', $param);
    }

    public function add(Request $request)
    {

        $title = array('pageTitle' => Lang::get("labels.AddCoupon"));
        $result = array();
        $message = array();
        $result['message'] = $message;
        $emails = $this->Coupon->email();
        $result['emails'] = $emails;
        $products = $this->Coupon->cutomers();
        $result['products'] = $products;
        $categories = $this->Coupon->categories();
        $result['categories'] = $categories;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.coupons.add", $title)->with('result', $result);
    }

    public function insert(Request $request)
    {
       
         $userId = Auth::user()->id;
         $vendorId = DB::table('vendors')->where('user_id', $userId)->first();
         $validator = Validator::make(
            array(
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'location_name' => $request->location_name,
              ),
            array(
              'country_id'  => 'required',
              'state_id'  => 'required',
              'city_id'  => 'required',
              'location_name'  => 'required',
              )
          );


           $data = array(
             'country_id' => $request->country_id,
             'state_id' => $request->state_id,
             'city_id' => $request->city_id,
             'location_name' => $request->location_name,
             'vendor_id'      => $vendorId->id,
             'shipping_fees' => ($request->shipping_fees) ? $request->shipping_fees:0,
             'min_order'    => ($request->min_order) ? $request->min_order:0,
           ); 

          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
        DB::table('vendors_location')->insert($data);
        return redirect()->back()->withErrors('Vendor Location Added successfully');

    }

    public function edit($id)
    {
        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();
        $locationlist = DB::table('locations')->get();

        $vendors = DB::table('vendors_location')
        ->where('vendors_location.id', $id)
        ->where('vendors_location.deleted_at', 0)
        ->first();

       /* echo "<pre>";
        print_r($vendors);*/

        $title = array('pageTitle' => 'Edit Vendor Location');
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors_location.edit", $title)->with('result', $result)->with(['vendors' => $vendors, 'countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'locationlist' => $locationlist]);
    }

    public function update(Request $request)
    {
        if ($request->id) {

            $validator = Validator::make(
            array(
               'country_id' => $request->country_id,
               'state_id' => $request->state_id,
               'city_id' => $request->city_id,
               'location_name' => $request->location_name,
              ),
            array(
              'country_id'  => 'required',
              'state_id'  => 'required',
              'city_id'  => 'required',
              'location_name'  => 'required',
              )
          );

          $data = array(
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'location_name' => $request->location_name,
            'shipping_fees' => ($request->shipping_fees) ? $request->shipping_fees:0,
            'min_order'    => ($request->min_order) ? $request->min_order:0,
          );  
          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
             DB::table('vendors_location')->where('id',$request->id)->update($data);
             //redirect()->to('admin/languages/vendors_address/'.$request->id->vendor_id);
             //return \Redirect::route('/vendors_address/', [$request->vendor_id])->withErrors('message', 'State saved correctly!!!');
            return redirect('admin/vendorlocation/display')->withErrors('Vendor Location Update successfully');
        } 

    }

    public function delete(Request $request)
    {
        if ($request->id) {
             DB::table('vendors_location')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('Vendor deleted successfully');
        }
    }

    public function getStateList(Request $request)
    {
        $states = DB::table("states")
        ->where("country_id",$request->country_id)
        ->pluck("name","id");
        return response()->json($states);
    }

    public function getCityList(Request $request)
    {
        $cities = DB::table("cities")
        ->where("state_id",$request->country_id)
        ->pluck("name","id");
        return response()->json($cities);
    } 

    public function getAddressList(Request $request)
    {
        $address = DB::table("locations")
        ->where("city_id",$request->city_id)
        ->pluck("location","id");
        return response()->json($address);
    }

}
