<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\AdminControllers\AlertController;
use App\Http\Controllers\AdminControllers\SiteSettingController;
use App\Http\Controllers\Controller;
use App\Models\Core\Categories;
use App\Models\Core\Images;
use App\Models\Core\Languages;
use App\Models\Core\Manufacturers;
use App\Models\Core\Products;
use App\Models\Core\Reviews;
use App\Models\Core\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;
use Auth;
use Image;
class ProductController extends Controller
{

    public function __construct(Products $products, Languages $language, Images $images, Categories $category, Setting $setting,
        Manufacturers $manufacturer, Reviews $reviews) {
        $this->category = $category;
        $this->reviews = $reviews;
        $this->language = $language;
        $this->images = $images;
        $this->manufacturer = $manufacturer;
        $this->products = $products;
        $this->myVarsetting = new SiteSettingController($setting);
        $this->myVaralter = new AlertController($setting);
        $this->Setting = $setting;

    }

    public function reviews(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.reviews"));
        $result = array();
         if(Auth::user()->role_id==14){
             $data = $this->reviews->paginator(Auth::user()->id);
        }else{
             $data = $this->reviews->paginator();
        }
        //$data = $this->reviews->paginator();
        $result['reviews'] = $data;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.reviews.index", $title)->with('result', $result);

    }

    public function editreviews($id, $status)
    {
        if ($status == 1) {
            DB::table('reviews')
                ->where('reviews_id', $id)
                ->update([
                    'reviews_status' => 1,
                ]);
            DB::table('reviews')
                ->where('reviews_id', $id)
                ->update([
                    'reviews_read' => 1,
                ]);
        } elseif ($status == 0) {
            DB::table('reviews')
                ->where('reviews_id', $id)
                ->update([
                    'reviews_read' => 1,
                ]);
        } else {
            DB::table('reviews')
                ->where('reviews_id', $id)
                ->update([
                    'reviews_read' => 1,
                    'reviews_status' => -1,
                ]);
        }
        $message = Lang::get("labels.reviewupdateMessage");
        return redirect()->back()->withErrors([$message]);

    }

    public function display(Request $request)
    {
        
        $language_id = '1';
        $categories_id = $request->categories_id;
        $product = $request->product;
        $title = array('pageTitle' => Lang::get("labels.Products"));
        $subCategories = $this->category->allcategories($language_id);
        //$products = $this->products->paginator($request);
        if(Auth::user()->role_id==14){
            $products = $this->products->paginator($request,Auth::user()->id);
        }else{
            $products = $this->products->paginator($request);
        }
        $results['products'] = $products;
        $results['currency'] = $this->myVarsetting->getSetting();
        $results['units'] = $this->myVarsetting->getUnits();
        $results['subCategories'] = $subCategories;
        $currentTime = array('currentTime' => time());
        //$vendors = DB::table('users')->where('role_id', 14)->select('id', 'first_name')->get(); 
        $vendors = DB::table('vendors')->where('account_status', 1)->where('deleted_at', 0)->where('user_id','!=',0)->select('user_id as id', 'name as first_name')->get(); 


//        echo "<pre>";
//        print_r($vendors);
//        die;
        $result['commonContent'] = $this->Setting->commonContent();

        $countrylist = DB::table('country')->get();
        $statelist = DB::table('states')->get();
        $citylist = DB::table('cities')->get();
        $locations = DB::table('locations')->where('deleted_at', 0)->get(); 

        /*echo "<pre>";
        print_r($product);die();*/
        return view("admin.products.index", $title)->with('result', $result)->with('results', $results)->with('categories_id', $categories_id)->with('product', $product)->with('vendors', $vendors)->with(['countrylist' => $countrylist, 'statelist' => $statelist, 'citylist' => $citylist, 'locations' => $locations]);

    }

    public function add(Request $request)
    {   
          if(Auth::user()->role_id==14){
               $packageVendor = DB::table('vendors')->where('user_id', Auth::user()->id)->first();
                if(!empty($packageVendor)){
                    $packaeId = $packageVendor->package_id;
                     $packageData = DB::table('packages')->where('id', $packaeId)->first();

                      $userparoduct = Products::where('user_id', Auth::user()->id)->count();
                      
                      if($packageData->no_of_product <= $userparoduct){
                        return redirect()->back()->withErrors(trans('Product Limit Exceed'));
                      }    

                }  
          }  
          
        $title = array('pageTitle' => Lang::get("labels.AddProduct"));
        $language_id = '1';
        $allimage = $this->images->getimages();
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
        
        $result['manufacturer'] = $this->manufacturer->getter($language_id);
        $taxClass = DB::table('tax_class')->get();
        $result['taxClass'] = $taxClass;
        $result['languages'] = $this->myVarsetting->getLanguages();
        $result['units'] = $this->myVarsetting->getUnits();
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.add", $title)->with('result', $result)->with('allimage', $allimage);

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

    public function edit(Request $request)
    {
        if(Auth::user()->role_id==14){
             
             $product = DB::table('products')->where('products_id', '=', $request->id)->where('user_id', '=', Auth::user()->id)->get();
             if($product->count()<1){
                 echo 'invalid request'; die;
             }
            
        }
        $allimage = $this->images->getimages();
        $result = $this->products->edit($request);
        
        //dd($result['categories_array']);
        $categories = $this->category->recursivecategories($request);

        $parent_id = $result['categories_array'];
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
        $title = array('pageTitle' => Lang::get("labels.EditProduct"));
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.edit", $title)->with('result', $result)->with('allimage', $allimage);

    }

    public function update(Request $request)
    {
        
       //echo "<pre>"; print_r($request->all());die;
        
        
        if ($request->hasFile('file') ) {
          
            $time = Carbon::now();
            $image = $request->file('file');
            $extensions = Setting::imageType();
            $size = getimagesize($image);
            list($width, $height, $type, $attr) = $size;
            $extension = $image->getClientOriginalExtension();
            $directory = date_format($time, 'Y') . '/' . date_format($time, 'm');
            $filename = str_random(5) . date_format($time, 'd') . rand(1, 9) . date_format($time, 'h') . "." . $extension;
            $upload_success = $image->storeAs($directory, $filename, 'public');
            $Path = 'images/media/' . $directory . '/' . $filename;
            $Images = new Images();
            $imagedata = $Images->imagedata($filename, $Path, $width, $height);
            $AllImagesSettingData = $Images->AllimagesHeightWidth();
           //print_r($AllImagesSettingData);die; 
            $tuhmbnailid = $this->storeThumbnialall($Path, $filename, $directory, $filename);
           
            $request['image_id'] =$tuhmbnailid; 
            
        } else{
            $request['image_id'] =0;
        }
 
        $result = $this->products->updaterecord($request);
        $products_id = $request->id;
        if ($request->products_type == 1) {
            return redirect('admin/products/attach/attribute/display/' . $products_id);
        } else {
            return redirect('admin/products/images/display/' . $products_id);
        }
    }


    public function storeThumbnialall($Path, $filename, $directory, $input)
    {
        $Images = new Images();
        $thumbnail_values = $Images->thumbnailHeightWidth();
        $originalImage = $Path;
        $destinationPath = public_path('images/media/' . $directory . '/');
        $thumbnailImage = Image::make($originalImage, array(
            'width' => $thumbnail_values[1]->value,
            'height' => $thumbnail_values[0]->value,
            'grayscale' => false));
        $returnimage = $thumbnailImage->save($destinationPath . 'thumbnail' . time() . $filename);
     
       
        $Path = 'images/media/' . $directory . '/' . 'thumbnail' . time() . $filename;
        $destinationFile = public_path($Path);
        $size = getimagesize($destinationFile);
        list($width, $height, $type, $attr) = $size;
        $Images = new Images();
        $storethumbnailid = $Images->thumbnailrecordreturnid($input, $Path, $width, $height);
        
       
        
           
        
       $Medium_values = $Images->MediumHeightWidth();
         $originalImage = $Path;
         $destinationPath = public_path('images/media/' . $directory . '/');
        $thumbnailImage = Image::make($originalImage, array(
             'width' => $Medium_values[1]->value,
             'height' => $Medium_values[0]->value,
             'grayscale' => false));
        $namemedium = $thumbnailImage->save($destinationPath . 'medium' . time() . $filename);
        $Path = 'images/media/' . $directory . '/' . 'medium' . time() . $filename;
         $destinationFile = public_path($Path);
        $size = getimagesize($destinationFile);
        list($width, $height, $type, $attr) = $size;
         $storeMediumImage = $Images->Mediumrecordreturnid($input, $Path, $width, $height);
       
        
        $Large_values = $Images->LargeHeightWidth();
        $originalImage = $Path;
        $destinationPath = public_path('images/media/' . $directory . '/');
        $thumbnailImage = Image::make($originalImage, array(
            'width' => $Large_values[1]->value,
            'height' => $Large_values[0]->value,
            'grayscale' => false));
       
        $namelarge = $thumbnailImage->save($destinationPath . 'large' . time() . $filename);
        $Path = 'images/media/' . $directory . '/' . 'large' . time() . $filename;
        $destinationFile = public_path($Path);
       
        
//        $size = getimagesize($destinationFile);
//        $width = $size[0];
//        $height = $size[1];
//        $storeLargeImage = $Images->Largerecordreturnid($input, $Path, $width, $height);
 
        return $storethumbnailid;
    }




    public function delete(Request $request)
    {
        $this->products->deleterecord($request);
        return redirect()->back()->withErrors([Lang::get("labels.ProducthasbeendeletedMessage")]);

    }

    public function insert(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.AddAttributes"));
        $language_id = '1';
         if(Auth::user()->role_id==14){
                $request['user_id']=Auth::user()->id;
            }
        //echo "<pre>"; print_r($request->all());die;
         if ($request->hasFile('file') ) {
          
            $time = Carbon::now();
            $image = $request->file('file');
            $extensions = Setting::imageType();
            $size = getimagesize($image);
            list($width, $height, $type, $attr) = $size;
            $extension = $image->getClientOriginalExtension();
            $directory = date_format($time, 'Y') . '/' . date_format($time, 'm');
            $filename = str_random(5) . date_format($time, 'd') . rand(1, 9) . date_format($time, 'h') . "." . $extension;
            $upload_success = $image->storeAs($directory, $filename, 'public');
            $Path = 'images/media/' . $directory . '/' . $filename;
            $Images = new Images();
            $imagedata = $Images->imagedata($filename, $Path, $width, $height);
            $AllImagesSettingData = $Images->AllimagesHeightWidth();
           //print_r($AllImagesSettingData);die; 
            $tuhmbnailid = $this->storeThumbnialall($Path, $filename, $directory, $filename);
           
            $request['image_id'] =$tuhmbnailid; 
            
        } 
        
        $products_id = $this->products->insert($request);
        $result['data'] = array('products_id' => $products_id, 'language_id' => $language_id);
        $alertSetting = $this->myVaralter->newProductNotification($products_id);
        if ($request->products_type == 1) {
            return redirect('/admin/products/attach/attribute/display/' . $products_id);
        } else {
            return redirect('admin/products/images/display/' . $products_id);
        }
    }

    public function addinventory(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductInventory"));
        $id = $request->id;
        $result = $this->products->addinventory($id);
        
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.inventory.add", $title)->with('result', $result);

    }

    public function ajax_min_max($id)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductInventory"));
        $result = $this->products->ajax_min_max($id);
        return $result;

    }

    public function ajax_attr($id)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductInventory"));
        $result = $this->products->ajax_attr($id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.inventory.attribute_div")->with('result', $result);

    }

    public function addinventoryfromsidebar(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.ProductInventory"));
        
        if(Auth::user()->role_id==14){
            $result = $this->products->addinventoryfromsidebar(Auth::user()->id);
        }else{
            $result = $this->products->addinventoryfromsidebar();
            
        }
        
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.inventory.add1", $title)->with('result', $result);

    }

    public function addnewstock(Request $request)
    {

        $this->products->addnewstock($request);
        return redirect()->back()->withErrors([Lang::get("labels.inventoryaddedsuccessfully")]);

    }

    public function addminmax(Request $request)
    {

        $this->products->addminmax($request);
        return redirect()->back()->withErrors([Lang::get("labels.Min max level added successfully")]);

    }

    public function displayProductImages(Request $request)
    {

        $title = array('pageTitle' => Lang::get("labels.AddImages"));
        $products_id = $request->id;
        $result = $this->products->displayProductImages($request);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products/images/index", $title)->with('result', $result)->with('products_id', $products_id);

    }

    public function addProductImages($products_id)
    {
        $title = array('pageTitle' => Lang::get("labels.AddImages"));
        $allimage = $this->images->getimages();
        $result = $this->products->addProductImages($products_id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view('admin.products.images.add', $title)->with('result', $result)->with('products_id', $products_id)->with('allimage', $allimage);

    }

    public function insertProductImages(Request $request)
    {
        $product_id = $this->products->insertProductImages($request);
        return redirect()->back()->with('product_id', $product_id);
    }
     public function uploadimage($products_id)
    {
        $title = array('pageTitle' => Lang::get("labels.AddImages"));
        $allimage = $this->images->getimages();
        $result = $this->products->addProductImages($products_id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view('admin.products.images.upload', $title)->with('result', $result)->with('products_id', $products_id)->with('allimage', $allimage);

    }

    public function insertuploadimage(Request $request)
    {
         if ($request->hasFile('gallery') ) {
                $gallleryimages = $request->gallery;
                $product_id = $request->products_id;
                $sort_order = $request->sort_order;
                $htmlcontent = $request->htmlcontent;
            if(count($gallleryimages)>0){
                foreach($gallleryimages as $key=>$singleimage){

                        $time = Carbon::now();
                        $image = $singleimage;
                        $extensions = Setting::imageType();
                        $size = getimagesize($image);
                        list($width, $height, $type, $attr) = $size;
                        $extension = $image->getClientOriginalExtension();
                        $directory = date_format($time, 'Y') . '/' . date_format($time, 'm');
                        $filename = str_random(5) . date_format($time, 'd') . rand(1, 9) . date_format($time, 'h') . "." . $extension;
                        $upload_success = $image->storeAs($directory, $filename, 'public');
                        $Path = 'images/media/' . $directory . '/' . $filename;
                        $Images = new Images();
                        $imagedata = $Images->imagedata($filename, $Path, $width, $height);
                        $AllImagesSettingData = $Images->AllimagesHeightWidth();
                       //print_r($AllImagesSettingData);die; 
                        $tuhmbnailid = $this->storeThumbnialall($Path, $filename, $directory, $filename);
                         DB::table('products_images')->insert([
                             'products_id' => $product_id,
                             'image' => $tuhmbnailid,
                             'htmlcontent' => $htmlcontent,
                             'sort_order' => $sort_order+$key,
                         ]);
                }
            }
        } 
        return redirect()->back()->with('product_id', $product_id);
    }
    
    

    public function editProductImages($id)
    {

        $allimage = $this->images->getimages();
        $products_images = $this->products->editProductImages($id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/images/edit")->with('products_images', $products_images)->with('allimage', $allimage);

    }

    public function updateproductimage(Request $request)
    {

        $title = array('pageTitle' => Lang::get("labels.Manage Values"));
        $result = $this->products->updateproductimage($request);
        return redirect()->back();

    }

    public function deleteproductimagemodal(Request $request)
    {

        $products_id = $request->products_id;
        $id = $request->id;
        $result['data'] = array('products_id' => $products_id, 'id' => $id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/images/modal/delete")->with('result', $result);

    }

    public function deleteproductimage(Request $request)
    {
        $this->products->deleteproductimage($request);
        return redirect()->back()->with('success', trans('labels.DeletedSuccessfully'));

    }

    public function addproductattribute(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.AddAttributes"));
        $result = $this->products->addproductattribute($request);
        //echo "<pre>"; print_r($result);die;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.attribute.add", $title)->with('result', $result);
    }

    public function addnewdefaultattribute(Request $request)
    {
        $products_attributes = $this->products->addnewdefaultattribute($request);
        return ($products_attributes);
    }

    public function editdefaultattribute(Request $request)
    {
        $result = $this->products->editdefaultattribute($request);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/pop_up_forms/editdefaultattributeform")->with('result', $result);
    }

    public function updatedefaultattribute(Request $request)
    {
        $products_attributes = $this->products->updatedefaultattribute($request);
        return ($products_attributes);

    }

    public function deletedefaultattributemodal(Request $request)
    {

        $products_id = $request->products_id;
        $products_attributes_id = $request->products_attributes_id;
        $result['data'] = array('products_id' => $products_id, 'products_attributes_id' => $products_attributes_id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/modals/deletedefaultattributemodal")->with('result', $result);

    }

    public function deletedefaultattribute(Request $request)
    {
        $products_attributes = $this->products->deletedefaultattribute($request);
        return ($products_attributes);
    }

    public function showoptions(Request $request)
    {
        $products_attributes = $this->products->showoptions($request);
        return ($products_attributes);
    }

    public function editoptionform(Request $request)
    {
        $result = $this->products->editoptionform($request);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/pop_up_forms/editproductattributeoptionform")->with('result', $result);

    }

    public function updateoption(Request $request)
    {
        $products_attributes = $this->products->updateoption($request);
        return ($products_attributes);
    }

    public function showdeletemodal(Request $request)
    {

        $products_id = $request->products_id;
        $products_attributes_id = $request->products_attributes_id;
        $result['data'] = array('products_id' => $products_id, 'products_attributes_id' => $products_attributes_id);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin/products/modals/deleteproductattributemodal")->with('result', $result);

    }

    public function deleteoption(Request $request)
    {

        $products_attributes = $this->products->deleteoption($request);
        return ($products_attributes);

    }

    public function getOptionsValue(Request $request)
    {
        $value = $this->products->getOptionsValue($request);
        if (count($value) > 0) {
            foreach ($value as $value_data) {
                $value_name[] = "<option value='" . $value_data->products_options_values_id . "'>" . $value_data->options_values_name . "</option>";
            }
        } else {
            $value_name = "<option value=''>" . Lang::get("labels.ChooseValue") . "</option>";
        }
        print_r($value_name);
    }

    public function currentstock(Request $request)
    {

        $result = $this->products->currentstock($request);
        print_r(json_encode($result));

    }
    public function import_product(Request $request){
        
        $result = array();
        $title = array('pageTitle' => Lang::get("Add Product"));
        $language_id = '1';
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.products.import", $title)->with('result', $result);
    }

    public function insert_import_product(Request $request)
    {
        $request->validate([
            'file'          => 'required|required|mimes:csv,txt',
        ]);
       
        $dealer_id = $request->dealer;
        if($_FILES['file']['name']!=""){
            $timeDate = date("Y-m-d H:i:sP");
            $newTime = strtotime($timeDate);
            $filename = $_FILES['file']['name']; 

            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $filenamenew = 'upload_file_'.time().'.'.$ext;

            if($ext=="csv"){
                $src    = $_FILES['file']['tmp_name'];
                $destinationPath = public_path().'/doucmument/'.$filenamenew;

                if(move_uploaded_file($src,$destinationPath)){
                    $ignoreFirstRow = 1;
                    $has_error = 0;

                    if (($handle = fopen(public_path().'/doucmument/'.$filenamenew, "r")) !== FALSE){
                        $error_array = array();
                        $data_insert_arrray = array();

                        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
                            if($ignoreFirstRow != 1){
                                $data_insert_arrray[]=$data;
                            }
                            $ignoreFirstRow++;
                        }

                        if(Auth::user()->role_id==14){
                           
                            $packageVendor = DB::table('vendors')->where('user_id', Auth::user()->id)->first();
                            if(!empty($packageVendor)){
                                $packaeId = $packageVendor->package_id;
                                 $packageData = DB::table('packages')->where('id', $packaeId)->first();

                                  $userparoduct = Products::where('user_id', Auth::user()->id)->count();
                                  
                                  if($packageData->no_of_product <= $userparoduct){
                                    return redirect()->back()->withErrors(trans('Product Limit Exceed'));
                                  }

                                  if($packageData->no_of_product <= count($data_insert_arrray)){
                                    return redirect()->back()->withErrors(trans('Product Limit Exceed'));
                                  }
                            }

                            
                        }
                        //echo "<pre>"; print_r($data_insert_arrray);die;
                            $incr_valu = 1;
                            $recordinsert = 0;
                            
                            $errors = array();
                            foreach ($data_insert_arrray as $key => $data) {
                                //echo str_slug($data[0], "-").time();die;
                               $categoryId =  $this->getcategoryId($data[12]);
                                if($categoryId == 0){
                                    $errors[] = 'Invalid Category name in row -'.($key+1);
                                }else{
                                //sleep(1);
                                $product = new Products();
                                $product->user_id = Auth::user()->id;
                                $product->products_quantity         = $data[5];
                                $product->productuniqueno           = $categoryId."-".Auth::user()->id;
                                $product->products_model            = $data[6];
                                $product->products_image            = $data[1];

                                $product->products_price            = $data[3];
                                $product->products_date_added       = 0;
                                $product->products_last_modified    = 0;
                                $product->products_date_available   = 0;
                                
                                $product->products_weight           = $data[7];
                                $product->products_weight_unit      = $data[8];
                                $product->products_video_link       = $data[10];
                                $product->is_feature = 0;
                                if($data[9] == 'Yes'){
                                 $product->is_feature                = 1;
                                }
                                $product->products_status           = 0;
                                $product->is_current                = 1;
                                $product->products_tax_class_id     = 2;
                                $product->manufacturers_id          = 1;
                                $product->products_ordered          = 0;
                                $product->products_liked            = 0;
                                $product->low_limit                 = 0;
                                $product->products_slug             = str_slug($data[0], "-").time().$key;
                                $product->products_type             = 0;
                                $product->products_min_order        = 1;
                                $product->products_max_stock        = $data[11];;
                                
                            //echo "<pre>";print_r($product);die;
                                if($product->save()){
                                    $language_id = 1;
                                    $productid = $product->id;
                                    $result['data'] = array('products_id' => $productid, 'language_id' => $language_id);
                                    $alertSetting = $this->myVaralter->newProductNotification($productid);
                                    $productdata = array(
                                        'products_id' => $productid,
                                        'language_id' => 1,
                                        'products_name' => $data[0],
                                        'products_description' => $data[2],
                                        'products_url' => ''
                                    );

                                    $prodata2 = array(
                                        'products_id' => $productid,
                                        'categories_id' => $categoryId, 
                                    );
                                  DB::table('products_description')->insert($productdata);
                                  DB::table('products_to_categories')->insert($prodata2);
                                }
                                $recordinsert++;
                                $incr_valu++;
                              }
                            }
                        //print_r($errors);die;
                            if(count($errors)>0){
                                $errorsdata = implode(' OR  ',$errors);
                                return redirect()->back()->withErrors($errorsdata);
//                                echo "<pre>"; 
//                                print_r($errors); 
//                                echo '</pre>'; exit;
                            }
                        
                            return redirect('admin/products/display')->with('success', trans('Product Added Successfully'));
                            
                        }
                    }
                }
            }else{
                return redirect()->back()->withErrors([Lang::get("Please Select File")]);
            }
            return redirect()->back()->withErrors([Lang::get("Error in import data")]);
        }

        function getcategoryId($name){
            $category = DB::table('categories_description')->where('categories_name', $name)->get();
            if($category->count()>0){
                return $category[0]->categories_id; 
            }else{
                return 0;
            }
               
        }  

}
