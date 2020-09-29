<?php

namespace App\Models\Web;

use App\Http\Controllers\Web\AlertController;
use App\Models\Web\Index;
use App\Models\Web\Products;
use App\User;
use Auth;
use Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Lang;
use Session;
use Socialite;

class Categories extends Model
{

  
    public function recursivecategories(){
        
             $items = DB::table('categories')
              ->leftJoin('categories_description','categories_description.categories_id', '=', 'categories.categories_id')
              ->select('categories.categories_id', 'categories_description.categories_name', 'categories.parent_id')
              ->where('language_id','=', 1)
              ->where('categories_status', '1')
              
              //->orderby('categories_id','ASC')
              ->get();
            //print_r($items);die;
            
       
          $childs = array();
          foreach($items as $item)
              $childs[$item->parent_id][] = $item;

          foreach($items as $item) if (isset($childs[$item->categories_id]))
              $item->childs = $childs[$item->categories_id];
          if(count($childs)>0){
            $tree = $childs[0];
          }else{
            $tree = $childs;
          }

          return  $tree;
    }


}
