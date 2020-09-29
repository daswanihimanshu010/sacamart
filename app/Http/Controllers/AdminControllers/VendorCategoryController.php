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
use App\Models\Core\Categories;
use App\Models\Core\Languages;
use Auth;

class VendorCategoryController extends Controller
{
    //
    public function __construct(Coupon $coupon, Setting $setting, Languages $language, Categories $category)
    {
        $this->Coupon = $coupon;
        $this->myVarSetting = new SiteSettingController($setting);
        $this->Setting = $setting;
        $this->language = $language;
        $this->category = $category;

    }

    public function display(Request $request)
    {
        $id = Auth::user()->id;

        $vendorId = DB::table('vendors')->where('user_id', $id)->first();

      /*  echo "<pre>";
        print_r($vendorId);
        die;*/

       /* $category = DB::table('categories')->where('parent_id',0)->get();
        $categoryids = array(0);
        foreach($category as $singlecat){
           $categoryids[] = $singlecat->categories_id; 
        }

        $categorydata = DB::table('categories_description')->where('language_id',1)->whereIn('categories_id',$categoryids)->get();*/

       /* $vendors = DB::table('vendor_category')
        ->leftjoin('categories_description', 'vendor_category.category_id', '=', 'categories_description.categories_id')
        ->where('vendor_category.user_id', $vendorId->id)
        ->where('vendor_category.deleted_at', 0)
        ->paginate('10');*/

        $vendorCat = DB::table('vendor_category')->where('user_id', $vendorId->id)->get();
         $categories = $this->category->recursivecategories();

        
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

        $title = array('pageTitle' => 'Vendor Category');
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
         
        $result['commonContent'] = $this->Setting->commonContent();
        /*echo "string";
         die;*/
        return view("admin.vendors_category_new.index", $title)->with('result', $result);
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
               'categories' => $request->categories,
              ),
            array(
              'categories'  => 'required',
              )
          );

          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }

          DB::table('vendor_category')->where([
              'user_id' => $vendorId->id,
          ])->delete();
          foreach($request->categories as $categories){
            DB::table('vendor_category')->insert([
                'user_id' => $vendorId->id,
                'category_id' => $categories
            ]);
          }
            return redirect()->back()->withErrors('Vendor Category Added successfully');


    }

    public function edit(Request $request, $id)
    {

        $title = array('pageTitle' => Lang::get("labels.EditCoupon"));
        $result = array();
        $message = array();
        $result['message'] = $message;
        //coupon
        $coupon = $this->Coupon->getcoupon($id);
        $result['coupon'] = $coupon;
        $emails = $this->Coupon->getemail();
        $result['emails'] = $emails;
        $products = $this->Coupon->getproduct();
        $result['products'] = $products;
        $categories = $this->Coupon->getcategories();
        $result['categories'] = $categories;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.coupons.edit", $title)->with('result', $result);
    }

    public function update(Request $request)
    {

        $coupans_id = $request->id;
        if (!empty($request->free_shipping)) {
            $free_shipping = $request->free_shipping;
        } else {
            $free_shipping = '0';
        }
        $code = $request->code;
        $description = $request->description;
        $discount_type = $request->discount_type;
        $amount = $request->amount;
        $date = str_replace('/', '-', $request->expiry_date);
        $expiry_date = date('Y-m-d', strtotime($date));
        if (!empty($request->individual_use)) {
            $individual_use = $request->individual_use;
        } else {
            $individual_use = '';
        }
        //include products
        if (!empty($request->product_ids)) {
            $product_ids = implode(',', $request->product_ids);
        } else {
            $product_ids = '';
        }
        if (!empty($request->exclude_product_ids)) {
            $exclude_product_ids = implode(',', $request->exclude_product_ids);
        } else {
            $exclude_product_ids = '';
        }
        $usage_limit = $request->usage_limit;
        $usage_limit_per_user = $request->usage_limit_per_user;
        if (!empty($request->product_categories)) {
            $product_categories = implode(',', $request->product_categories);
        } else {
            $product_categories = '';
        }
        if (!empty($request->excluded_product_categories)) {
            $excluded_product_categories = implode(',', $request->excluded_product_categories);
        } else {
            $excluded_product_categories = '';
        }
        if (!empty($request->email_restrictions)) {
            $email_restrictions = implode(',', $request->email_restrictions);
        } else {
            $email_restrictions = '';
        }
        $minimum_amount = $request->minimum_amount;
        $maximum_amount = $request->maximum_amount;
        $validator = Validator::make(
            array(
                'code' => $request->code,
            ),
            array(
                'code' => 'required',
            )
        );

        if ($request->usage_count !== null) {
            $usage_count = $request->usage_count;
        } else {
            $usage_count = 0;
        }
        if ($request->used_by !== null) {
            $used_by = $request->used_by;
        } else {
            $used_by = '';
        }
        if ($request->limit_usage_to_x_items !== null) {
            $limit_usage_to_x_items = $request->limit_usage_to_x_items;
        } else {
            $limit_usage_to_x_items = 0;
        }
        //check validation
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            //check coupon already exist
            $couponInfo = $this->Coupon->getcode($code);
            if (count($couponInfo) > 1) {
                return redirect()->back()->withErrors(Lang::get("labels.CouponAlreadyError"))->withInput();
            } else if (empty($code)) {
                return redirect()->back()->withErrors(Lang::get("labels.EnterCoupon"))->withInput();
            } else {
                //insert record
                $coupon_id = $this->Coupon->couponupdate($coupans_id, $code, $description, $discount_type, $amount, $individual_use,
                    $product_ids, $exclude_product_ids, $usage_limit, $usage_limit_per_user, $usage_count,
                    $limit_usage_to_x_items, $product_categories, $used_by, $excluded_product_categories,
                    $request, $email_restrictions, $minimum_amount, $maximum_amount, $expiry_date, $free_shipping);

                $message = Lang::get("labels.CouponUpdatedMessage");
                return redirect()->back()->withErrors([$message]);
            }

        }

    }

    public function delete(Request $request)
    {
        if ($request->id) {
             DB::table('vendor_category')->where('id',$request->id)->update(['deleted_at' => 1]);
            return redirect()->back()->withErrors('Vendor deleted successfully');
        } 
    }

    public function subcategorydisplay(Request $request)
    {
        $id = Auth::user()->id;
        $vendorId = DB::table('vendors')->where('user_id', $id)->first();
        $categories = $this->category->recursivecategories();
        $vendorCat = DB::table('vendor_category')->where('user_id', $vendorId->id)->get();

        

         $categories_array = array();
        foreach($vendorCat as $vcategory){
            $categories_array[] = $vcategory->category_id;
        }

         $parent_id = $categories_array;

          $categoryarray = array();
          $subcategoryarray = array();
         foreach ($categories as $parents) {
            $singlearray = array();
            $singlearray['categories_id'] = $parents->categories_id;
            $singlearray['categories_name'] = $parents->categories_name;
            $singlearray['parent_id'] = $parents->parent_id;
            if (isset($parents->childs)) {
                $subcategoryname =  $this->childcat($parents->childs, $parent_id);
                $subcategoryid =  $this->childcatx($parents->childs, $parent_id);
                
                $singlearray['sub_categories_name'] = $subcategoryname;
                $singlearray['sub_categories_id'] = $subcategoryid;
                
            } 
            $categoryarray[] = $singlearray;

         }

         

          $vendors = $categoryarray; 
                
        $category = DB::table('categories')->where('parent_id',0)->get();
        $categoryids = array(0);
        foreach($category as $singlecat){
           $categoryids[] = $singlecat->categories_id; 
        }

        $categorydata = DB::table('categories_description')->where('language_id',1)->whereIn('categories_id',$categoryids)->get();

       /*$vendors = DB::table('vendor_sub_category')
        ->leftjoin('categories_description as a', 'vendor_sub_category.category_id', '=', 'a.categories_id')
        ->leftjoin('categories_description as b', 'vendor_sub_category.sub_category_id', '=', 'b.categories_id')
        ->select('vendor_sub_category.id', 'vendor_sub_category.created', 'a.categories_name as categories_name', 'b.categories_name as subcategory')
        ->where('vendor_sub_category.user_id', $id)
        ->where('vendor_sub_category.deleted_at', 0)
        ->paginate('10');*/

        /*echo "<pre>";
        print_r($vendors);
        die;*/

        $title = array('pageTitle' => 'Vendor Category');
        $languages = $this->language->paginator();
        $result['languages'] = $languages;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.vendors_sub_category.index", $title)->with('result', $result)->with(['vendors' => $vendors, 'categories' => $categorydata]);
    }

    /*public function childcat($childs, $parent_id)
    {

        $contents = '';
        foreach ($childs as $key => $child) {
           $contents .=  $child->categories_name;
            if (isset($child->childs)) {
                
                $contents = $this->childcat($child->childs, $parent_id);
            }

        }
        return $contents;
    }*/

    /*public function childcatx($childs, $parent_id)
    {

        $contents = '';
        foreach ($childs as $key => $child) {
           $contents .=  $child->categories_id;
            if (isset($child->childs)) {
                
                $contents = $this->childcatx($child->childs, $parent_id);
            }

        }
        return $contents;
    }*/

    public function insertsubcategory(Request $request)
    {
         $userId = Auth::user()->id;
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
             'user_id' => $userId,
             'category_id' => $request->category_id,
             'sub_category_id' => $request->sub_category_id,

           );  

          if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
          }
          $checkcat =  DB::table('vendor_sub_category')->where('sub_category_id',  $request->sub_category_id)->where('user_id', $userId)->first();
          if(!empty($checkcat)){
            return redirect()->back()->withErrors('Vendor Category Already exists');
          }
             DB::table('vendor_sub_category')->insert($data);
            return redirect()->back()->withErrors('Vendor Sub Category Added successfully'); 
    }

    public function gesubcategorys(Request $request)
     {
       $subcategory = DB::table('categories')->where('parent_id', $request->categoryid)->get();
       
       $categoryids = array(0);
       foreach ($subcategory as $key => $singlecat) {
            $categoryids[] = $singlecat->categories_id;
       }

      $categorydata = DB::table('categories_description')->where('language_id',1)->whereIn('categories_id',$categoryids)->pluck("categories_name","categories_id");
        return response()->json($categorydata);

     }

    public function deletesubcategory(Request $request)
    {
        
        if ($request->id) {
            $id = Auth::user()->id;
             $vendorId = DB::table('vendors')->where('user_id', $id)->first();
             DB::table('vendor_category')->where('category_id',$request->id)->where('user_id', $vendorId->id)->delete();
            return redirect()->back()->withErrors('Vendor sub category deleted successfully');
        } 
    }

}
