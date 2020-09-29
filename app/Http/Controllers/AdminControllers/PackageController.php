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

class PackageController extends Controller
{

    public function __construct(Languages $language, Images $images, Setting $setting)
    {

        $this->language = $language;
        $this->images = $images;
        $this->Setting = $setting;

    }

    //languages
    public function display(Request $request)
    {
        echo "string";
        //$packages = DB::table('packages')->paginate('10');
        //$country = DB::table('packages')->where('deleted_at', NUll)->paginate('10');
        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        //return view("admin.package.index", $title)->with(['result' => $result]);
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
                    ->leftJoin('image_categories', 'image_categories.image_id', '=', 'languages.image')
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

        $country = DB::table('country')->paginate('10');
        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.country.index", $title)->with('result', $result)->with('country',$country);
    }

    public function manage_state(Request $request){
   
        $states = DB::table('states')
        ->leftjoin('country', 'states.country_id', '=', 'country.id')
        ->select('country.name as country_name', 'states.name as state_name', 'states.id')
        ->paginate('10');
       /* echo "<pre>";
        print_r($states);
        die;*/
        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.states.index", $title)->with('result', $result)->with('states',$states);
    }

    public function manage_city(Request $request){

        $cities = DB::table('cities')
        ->leftjoin('states', 'cities.state_id', '=', 'states.id')
        ->select('states.name as state_name', 'cities.name as city_name', 'cities.id')
        ->paginate('10');
         
        $title = array('pageTitle' => Lang::get("labels.ListingLanguages"));
        $result = array();
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.cities.index", $title)->with('result', $result)->with('cities',$cities);
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
        ->where("state_id",$request->state_id)
        ->pluck("name","id");
        return response()->json($cities);
    }   
}
