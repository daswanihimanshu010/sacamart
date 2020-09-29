<?php

namespace App\Http\Controllers\AdminControllers;

use App;
use App\Http\Controllers\Controller;
use App\Models\Core\Images;
use App\Models\Core\Languages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\Core\Setting;
use Validator;
use Illuminate\Support\Facades\Input;
use App\User;
use Carbon\Carbon;
use App\Models\Core\Categories;
use App\Models\Core\Customers;
use Hash;

class LanguageController extends Controller
{

    public function __construct(Languages $language, Images $images, Categories $category, Setting $setting)
    {

        $this->language = $language;
        $this->images = $images;
        $this->Setting = $setting;
         $this->category = $category;

    }

    //languages
    public function display(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.languages.index", $title)->with('result', $result);
    }

    //addLanguages
    public function add(Request $request)
    {
        $allimage = $this->images->getimages();
        $title = array('pageTitle' => Lang::get("labels.AddLanguage"));
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.languages.add", $title)->with('allimage', $allimage)->with('result', $result);
    }

    //addNewLanguages
    public function insert(Request $request)
    {
        $languages = $this->language->getter();
        $languages = $this->language->insert($request);
        $message = Lang::get("labels.languageAddedMessage");
        return redirect()->back()->withErrors([$message]);
    }

    //editOrderStatus
    public function edit(Request $request)
    {
        $allimage = $this->images->getimages();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->edit($request);
        $result['languages'] = $languages;        
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.languages.edit", $title)->with('result', $result)->with('allimage', $allimage);
    }

    //updateLanguageStatus
    public function update(Request $request)
    {

        $languages = $this->language->getter();
        $this->language->updateRecord($request);
        $message = Lang::get("labels.languageEditMessage");
        return redirect()->back()->withErrors([$message]);
    }

    //deletelanguage
    public function delete(Request $request)
    {

        if ($request->id == 1) {
            return redirect()->back()->withErrors([Lang::get("labels.DefaultDeleteMessage")]);
        } else {
            $languages = $this->language->getter();
            $deleteLang = $this->language->deleteRecord($request);
            return redirect()->back()->withErrors([Lang::get("labels.languageDeleteMessage")]);
        }
    }

    //getsinglelanguages
    public function getSingleLanguages($language_id)
    {

        $languagesClass = new Languages();

        $languages = $languagesClass->getSingleLan();
        return $languages;
    }

    public function fetchlanguages()
    {
        $languagesClass = new Languages();
        $languages = $languagesClass->getSingleLan();
        return $languages;
    }

    public function filter(Request $request)
    {

        $filter = $request->FilterBy;
        $parameter = $request->parameter;

        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));

        $result = array();

        $Languages = null;
        switch ($filter) {
            case 'Language':

                $Languages = Languages::sortable(['languages_id' => 'desc'])->leftJoin('images', 'images.id', '=', 'languages.image')
                    ->leftJoin('image_countrylist', 'image_categories.image_id', '=', 'languages.image')
                    ->select('languages.languages_id', 'languages.name', 'languages.code', 'languages.directory', 'languages.is_default', 'languages.direction', 'languages.sort_order', 'image_categories.path')
                    ->where('languages.name', 'LIKE', '%' . $parameter . '%')->where(function ($query) {
                    $query->where('image_categories.image_type', '=', 'THUMBNAIL')

                        ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                        ->orWhere('image_categories.image_type', '=', 'ACTUAL');

                })
                    ->paginate(5);
                break;

            case 'Code':

                $Languages = Languages::sortable(['languages_id' => 'desc'])->leftJoin('images', 'images.id', '=', 'languages.image')
                    ->leftJoin('image_categories', 'image_categories.image_id', '=', 'languages.image')
                    ->select('languages.languages_id', 'languages.name', 'languages.code', 'languages.directory', 'languages.is_default', 'languages.direction', 'languages.sort_order', 'image_categories.path')
                    ->where('languages.code', 'LIKE', '%' . $parameter . '%')->where(function ($query) {
                    $query->where('image_categories.image_type', '=', 'THUMBNAIL')

                        ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                        ->orWhere('image_categories.image_type', '=', 'ACTUAL');

                })
                    ->paginate(5);
                break;
            default:
                $Languages = Languages::sortable(['languages_id' => 'desc'])->leftJoin('images', 'images.id', '=', 'languages.image')
                    ->leftJoin('image_categories', 'image_categories.image_id', '=', 'languages.image')
                    ->select('languages.languages_id', 'languages.name', 'languages.code', 'languages.directory', 'languages.is_default', 'languages.direction', 'languages.sort_order', 'image_categories.path')
                    ->where(function ($query) {
                        $query->where('image_categories.image_type', '=', 'THUMBNAIL')

                            ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                            ->orWhere('image_categories.image_type', '=', 'ACTUAL');

                    })->paginate(5);

                break;

        }

        $result['languages'] = $Languages;        
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.languages.index", $title)->with('result', $result)->with('filter', $filter)->with('parameter', $parameter);

    }

    function default(Request $request) {

        DB::table('languages')->where('is_default', '=', 1)->update([
            'is_default' => 0,
        ]);
        DB::table('languages')->where('languages_id', '=', $request->languages_id)->update([
            'is_default' => 1,
            'status' => 1,
        ]);
    }



   public function manage_country(Request $request){

        $country = DB::table('country')->where('deleted_at', 0)->paginate('10');
        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.country.index", $title)->with('result', $result)->with('country',$country);
    }

    public function add_country()
    {
        $allimage = $this->images->getimages();
        $title = array('pageTitle' => Lang::get("Add Country"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.country.add", $title)->with('result', $result);
    }



    public function insert_country(Request $request)
    {
        $validator = Validator::make(
            array(
              'name' => $request->name,
              'sortname' => $request->sortname,
              'phonecode' => $request->phonecode,
              ),
            array(
              'name' => 'required',
              'sortname' => 'required',
              'phonecode' => '',
              )
          );
          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

        $data = [
            'name' => request()->get('name'),
            'sortname' => request()->get('sortname'),
            'phonecode' => request()->get('phonecode')
        ];
        DB::table('country')->insert($data);
        $title = array('pageTitle' => Lang::get("Add Country"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return redirect('admin/languages/manage_country');
    }

    public function edit_country($id)
    {
        $allimage = $this->images->getimages();
        $country = DB::table('country')->where('id',$id)->get();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.country.edit", $title)->with('result', $result)->with('country',$country);
    }


    public function update_country($id)
    {
        $data = [
            'name' => request()->get('name'),
            'sortname' => request()->get('sortname'),
            'phonecode' => request()->get('phonecode')
        ];
        DB::table('country')->where('id',$id)->update($data);
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return redirect('admin/languages/manage_country');
    }

    public function delete_country(Request $request)
    {
        if ($request->id) {
             DB::table('country')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('country deleted successfully');
        } 
    }

    public function manage_state(Request $request){
   
        $states = DB::table('states')
        ->leftjoin('country', 'states.country_id', '=', 'country.id')
        ->select('country.name as country_name', 'states.name as state_name', 'states.id')
        ->where('states.deleted_at', 0)
        ->paginate('10');

        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.states.index", $title)->with('result', $result)->with('states',$states);
    }

    public function add_state()
    {
        $allimage = $this->images->getimages();
        $title = array('pageTitle' => Lang::get("Add State"));
        $country = DB::table('country')->get();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.states.add", $title)->with('result', $result)->with('country', $country);
    }

    public function insert_state(Request $request)
    {
        $validator = Validator::make(
            array(
              'name' => $request->state_name,
              'country_id' => $request->country_id,
              ),
            array(
              'name' => 'required',
              'country_id' => 'required',
              )
          );
          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

        $data = [
            'name' => request()->get('state_name'),
            'country_id' => request()->get('country_id'),
        ];
        DB::table('states')->insert($data);
        $title = array('pageTitle' => Lang::get("Add Country"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return redirect('admin/languages/manage_state');
    }


    public function edit_state($id)
    {
        $allimage = $this->images->getimages();
        $country = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $states = DB::table('states')->where('id',$id)->first();
        /*print_r($states);
        die;*/
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.states.edit", $title)->with('result', $result)->with(['country' => $country, 'states' => $states, 'statelist' => $statelist]);
    }


    public function update_state()
    {
        $id = request()->get('myid');
        if(!empty($id)){
            $data = [
                'country_id' => request()->get('country_id'),
                'name' => request()->get('state_name'),
            ];
            DB::table('states')->where('id',$id)->update($data);
            $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
            $languages = $this->language->paginator();
            $result['languages'] = $languages;
            $result['commonContent'] = $this->Setting->commonContent();
            return redirect('admin/languages/manage_state');
        }
    }

    public function delete_state(Request $request)
    {
        if ($request->id) {
             DB::table('states')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('State deleted successfully');
        } 
    }

    public function manage_city(Request $request){

        $cities = DB::table('cities')
        ->leftjoin('states', 'cities.state_id', '=', 'states.id')
        ->select('states.name as state_name', 'cities.name as city_name', 'cities.id')
        ->where('cities.deleted_at', 0)
        ->paginate('10');
         
        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.cities.index", $title)->with('result', $result)->with('cities',$cities);
    }

    public function add_city()
    {
        $allimage = $this->images->getimages();
        $title = array('pageTitle' => Lang::get("Add State"));
        $country = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.cities.add", $title)->with('result', $result)->with(['country' =>  $country, 'statelist' =>  $statelist]);
    }

    public function insert_city(Request $request)
    {
        $validator = Validator::make(
            array(
              'name' => $request->city_name,
              'state_id' => $request->state_id,
              'country_id' => $request->country_id,
              ),
            array(
              'name' => 'required',
              'state_id' => 'required',
              'country_id' => 'required',
              )
          );
          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

        $data = [
            'name' => request()->get('city_name'),
            'state_id' => request()->get('state_id'),
        ];
        DB::table('cities')->insert($data);
        $title = array('pageTitle' => Lang::get("Add Country"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return redirect('admin/languages/manage_city');
    }
    

    public function edit_city($id)
    {
        $allimage = $this->images->getimages();
        $statelist = DB::table('states')->get();
        $cities = DB::table('cities')->where('id',$id)->first();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.cities.edit", $title)->with('result', $result)->with(['cities' => $cities, 'statelist' => $statelist]);
    }

    

    

    public function update_cities()
    {
        $id = request()->get('myid');
        if(!empty($id)){
            $data = [
                'state_id' => request()->get('state_id'),
                'name' => request()->get('city_name'),
            ];
            DB::table('cities')->where('id',$id)->update($data);
            $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
            $languages = $this->language->paginator();
            $result['languages'] = $languages;
            $result['commonContent'] = $this->Setting->commonContent();
            return redirect('admin/languages/manage_city');
        }
    }

    public function delete_city(Request $request)
    {
        if ($request->id) {
             DB::table('cities')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('City deleted successfully');
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

    public function manage_package(Request $request){

        $package = DB::table('packages')->where('deleted_at', 0)->orderBy('id', 'desc')->paginate('10');
         
        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.package.index", $title)->with('result', $result)->with('package',$package);
    }

    public function add_package(Request $request)
    {


        $allimage = $this->images->getimages();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.package.add", $title)->with('result', $result);
    }

    public function package_insert(Request $request)
    {
        $validator = Validator::make(
            array(
              'name' => $request->name,
              'price' => $request->price,
              'time' => $request->package_time,
              ),
            array(
              'name' => 'required',
              'price' => 'required',
              'time' => 'required',
              )
          );
          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
         if(!empty($request->image)){
             $imageName = time().'.'.$request->image->extension();  
             $request->image->move(public_path('images'), $imageName);
                 $data = [
                'package_name'  => request()->get('name'),
                'price'         => request()->get('price'),
                'status'        => request()->get('status'),
                'package_desc'  => request()->get('description'),
                'package_time'  => request()->get('package_time'),
                'status'        => request()->get('status'),
                'no_of_product'  => request()->get('no_of_product'),
                'image'        => $imageName
              ];

           }else{
                $data = [
                'package_name'  => request()->get('name'),
                'price'         => request()->get('price'),
                'status'        => request()->get('status'),
                'package_desc'  => request()->get('description'),
                'package_time'  => request()->get('package_time'),
                'no_of_product'  => request()->get('no_of_product'),
                'status'        => request()->get('status'),
                ];
           } 
         DB::table('packages')->insert($data); 
        $allimage = $this->images->getimages();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
         return redirect('admin/languages/manage_package');
    }

    public function edit_package($id)
    {
        $package = DB::table('packages')->where('id',$id)->first();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.package.edit", $title)->with('result', $result)->with('package',$package);
    }

    public function update_package(Request $request)
    {
        $id = request()->get('id');
        $validator = Validator::make(
            array(
              'name' => $request->name,
              'price' => $request->price,
              ),
            array(
              'name' => 'required',
              'price' => 'required',
              )
          );
        
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }


           if(!empty($request->image)){
             $imageName = time().'.'.$request->image->extension();  
             $request->image->move(public_path('images'), $imageName);
                 $data = [
                'package_name'  => request()->get('name'),
                'price'         => request()->get('price'),
                'status'        => request()->get('status'),
                'package_desc'  => request()->get('description'),
                'package_time'  => request()->get('package_time'),
                'no_of_product'  => request()->get('no_of_product'),
                'status'        => request()->get('status'),
                'image'        => $imageName
              ];

           }else{
                $data = [
                'package_name'  => request()->get('name'),
                'price'         => request()->get('price'),
                'status'        => request()->get('status'),
                'package_desc'  => request()->get('description'),
                'no_of_product'  => request()->get('no_of_product'),
                'package_time'  => request()->get('package_time'),
                'status'        => request()->get('status'),
                ];
           }
          
        
        DB::table('packages')->where('id',$id)->update($data);
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return redirect('admin/languages/manage_package');
    }

    public function delete_package(Request $request)
    {
        if ($request->id) {
             DB::table('packages')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('package deleted successfully');
        } 
    }

    public function shipping_zones_slabs(Request $request){


        $shippingzones = DB::table('shipping_weight_slabs')->get();
        
        $title = array('pageTitle' => "Shipping Weight Slabs");
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.locations.weightslabindex", $title)->with('result', $result)->with('shippingzones', $shippingzones);
    }

    public function weight_slab_add(Request $request)
    {

        $shipping_zone_list = DB::table('shipping_zones_charges')->get();

        $title = array('pageTitle' => "Add Weight Slab");
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.locations.weightslabadd", $title)->with('result', $result)->with('shipping_zone_list', $shipping_zone_list);
    }

    public function insert_weightslab(Request $request)
    {

        $validator = Validator::make(
            array(
              'shipping_zone_id' => $request->shipping_zone_id,
              'slab_name' => $request->slab_name,
              'range_start' => $request->range_start,
              'range_end' => $request->range_end,
              'shipping_fee' => $request->shipping_fee,
              ),
            array(
              'shipping_zone_id'  => 'required',
              'slab_name'    => 'required',
              'range_start'    => 'required',
              'range_end'    => 'required',
              'shipping_fee'    => 'required',
              )
          );

          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

         $data = [
                'shipping_zone_id'    => request()->get('shipping_zone_id'),
                'slab_name'      => request()->get('slab_name'),
                'range_start'       => request()->get('range_start'),
                'range_end'        => request()->get('range_end'),
                'shipping_fee'        => request()->get('shipping_fee'),
                ]; 

         DB::table('shipping_weight_slabs')->insert($data);

        $title = array('pageTitle' => "List Weight Slabs");
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
         return redirect('admin/languages/shipping_zones_slabs');
    }

    public function manage_location(Request $request){


        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();


        //echo $request->location_name;

        //die;

        $locations = DB::table('locations')->orderBy('id', 'desc')
        ->leftjoin('country', 'locations.country_id', '=', 'country.id')
        ->leftjoin('states', 'locations.state_id', '=', 'states.id')
         ->leftjoin('cities', 'locations.city_id', '=', 'cities.id')
         ->leftjoin('shipping_weight_slabs', 'shipping_weight_slabs.shipping_zone_id', '=', 'locations.shipping_zone_id')
        ->select('country.name as country_name', 'states.name as state_name', 'cities.name as city_name', 'locations.location', 'locations.status', 'locations.created_at', 'locations.id', 'shipping_weight_slabs.slab_name', 'shipping_weight_slabs.range_start', 'shipping_weight_slabs.range_end', 'shipping_weight_slabs.shipping_fee')
        ->where('locations.deleted_at', 0);


        if(isset($request->country_id) && $request->country_id){
            $locations->where('locations.country_id', $request->country_id);
         }

         if(isset($request->state_id) &&  $request->state_id){
            $locations->where('locations.state_id', $request->state_id);
         }

          if(isset($request->city_id) && $request->city_id){
            
            $locations->where('locations.city_id', $request->city_id);
          }

          if(isset($request->location_name) && $request->location_name){
            $locations->where('location', 'like', '%' . $request->location_name . '%');
          }
        $locations = $locations->paginate('10');

         //echo "<pre>";
         //print_r($locations);die;
       
        //->paginate('10');
        
        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.locations.index", $title)->with('result', $result)->with(['locations' => $locations, 'countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist]);
    }

    public function shipping_zones(Request $request){
        $title = array('pageTitle' => "Shipping Zones");
        $tax_class = DB::table('shipping_zones_charges')->get();
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.locations.shipzones", $title)->with('result', $result)->with('tax_class', $tax_class);
    }

    public function shipping_zone_add(Request $request) {
        $title = array('pageTitle' => "Shipping zone add");
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.locations.shipzoneadd", $title)->with('result', $result);
    }

    public function insert_shippingzone(Request $request)
    {

        $validator = Validator::make(
            array(
              'shipping_zone_title' => $request->shipping_zone_title,
              ),
            array(
              'shipping_zone_title'    => 'required',
              )
          );

          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

         $data = [
                'shipping_zone_title'    => request()->get('shipping_zone_title'),
                ]; 

         DB::table('shipping_zones_charges')->insert($data);

        $title = array('pageTitle' => "Shipping Zones");
        $tax_class = DB::table('shipping_zones_charges')->get();
        $result['commonContent'] = $this->Setting->commonContent();
         return redirect('admin/languages/shipping_zones');
    }

    public function add_location(Request $request)
    {

        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();
        $shipping_zone_list = DB::table('shipping_zones_charges')->get();
        $allimage = $this->images->getimages();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.locations.add", $title)->with('result', $result)->with(['countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'shipping_zone_list' => $shipping_zone_list]);
    }
    public function insert_location(Request $request)
    {

        $validator = Validator::make(
            array(
              'country_id' => $request->country_id,
              'state_id' => $request->state_id,
              'location' => $request->location,
              ),
            array(
              'country_id'  => 'required',
              'state_id'    => 'required',
              'location'    => 'required',
              )
          );

          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

         $data = [
                'country_id'    => request()->get('country_id'),
                'state_id'      => request()->get('state_id'),
                'city_id'       => request()->get('city_id'),
                'location'        => request()->get('location'),
                'status'        => request()->get('status'),
                'shipping_zone_id'        => request()->get('shipping_zone_id'),
                ]; 

         DB::table('locations')->insert($data);

        $allimage = $this->images->getimages();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
         return redirect('admin/languages/manage_location');
    }

    public function edit_location($id)
    {
        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();
        $shipping_zones_charges = DB::table('shipping_zones_charges')->get();
        $locations = DB::table('locations')->where('id',$id)->first();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.locations.edit", $title)->with('result', $result)->with('locations',$locations)->with(['countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'shipping_zones_charges' => $shipping_zones_charges]);
    }

    public function edit_weight_slab($id) {
        $shipping_zones_charges = DB::table('shipping_zones_charges')->get();
        $shipping_weight_slab = DB::table('shipping_weight_slabs')->where('id',$id)->first();
        $title = array('pageTitle' => "Edit Weight Slab");
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.locations.shipzoneedit", $title)->with('result', $result)->with('shipping_weight_slab',$shipping_weight_slab)->with('shipping_zones_charges',$shipping_zones_charges);
    }

    public function update_weight_slab(Request $request)
    {
        $id = request()->get('id');
        $validator = Validator::make(
            array(
              'shipping_zone_id' => $request->shipping_zone_id,
              'slab_name' => $request->slab_name,
              'range_start' => $request->range_start,
              'range_end' => $request->range_end,
              'shipping_fee' => $request->shipping_fee,
              ),
            array(
              'shipping_zone_id'  => 'required',
              'slab_name'    => 'required',
              'range_start'    => 'required',
              'range_end'    => 'required',
              'shipping_fee'    => 'required',
              )
          );

          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

         $data = [
                'shipping_zone_id'    => request()->get('shipping_zone_id'),
                'slab_name'      => request()->get('slab_name'),
                'range_start'       => request()->get('range_start'),
                'range_end'        => request()->get('range_end'),
                'shipping_fee'        => request()->get('shipping_fee'),
                ];
        
        DB::table('shipping_weight_slabs')->where('id',$id)->update($data);
        $title = array('pageTitle' => "Weight Slabs");
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return redirect('admin/languages/shipping_zones_slabs');
    } 

    public function update_location(Request $request)
    {
         $id = request()->get('id');
        $validator = Validator::make(
            array(
              'country_id' => $request->country_id,
              'state_id' => $request->state_id,
              'location' => $request->location,
              ),
            array(
              'country_id'  => 'required',
              'state_id'    => 'required',
              'location'    => 'required',
              )
          );

          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

         $data = [
                'shipping_zone_id'    => request()->get('shipping_zone_id'),
                'country_id'    => request()->get('country_id'),
                'state_id'      => request()->get('state_id'),
                'city_id'       => request()->get('city_id'),
                'location'        => request()->get('location'),
                'status'        => request()->get('status'),
                ];
        
        DB::table('locations')->where('id',$id)->update($data);
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return redirect('admin/languages/manage_location');
    } 

    public function delete_location(Request $request)
    {
        if ($request->id) {
             DB::table('locations')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('location deleted successfully');
        } 
    }

    public function shipping_zone_delete(Request $request)
    {
        if ($request->id) {
            
            DB::table('shipping_zones_charges')->where('shipping_zone_id',$request->id)->delete();
            return redirect()->back()->withErrors('shipping zone deleted successfully');
        } 
        
    }

    public function delete_weightslab(Request $request)
    {
        if ($request->id) {
            
            DB::table('shipping_weight_slabs')->where('id',$request->id)->delete();
            return redirect()->back()->withErrors('Weight slab deleted successfully');
        } 
        
    }


     public function manage_vendors(Request $request, $type='all'){

         $countrylist = DB::table('country')->get();
         $statelist = DB::table('states')->get();
         $citylist = DB::table('cities')->get();
         $vendors = DB::table('vendors');
        if(!empty($type == 'approved')){
          $vendors->where('vendors.deleted_at', 0)->where('vendors.status', 1);
        }elseif(!empty($type == 'pending')){
            $vendors->where('vendors.deleted_at', 0)->where('vendors.status', 0);
        }elseif(!empty($type == 'rejected')){
            $vendors->where('vendors.deleted_at', 0)->where('vendors.status', 2);
        }else{
        $vendors->where('vendors.deleted_at', 0);
        }
         $filter='';
         $parameter='';
        /*if(isset($request->FilterBy)){
            $filter    = $request->FilterBy;
            $parameter = $request->parameter;
            if($filter=='Name'){
              $vendors->where('name','LIKE', '%' . $parameter . '%');  
            }elseif($filter=='E-mail'){
              $vendors->where('email','LIKE', '%' . $parameter . '%');  
            }elseif($filter=='Phone'){
              $vendors->where('phone',$parameter);  
            }
        }*/

           if(isset($request->name) && !empty($request->name)){
               $vendors->where('name', 'LIKE', '%' . $request->name . '%');
            }

            if(isset($request->email) && !empty($request->email)){
               $vendors->where('email', 'LIKE', '%' . $request->email . '%');
            }

            if(isset($request->phone) && !empty($request->phone)){
               $vendors->where('phone',$request->phone); 
            }

            if(isset($request->phone) && !empty($request->phone)){
               $vendors->where('phone',$request->phone); 
            }

            if(isset($request->status) && !empty($request->status)){
               $vendors->where('status',$request->status); 
            }

            if(isset($request->country_id) && !empty($request->country_id)){
               $vendors->where('country_id',$request->country_id); 
            }

            if(isset($request->state_id) && !empty($request->state_id)){
               $vendors->where('state_id',$request->state_id); 
            }

            if(isset($request->city_id) && !empty($request->city_id)){
               $vendors->where('city_id',$request->city_id); 
            }
            

        $vendorskk = $vendors->orderBy('vendors.id', 'desc')->paginate('10');

        foreach ($vendorskk as $key => $vendor) {
            $vendor->package_id = DB::table('packages')->where('id',$vendor->package_id)->get();

            $vendor->no_of_products = DB::table('products')->where('user_id',$vendor->user_id)->where('products_status',1)->get()->count();
        }

        $title = array('pageTitle' => 'Vendors');
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors.index", $title)->with('result', $result)->with('vendors',$vendorskk)->with('filter',$filter)->with('parameter',$parameter)->with(['countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist]);
    }

    public function filtervendor(Request $request){
      $filter    = $request->FilterBy;
      $parameter = $request->parameter;
        $vendors = DB::table('vendors')
        ->where('vendors.deleted_at', 0);
        if($filter=='Name'){
          $vendors->where('name','Like',"'%".$parameter."%'");  
        }elseif($filter=='E-mail'){
          $vendors->where('email','=',"'".$parameter."'");  
        }elseif($filter=='Phone'){
          $vendors->where('phone','=',$parameter);  
        }
        $vendors = $vendors->orderBy('vendors.id', 'desc')->paginate('50');
    
        $title = array('pageTitle' => 'Vendors');
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors.index", $title)->with('result', $result)->with('vendors',$vendors);
    }

    public function add_vendors(Request $request)
    {
        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();
        $locationlist = DB::table('locations')->get();
        $title = array('pageTitle' => Lang::get("Add Vendor"));
        $languages = $this->language->paginator();

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
            <input id="categories_' . $parents->categories_id . '" ' . $checked . ' type="checkbox" class=" required_one categories sub_categories" name="categories[]" value="' . $parents->categories_id . '">
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
        
        $result['categories'] = $option;
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors.add", $title)->with('result', $result)->with(['countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'locationlist' => $locationlist]);
    }

    public function vendors_insert(Request $request)
    {
        $validator = Validator::make(
            array(
              'name' => $request->name,
              'email' => $request->email,
              'phone' => $request->phone,
              'password' => $request->password,
              'country_id' => $request->country_id,
              'state_id' => $request->state_id,
              'city_id' => $request->city_id,
              'business_name' => $request->business_name,
              'business_address' => $request->business_address,
              
              ),
            array(
              'name'  => 'required',
              'email'  => 'required | email|unique:users,email',
              'phone'  => 'required|numeric|digits:10',
              'password' => 'required',
              'country_id'  => 'required',
              'state_id'    => 'required',
              'city_id'    => 'required',
              'business_name'    => 'required',
              'business_address'    => 'required',
              )
          );

          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

          if($request->hasFile('doucmument'))
            {
                foreach($request->file('doucmument') as $image)
                {
                    $name=$image->getClientOriginalName();
                    $image->move(public_path().'/doucmument/', $name);  
                    $images_data[] = $name;  
                }

                $data = [
                    'name'                  => request()->get('name'),
                    'email'                 => request()->get('email'),
                    'phone'                 => request()->get('phone'),
                    'password'              => Hash::make(request()->get('password')),
                    'country_id'            => request()->get('country_id'),
                    'state_id'              => request()->get('state_id'),
                    'city_id'               => request()->get('city_id'),
                    'business_name'         => request()->get('business_name'),
                    'business_address'      => request()->get('business_address'),
                    'doucmument'            => json_encode($images_data),
                    ];
            }else{

                $data = [
                'name'                  => request()->get('name'),
                'email'                 => request()->get('email'),
                'phone'                 => request()->get('phone'),
                'password'              => Hash::make(request()->get('password')),
                'country_id'            => request()->get('country_id'),
                'state_id'              => request()->get('state_id'),
                'city_id'               => request()->get('city_id'),
                'business_name'         => request()->get('business_name'),
                'business_address'      => request()->get('business_address'),
                ];
            }
         $userIds =  DB::table('vendors')->insertGetId($data);

        foreach($request->categories as $categories){
            DB::table('vendor_category')->insert([
                'user_id' => $userIds,
                'category_id' => $categories
            ]);
          }
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return redirect('admin/languages/manage_vendors');
    }

    public function edit_vendors($id)
    {
        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();
        $locationlist = DB::table('locations')->get();
        $vendors = DB::table('vendors')->where('id',$id)->first();
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $categories = $this->category->recursivecategories();
         
         $vendorCat = DB::table('vendor_category')->where('user_id', $id)->get();

         $categories_array = array();
        foreach($vendorCat as $vcategory){
            $categories_array[] = $vcategory->category_id;
        }



        $parent_id = $categories_array;
          
        $option = '<ul class="list-group list-group-root well">';

        foreach ($categories as $parents) {

            if (in_array($parents->categories_id, $parent_id)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }

            $option .= '<li href="#" class="list-group-item">
          <label style="width:100%">
            <input id="categories_' . $parents->categories_id . '" ' . $checked . ' type="checkbox" class=" required_one categories sub_categories" name="categories[]" value="' . $parents->categories_id . '">
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
        
         $result['categories'] = $option;

        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors.edit", $title)->with('result', $result)->with(['countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'locationlist' => $locationlist, 'vendors' => $vendors]);
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

            $contents .= '<label> <input id="categories_' . $child->categories_id . '" parents_id="' . $child->parent_id . '"  type="checkbox" name="categories[]" class="required_one sub_categories categories sub_categories_' . $child->parent_id . '" value="' . $child->categories_id . '" ' . $checked . '> ' . $child->categories_name . '</label>';

            if (isset($child->childs)) {
                $contents .= '<ul class="list-group">
        <li class="list-group-item">';
                $contents .= $this->childcat($child->childs, $parent_id);
                $contents .= "</li></ul>";
            }

        }
        return $contents;
    }

    public function update_vendors(Request $request)
    {
         $id = request()->get('id');
        $validator = Validator::make(
            array(
               'name' => $request->name,
               'email' => $request->email,
               'phone' => $request->phone,
              'country_id' => $request->country_id,
              'state_id' => $request->state_id,
              'city_id' => $request->city_id,
              'business_name' => $request->business_name,
              'business_address' => $request->business_address,
              
              ),
            array(
              'name'  => 'required',
              'email'  => 'required',
              'phone'  => 'required',  
              'country_id'  => 'required',
              'state_id'    => 'required',
              'city_id'    => 'required',
              'business_name'    => 'required',
              'business_address'    => 'required',
              )
          );

          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

          if($request->hasFile('doucmument'))
            {
                foreach($request->file('doucmument') as $image)
                {
                    $name=$image->getClientOriginalName();
                    $image->move(public_path().'/doucmument/', $name);  
                    $images_data[] = $name;  
                }

                $data = [
                    'name'                  => request()->get('name'),
                    'email'                 => request()->get('email'),
                    'phone'                 => request()->get('phone'),
                    'country_id'            => request()->get('country_id'),
                    'state_id'              => request()->get('state_id'),
                    'city_id'               => request()->get('city_id'),
                    'business_name'         => request()->get('business_name'),
                    'business_address'      => request()->get('business_address'),
                    'doucmument'            => json_encode($images_data),
                    ];
            }else{

                $data = [
                'name'                  => request()->get('name'),
                'email'                 => request()->get('email'),
                'phone'                 => request()->get('phone'),
                'country_id'            => request()->get('country_id'),
                'state_id'              => request()->get('state_id'),
                'city_id'               => request()->get('city_id'),
                'business_name'         => request()->get('business_name'),
                'business_address'      => request()->get('business_address'),
                ];
            }

           DB::table('vendor_category')->where([
              'user_id' => $id,
          ])->delete();
          foreach($request->categories as $categories){
            DB::table('vendor_category')->insert([
                'user_id' => $id,
                'category_id' => $categories
            ]);
          }
          $results = DB::table('vendors')->where('id',$id)->update($data);

       
        $title = array('pageTitle' => Lang::get("labels.EditLanguage"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return redirect('admin/languages/manage_vendors');
    }

    public function delete_vendors(Request $request)
    {
        if ($request->id) {
             DB::table('vendors')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('Vendor deleted successfully');
        } 
    }

    public function vendors_category($id)
    {
        $category = DB::table('categories')->where('parent_id',0)->get();
        $categoryids = array(0);
        foreach($category as $singlecat){
           $categoryids[] = $singlecat->categories_id; 
        }
        $categorydata = DB::table('categories_description')->where('language_id',1)->whereIn('categories_id',$categoryids)->get();

        $vendors = DB::table('vendor_category')
        ->leftjoin('categories_description', 'vendor_category.category_id', '=', 'categories_description.categories_id')
        ->where('vendor_category.user_id', $id)
        ->where('vendor_category.deleted_at', 0)
        ->paginate('10');

        $title = array('pageTitle' => 'Vendor Category');
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors_category.index", $title)->with('result', $result)->with(['vendors' => $vendors, 'categories' => $categorydata]);
    }

    public function vendors_insert_category(Request $request)
    { 

            $validator = Validator::make(
            array(
               'category_id' => $request->category_id,
              ),
            array(
              'category_id'  => 'required',
              )
          );


           $data = array(
             'category_id' => $request->category_id,
             'user_id'      => $request->user_id,
           ); 

          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
             DB::table('vendor_category')->insert($data);
            return redirect()->back()->withErrors('Vendor Category Added successfully'); 
    }

    public function update_vendors_category(Request $request)
    { 

        if ($request->id) {

            $validator = Validator::make(
            array(
               'category_id' => $request->category_id,
              ),
            array(
              'category_id'  => 'required',
              )
          );

          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
             DB::table('vendor_category')->where('id',$request->id)->update(['category_id' => $request->category_id]);
            return redirect()->back()->withErrors('Vendor Category Update successfully');
        } 
    }


    public function delete_vendors_category(Request $request)
    {
        if ($request->id) {
             DB::table('vendor_category')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('Vendor deleted successfully');
        } 
    }



    public function vendors_address($id, Request $request)
    {
        
        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();

        //'locations.location_name',

       /* echo $request->state_id;
        die;*/

        if(!empty($id)){
            $id = $id;
        }else{
            $id = $request->user_id;
        } 

        $vendors = DB::table('vendors_location')
        ->leftjoin('locations', 'vendors_location.location_name', '=', 'locations.id')
        ->leftjoin('country', 'vendors_location.country_id', '=', 'country.id')
        ->leftjoin('states', 'vendors_location.state_id', '=', 'states.id')
        ->leftjoin('cities', 'vendors_location.city_id', '=', 'cities.id')
        ->select('vendors_location.id', 'locations.location as location_name', 'vendors_location.shipping_fees', 'country.name as country_name', 'states.name as state_name', 'cities.name as city_name', 'vendors_location.status', 'vendors_location.created_at')
        ->where('vendors_location.vendor_id', $id)
        ->where('vendors_location.deleted_at', 0);
         
        if(isset($request->country_id) && !empty($request->country_id)){
            $vendors->where('vendors_location.country_id', $request->country_id);
        }

         if(isset($request->state_id) && !empty($request->state_id)){
            $vendors->where('vendors_location.state_id', $request->state_id);
         }

          if(isset($request->city_id) && !empty($request->city_id)){
            $vendors->where('vendors_location.city_id', $request->city_id);
          }

          if(isset($request->location_name) && !empty($request->location_name)){
            $vendors->where('vendors_location.location_name', $request->location_name);
          }
        $vendors = $vendors->paginate('10');
       
        //echo "<pre>";
        //print_r($vendors);
        //die;

        $title = array('pageTitle' => 'Vendor Address');
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors_address.index", $title)->with('result', $result)->with(['vendors' => $vendors, 'countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist]);
    }

    public function add_vendors_address()
    {
        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();
        $locationlist = DB::table('locations')->get();


        $title = array('pageTitle' => 'Edit Vendor Address');
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors_address.add", $title)->with('result', $result)->with(['countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'locationlist' => $locationlist]);
    }

    public function vendors_insert_address(Request $request)
    { 

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
             'vendor_id'      => $request->user_id,
             'shipping_fees' => !empty($request->shipping_fees) ? $request->shipping_fees:0,
             'min_order'    => !empty($request->min_order) ? $request->min_order:0,

           ); 

          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
           /* echo "<pre>";
            print_r($request->all());
            die;*/
             DB::table('vendors_location')->insert($data);
            return redirect()->back()->withErrors('Vendor Location Added successfully'); 
    }



    


    public function edit_vendors_address($vendierid,  $locationid)
    {
        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();
        $locationlist = DB::table('locations')->get();

        $vendors = DB::table('vendors_location')
        ->where('vendors_location.id', $locationid)
        ->where('vendors_location.deleted_at', 0)
        ->first();

       /* echo "<pre>";
        print_r($vendors);*/

        $title = array('pageTitle' => 'Edit Vendor Address');
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors_address.edit", $title)->with('result', $result)->with(['vendors' => $vendors, 'countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'locationlist' => $locationlist]);
    }

    public function update_vendors_address(Request $request)
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
            'shipping_fees' => !empty($request->shipping_fees) ? $request->shipping_fees:0,
            'min_order'    => !empty($request->min_order) ? $request->min_order:0,
          );  
          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
             DB::table('vendors_location')->where('id',$request->id)->update($data);
             //redirect()->to('admin/languages/vendors_address/'.$request->id->vendor_id);
             //return \Redirect::route('/vendors_address/', [$request->vendor_id])->withErrors('message', 'State saved correctly!!!');
            return redirect()->back()->withErrors('Vendor Location Update successfully');
        } 
    }


    public function delete_vendors_address(Request $request)
    {
        if ($request->id) {
             DB::table('vendors_location')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('Vendor deleted successfully');
        } 
    }

    public function update_vendor_status(Request $request)
    {
        $statusID = $request->statisIds;
        $vendorID = $request->vendorid;

        if(!empty($statusID)){

         $updatestatus =  DB::table('vendors')->where('id',$vendorID)->update(['status' => $statusID, 'account_inactive_status' => 0]);

          if($updatestatus == true){
            $vendordata =  DB::table('vendors')->where('id', $vendorID)->first();
            $checkUser = DB::table('users')->where('id',$vendordata->user_id)->first();
            if(empty($checkUser)){
                    $data = array(
                    'role_id' => 14,
                    'first_name' => $vendordata->name,
                    'phone' => $vendordata->phone,
                    'email' => $vendordata->email,
                    'password' => $vendordata->password,
                );
               $userId  = DB::table('users')->insertGetId($data);
               $updateusersId =  DB::table('vendors')->where('id',$vendorID)->update(['user_id' => $userId]);
            }            

           
           if($vendordata){
             if(!empty($vendordata->user_id) && ($statusID == 2)){
                DB::table('users')->where('id',$vendordata->user_id)->update(['status' => 0]);
             }
             if(!empty($vendordata->user_id) && ($statusID == 1)){
                DB::table('users')->where('id',$vendordata->user_id)->update(['status' => 1]);
             }
           }
          }
          echo "Succcfully Updated Status";
        }
    }

    public function vendors_request(Request $request)
    {
       $paymentrequests = DB::table("vendor_payment_requests")
       ->leftjoin('users', 'vendor_payment_requests.vendor_id', '=', 'users.id')
       ->select('vendor_payment_requests.id', 'vendor_payment_requests.reference_no', 'vendor_payment_requests.amount', 'vendor_payment_requests.status', 'vendor_payment_requests.created_at','vendor_payment_requests.updated_at', 'vendor_payment_requests.payment_amount', 'vendor_payment_requests.payment_date', 'users.first_name')
       ->orderBy('id','desc')
       ->get(); 
       $title = array('pageTitle' => Lang::get("Withdraw Payment "));
       $result['commonContent'] = $this->Setting->commonContent();
       return view("admin.vendor_request.index", $title)->with('result',$result)->with('paymentrequests',$paymentrequests); 
    }

    public function edit_vendors_request($id)
    {
        $package = DB::table('vendor_payment_requests')->where('id',$id)->first();
        $title = array('pageTitle' => Lang::get("Edit Vendor Request"));
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendor_request.edit", $title)->with('result', $result)->with('package',$package);
    }

    public function update_vendors_request(Request $request)
    {
        date_default_timezone_set("Asia/Kolkata");
        $date = Carbon::now()->toDateTimeString();

        if ($request->id) {

            $validator = Validator::make(
            array(
               'payment_amount' => $request->payment_amount,
               'status' => $request->status,
              ),
            array(
              'payment_amount'  => 'required',
              'status'  => 'required',
              )
          );

          $data = array(
            'payment_amount' => $request->payment_amount,
            'desc_note' => $request->description,
            'payment_id' => $request->payment_id,
            'status' => $request->status,
            'payment_date' => strtotime($date),
          );  
          
          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
             DB::table('vendor_payment_requests')->where('id',$request->id)->update($data);
             //redirect()->to('admin/languages/vendors_address/'.$request->id->vendor_id);
             //return \Redirect::route('/vendors_address/', [$request->vendor_id])->withErrors('message', 'State saved correctly!!!');
            return redirect('admin/languages/vendors_request')->withErrors('Vendor Location Update successfully');
        }
    }

    public function update_vendors_request_status(Request $request)
    {
        $rquestId = $request->requestid;
        $status = $request->reqtstatus;
        DB::table('vendor_payment_requests')->where('id',$rquestId)->update(['status' => $status]);
        echo "Succcfully Update Status";
        exit;
    }

     public function vendors_sub_category($id)
     {
        $category = DB::table('categories')->where('parent_id',0)->get();
        $categoryids = array(0);
        foreach($category as $singlecat){
           $categoryids[] = $singlecat->categories_id; 
        }
        $categorydata = DB::table('categories_description')->where('language_id',1)->whereIn('categories_id',$categoryids)->get();
        /*$subcategory  = DB::table('categories')->whereIn('parent_id',$categoryids)->where('parent_id', '!=', 0)->get();

         $subcategoryids = array(0);
          foreach($subcategory as $singlesubcat){
           $subcategoryids[] = $singlesubcat->categories_id; 
        }

        $subcategorydata = DB::table('categories_description')->where('language_id',1)->whereIn('categories_id',$subcategoryids)->get();

         echo "<pre>";
         print_r($subcategorydata);*/

        $vendors = DB::table('vendor_sub_category')
        ->leftjoin('categories_description as a', 'vendor_sub_category.category_id', '=', 'a.categories_id')
        ->leftjoin('categories_description as b', 'vendor_sub_category.sub_category_id', '=', 'b.categories_id')
        ->select('vendor_sub_category.id', 'vendor_sub_category.created', 'a.categories_name as categories_name', 'b.categories_name as subcategory')
        ->where('vendor_sub_category.user_id', $id)
        ->where('vendor_sub_category.deleted_at', 0)
        ->paginate('10');

        $title = array('pageTitle' => 'Vendor Category');
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.adminsubcategoies.index", $title)->with('result', $result)->with(['vendors' => $vendors, 'categories' => $categorydata]);
     }

     public function gesubcategory(Request $request)
     {
       $subcategory = DB::table('categories')->where('parent_id', $request->categoryid)->get();
       
       $categoryids = array(0);
       foreach ($subcategory as $key => $singlecat) {
            $categoryids[] = $singlecat->categories_id;
       }

      $categorydata = DB::table('categories_description')->where('language_id',1)->whereIn('categories_id',$categoryids)->pluck("categories_name","categories_id");
        return response()->json($categorydata);

     }
     public function add_sub_vendors_category(Request $request)
     {
         $validator = Validator::make(
            array(
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
              ),
            array(
              'category_id'  => 'required',
              'sub_category_id'  => 'required',
              )
          );


           $data = array(
             'user_id' => $request->user_id,
             'category_id' => $request->category_id,
             'sub_category_id' => $request->sub_category_id,

           ); 

          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
             DB::table('vendor_sub_category')->insert($data);
            return redirect()->back()->withErrors('Vendor Sub Category Added successfully'); 
     }


     public function delete_sub_vendors_category(Request $request)
    {
        if ($request->id) {
             DB::table('vendor_sub_category')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('Vendor deleted successfully');
        } 
    }

     public function vendors_account_request(Request $request, $type='all'){

        if(!empty($type == 'approved')){
            $vendors = DB::table('vendors')
            ->where('vendors.deleted_at', 0)
            ->where('vendors.status', 1)
            ->orderBy('vendors.id', 'desc')
            ->paginate('10');
        }elseif(!empty($type == 'rejected')){
            $vendors = DB::table('vendors')
            ->where('vendors.deleted_at', 0)
            ->where('vendors.status', 2)
            ->orderBy('vendors.id', 'desc')
            ->paginate('10');
        }else{
        $vendors = DB::table('vendors')
        ->where('vendors.deleted_at', 0)
        ->orderBy('vendors.id', 'desc')
        ->paginate('10');
        }
        $title = array('pageTitle' => 'Vendors');
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.account_request.index", $title)->with('result', $result)->with('vendors',$vendors);
    }




}
