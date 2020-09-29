<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;
use GuzzleHttp\Client;

class Order extends Model
{
    public function paginator($type,$request){


         
       
        $language_id = '1';
        if(Auth::user()->role_id==14){
            $myorderids = $this->getuservenderproducts(Auth::user()->id);
            $orders = DB::table('orders')->orderBy('created_at', 'DESC');
            //dd($request->all());
            
             $statusids =array();
                if($type=='completed'){
                    $statusids = array(2,3,4);
                }elseif($type=='process'){
                    $statusids = array(1,5,6,7);
                }elseif($type=='return'){
                    $statusids = array(4);
                }
   

                //dd($statusids);
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
            if(isset($request->o_no) && !empty($request->o_no)){
               $orders->where('orders_id', $request->o_no);
            }
            if(isset($request->name) && !empty($request->name)){
               $orders->where('customers_name', $request->name);
            }
            if(isset($request->fromdate) && !empty($request->fromdate)){
               $orders->where('created_at','>=', date('Y-m-d 00:00:00',strtotime($request->fromdate)));
            }

             
            if(isset($request->todate) && !empty($request->todate)){
               $orders->where('created_at','<=',date('Y-m-d 00:00:00',strtotime($request->todate)));
            }
            $orders->where('customers_id', '!=', '')->whereIn('orders_id',$myorderids);
            $orders =  $orders->paginate(40);
            
        }elseif(Auth::user()->role_id==1){

           $orders = DB::table('orders')->orderBy('created_at', 'DESC');

           $statusids =array();
                if($type=='completed'){
                    $statusids = array(2,3,4);
                }elseif($type=='process'){
                    $statusids = array(1,5,6,7);
                }elseif($type=='return'){
                    $statusids = array(4);
                }
                if(count($statusids)<1){
                   $ordersbystatus = DB::table('orders_status_history')->where('orders_status_id', '=', $request->status)->get(); 
                }else{
                   $ordersbystatus = DB::table('orders_status_history')->whereIn('orders_status_id', $statusids)->get(); 
                    $myorderids = array();
                    foreach($ordersbystatus as $singleordk){
                       $myorderids[] =  $singleordk->orders_id;
                    }
                }

               /* echo "<pre>";
                print_r($myorderids);
                die; */

             if(isset($request->status) && !empty($request->status)){
                $ordersbystatus = DB::table('orders_status_history')->where('orders_status_id', '=', $request->status)->get();
                $myorderids = array();
                foreach($ordersbystatus as $singleordk){
                   $myorderids[] =  $singleordk->orders_id;
                }
            }   

            if(isset($request->o_no) && !empty($request->o_no)){
               $orders->where('orders_id', $request->o_no);
            }

            if(isset($request->name) && !empty($request->name)){
               $orders->where('customers_name', $request->name);
            }

            if(isset($request->country_id) && !empty($request->country_id)){

                $counrtuserids = DB::table('country')->where('id', $request->country_id)->first();

                /*$counrtuseridsarray = array();
                foreach ($counrtuserids as $counrtuseridskey => $counrtuseridsvalue) {
                    $userids = DB::table('vendors')->where('id', $counrtuseridsvalue->vendor_id)->where('deleted_at', 0)->first();
                    $counrtuseridsarray[] = $userids->user_id;
                    
                } 

                $producData = DB::table('products')->whereIn('user_id', $counrtuseridsarray)->get();

                  $productidsarray = array(); 
                 foreach ($producData as $producDatakey => $producDatavalue) {
                    $productidsarray[] = $producDatavalue->products_id;
                 }

                 $orderproducData = DB::table('orders_products')->whereIn('products_id', $productidsarray)->groupBy('orders_id')->get();

                  $ordertidsarray = array(); 
                 foreach ($orderproducData as $orderproducDatakey => $orderproducDatavalue) {
                    $ordertidsarray[] = $orderproducDatavalue->orders_id;
                 }*/
                    
                 //$orders->where('delivery_country', $counrtuserids->name);   
            }

            if(isset($request->state_id) && !empty($request->state_id)){
                $stateuserids = DB::table('states')->where('id', $request->state_id)->first();

                //$producDatastate = DB::table('products')->where('delivery_state', $stateuserids->name)->fist();
/*
                  $producstatetidsarray = array(); 
                 foreach ($producDatastate as $producDatakey => $producDataStatevalue) {
                    $producstatetidsarray[] = $producDataStatevalue->products_id;
                 }

                 $orderstateproducData = DB::table('orders_products')->whereIn('products_id', $producstatetidsarray)->groupBy('orders_id')->get();

                  $orderstatetidsarray = array(); 
                 foreach ($orderstateproducData as $orderproducstateDatakey => $orderproducstateDatavalue) {
                    $orderstatetidsarray[] = $orderproducstateDatavalue->orders_id;
                 }*/

                $orders->where('delivery_state', $stateuserids->name); 
            }

            if(isset($request->city_id) && !empty($request->city_id)){
                $stateuserids = DB::table('cities')->where('id', $request->city_id)->first();

               /* $stateuseridsarray = array();
                foreach ($stateuserids as $counrtuseridskey => $counrtuseridsvalue) {
                    $userids = DB::table('vendors')->where('id', $counrtuseridsvalue->vendor_id)->where('deleted_at', 0)->first();
                    $stateuseridsarray[] = $userids->user_id;
                    
                } 

                $producDatastate = DB::table('products')->whereIn('user_id', $stateuseridsarray)->get();

                  $producstatetidsarray = array(); 
                 foreach ($producDatastate as $producDatakey => $producDataStatevalue) {
                    $producstatetidsarray[] = $producDataStatevalue->products_id;
                 }

                 $orderstateproducData = DB::table('orders_products')->whereIn('products_id', $producstatetidsarray)->groupBy('orders_id')->get();

                  $orderstatetidsarray = array(); 
                 foreach ($orderstateproducData as $orderproducstateDatakey => $orderproducstateDatavalue) {
                    $orderstatetidsarray[] = $orderproducstateDatavalue->orders_id;
                 }*/

                $orders->where('delivery_city', 'LIKE', '%' . $stateuserids->name . '%'); 
                //$orders->where('customers_city', $request->city_id);
            }

            if(isset($request->location_name) && !empty($request->location_name)){

                $stateuserids = DB::table('locations')->where('id', $request->location_name)->where('deleted_at', 0)->first();

                /*$stateuseridsarray = array();
                foreach ($stateuserids as $counrtuseridskey => $counrtuseridsvalue) {
                    $userids = DB::table('vendors')->where('id', $counrtuseridsvalue->vendor_id)->where('deleted_at', 0)->first();
                    $stateuseridsarray[] = $userids->user_id;
                    
                } 

                $producDatastate = DB::table('products')->whereIn('user_id', $stateuseridsarray)->get();

                  $producstatetidsarray = array(); 
                 foreach ($producDatastate as $producDatakey => $producDataStatevalue) {
                    $producstatetidsarray[] = $producDataStatevalue->products_id;
                 }

                 $orderstateproducData = DB::table('orders_products')->whereIn('products_id', $producstatetidsarray)->groupBy('orders_id')->get();

                  $orderstatetidsarray = array(); 
                 foreach ($orderstateproducData as $orderproducstateDatakey => $orderproducstateDatavalue) {
                    $orderstatetidsarray[] = $orderproducstateDatavalue->orders_id;
                 }*/

                $orders->where('delivery_postcode', $stateuserids->location); 

                //$orders->where('customers_suburb', $request->location_name);
            }
            if(isset($request->fromdate) && !empty($request->fromdate)){
               $orders->where('created_at','>=', date('Y-m-d 00:00:00',strtotime($request->fromdate)));
            }


            if(isset($request->todate) && !empty($request->todate)){
               $orders->where('created_at','<=',date('Y-m-d 00:00:00',strtotime($request->todate)));
            }
            if(!empty($myorderids)){
             $orders->where('customers_id', '!=', '')->whereIn('orders_id',$myorderids);
            }
            $orders =  $orders->paginate(40);

             $index = 0;
            $total_price = array();

        foreach ($orders as $orders_data) {
            $orders_products = DB::table('orders_products')->sum('final_price');

            $orders[$index]->total_price = $orders_products;

            $orders_status_history = DB::table('orders_status_history')
                ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
                ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
                ->select('orders_status_description.orders_status_name', 'orders_status_description.orders_status_id')
                ->where('orders_status_description.language_id', '=', $language_id)
                ->where('orders_id', '=', $orders_data->orders_id)
                ->where('role_id', '<=', 2)
                ->orderby('orders_status_history.date_added', 'DESC')->limit(1)->get();

            $orders[$index]->orders_status_id = $orders_status_history[0]->orders_status_id;
            $orders[$index]->orders_status = $orders_status_history[0]->orders_status_name;
            $index++;

        }
        return $orders;


        }else{
            $orders = DB::table('orders')->orderBy('created_at', 'DESC');
            //dd($request->all());
            if(isset($request->status) && !empty($request->status)){
                $ordersbystatus = DB::table('orders_status_history')->where('orders_status_id', '=', $request->status)->get();
                $myorderids = array();
                foreach($ordersbystatus as $singleordk){
                   $myorderids[] =  $singleordk->orders_id;
                }
            }
            if(isset($request->o_no) && !empty($request->o_no)){
               $orders->where('orders_id', $request->o_no);
            }
            if(isset($request->name) && !empty($request->name)){
               $orders->where('customers_name', $request->name);
            }
            if(isset($request->fromdate) && !empty($request->fromdate)){
               $orders->where('created_at','>=', date('Y-m-d 00:00:00',strtotime($request->fromdate)));
            }
            if(isset($request->todate) && !empty($request->todate)){
               $orders->where('created_at','<=',date('Y-m-d 00:00:00',strtotime($request->todate)));
            }
            $orders->where('customers_id', '!=', '')->whereIn('orders_id',$myorderids);
            $orders =  $orders->paginate(40);
        }
        
        $index = 0;
        $total_price = array();

        foreach ($orders as $orders_data) {
            $orders_products = DB::table('orders_products')->sum('final_price');

            $orders[$index]->total_price = $orders_products;

            $orders_status_history = DB::table('orders_status_history')
                ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
                ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
                ->select('orders_status_description.orders_status_name', 'orders_status_description.orders_status_id')
                ->where('orders_status_description.language_id', '=', $language_id)
                ->where('orders_id', '=', $orders_data->orders_id)
                ->where('role_id', '<=', 2)
                ->orderby('orders_status_history.date_added', 'DESC')->limit(1)->get();

            $orders[$index]->orders_status_id = $orders_status_history[0]->orders_status_id;
            $orders[$index]->orders_status = $orders_status_history[0]->orders_status_name;
            $index++;

        }
//        echo "<pre>";
//        print_r($orders);die;
        return $orders;
    }

    public function detail($request){

        $language_id = '1';
        $orders_id = $request->id; 
        $ordersData = array();       
        $subtotal  = 0;
        
        if(Auth::user()->role_id==14){
            $myorderids = $this->getuservenderproducts(Auth::user()->id);
            $orders = DB::table('orders')->orderBy('created_at', 'DESC')->where('customers_id', '!=', '')->whereIn('orders_id',$myorderids)->where('orders_id',$orders_id)->get();
             if($orders->count()<1){
                 echo 'invalid request';exit;
             }                                                                          
        }
        
        DB::table('orders')->where('orders_id', '=', $orders_id)
            ->where('customers_id', '!=', '')->update(['is_seen' => 1]);

        $order = DB::table('orders')
            ->LeftJoin('orders_status_history', 'orders_status_history.orders_id', '=', 'orders.orders_id')
            ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
            ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
            ->where('orders_status_description.language_id', '=', $language_id)
            ->where('role_id', '<=', 2)
            ->where('orders.orders_id', '=', $orders_id)->orderby('orders_status_history.date_added', 'DESC')->get();

        if(Auth::user()->role_id==14){

            foreach ($order as $data) {
            $orders_id = $data->orders_id;

            $orders_products = DB::table('orders_products')
                ->join('products', 'products.products_id', '=', 'orders_products.products_id')
                ->LeftJoin('tax_rates', 'tax_rates.tax_class_id', '=', 'products.products_tax_class_id')
                ->LeftJoin('image_categories', function ($join) {
                    $join->on('image_categories.image_id', '=', 'products.products_image')
                        ->where(function ($query) {
                            $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                                ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                                ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                        });
                })
                ->select('products.including_tax', 'orders_products.*', 'image_categories.path as image', 'tax_rates.tax_rate')
                ->where('products.user_id', '=', Auth::user()->id)
                ->where('orders_products.orders_id', '=', $orders_id)->get();
    
                $i = 0;
                $total_price = 0;
                $total_tax = 0;
                $product = array();
                $subtotal = 0;
                foreach ($orders_products as $orders_products_data) {
                    $product_attribute = DB::table('orders_products_attributes')
                        ->where([
                            ['orders_products_id', '=', $orders_products_data->orders_products_id],
                            ['orders_id', '=', $orders_products_data->orders_id],
                        ])
                        ->get();

                    $orders_products_data->attribute = $product_attribute;
                    $product[$i] = $orders_products_data;
                    $total_price = $total_price + $orders_products[$i]->final_price;

                    $subtotal += $orders_products[$i]->final_price;

                    $i++;
                }
                $data->data = $product;
                $orders_data[] = $data;
            }
        
        } else {    

        foreach ($order as $data) {
            $orders_id = $data->orders_id;

            $orders_products = DB::table('orders_products')
                ->join('products', 'products.products_id', '=', 'orders_products.products_id')
                ->LeftJoin('tax_rates', 'tax_rates.tax_class_id', '=', 'products.products_tax_class_id')
                ->LeftJoin('image_categories', function ($join) {
                    $join->on('image_categories.image_id', '=', 'products.products_image')
                        ->where(function ($query) {
                            $query->where('image_categories.image_type', '=', 'THUMBNAIL')
                                ->where('image_categories.image_type', '!=', 'THUMBNAIL')
                                ->orWhere('image_categories.image_type', '=', 'ACTUAL');
                        });
                })
                ->select('products.including_tax','orders_products.*', 'image_categories.path as image', 'tax_rates.tax_rate')
                ->where('orders_products.orders_id', '=', $orders_id)->get();
    
                $i = 0;
                $total_price = 0;
                $total_tax = 0;
                $product = array();
                $subtotal = 0;
                foreach ($orders_products as $orders_products_data) {
                    $product_attribute = DB::table('orders_products_attributes')
                        ->where([
                            ['orders_products_id', '=', $orders_products_data->orders_products_id],
                            ['orders_id', '=', $orders_products_data->orders_id],
                        ])
                        ->get();

                    $orders_products_data->attribute = $product_attribute;
                    $product[$i] = $orders_products_data;
                    $total_price = $total_price + $orders_products[$i]->final_price;

                    $subtotal += $orders_products[$i]->final_price;

                    $i++;
                }
                $data->data = $product;
                $orders_data[] = $data;
            }

        }

        $ordersData['orders_data'] = $orders_data;
        $ordersData['total_price'] = $total_price;
        $ordersData['subtotal'] = $subtotal;

        return $ordersData;
    }

    public function currentOrderStatus($request){
        $language_id = 1;
        $status = DB::table('orders_status_history')
            ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
            ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
            ->where('orders_status_description.language_id', '=', $language_id)
            ->where('role_id', '<=', 2)
            ->orderBy('orders_status_history.date_added', 'desc')
            ->where('orders_id', '=', $request->id)->get();
            return $status;
    }

    public function orderStatuses(){
        $language_id = 1;
        $status = DB::table('orders_status')
                ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
                ->where('orders_status_description.language_id', '=', $language_id)->where('role_id', '<=', 2)->get();
        return $status;
    }

    public function updateRecord($request){
        $date_added = date('Y-m-d h:i:s');
        $orders_status = $request->orders_status;
        $old_orders_status = $request->old_orders_status;

        $comments = $request->comments;
        $orders_id = $request->orders_id;


        $status = DB::table('orders_status')->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
            ->where('orders_status_description.language_id', '=', 1)->where('role_id', '<=', 2)->where('orders_status_description.orders_status_id', '=', $orders_status)->get();

        //orders status history
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

        if ($orders_status == '5') {

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

            $orders_info = DB::table('orders')->where('orders_id', '=', $orders_id)->get();

            $orders_products = DB::table('orders_products')->where('orders_id', '=', $orders_id)->get();

            $vendor_list = array();

            foreach ($orders_products as $products_data) {
                $vendor_product_info = DB::table('products')->LeftJoin('vendors', 'vendors.user_id', '=', 'products.user_id')->where('products_id', $products_data->products_id)->get();

                $vendor_pickup_code = $vendor_product_info[0]->pickup_code;

                if(!in_array($vendor_pickup_code, $vendor_list, true)){
                    array_push($vendor_list, $vendor_pickup_code);
                }

            }


            for ($i=0;$i<sizeof($vendor_list);$i++) {
                $order_items = DB::table('orders_products')->LeftJoin('products', 'products.products_id', '=', 'orders_products.products_id')->LeftJoin('products_description', 'products_description.products_id', '=', 'products.products_id')->LeftJoin('vendors', 'vendors.user_id', '=', 'products.user_id')->where('orders_products.orders_id', '=', $orders_id)->where('vendors.pickup_code', '=', $vendor_list[$i])->select('products_description.products_name as name','products.products_id as sku','orders_products.products_quantity as units','orders_products.final_price as selling_price','products_description.shipping_weight as discount')->get();

                $order_price_per = 0;
                $total_shipping_weight = 0;
                foreach ($order_items as $order_item) {
                    $order_price_per = $order_price_per + $order_item->selling_price;
                    $total_shipping_weight = $total_shipping_weight + $order_item->discount;
                }

                $order_price_per = $order_price_per + (json_decode($orders_info, true)[0]['total_tax'] / sizeof($vendor_list)) + json_decode($orders_info, true)[0]['shipping_cost'];

                $body1['order_id'] = $orders_id."-".$vendor_list[$i];
                $body1['order_date'] = json_decode($orders_info, true)[0]['date_purchased'];
                $body1['pickup_location'] = $vendor_list[$i];
                $body1['billing_customer_name'] = json_decode($orders_info, true)[0]['customers_name'];
                $body1['billing_last_name'] = json_decode($orders_info, true)[0]['customers_name'];
                $body1['billing_address'] = json_decode($orders_info, true)[0]['customers_street_address'];
                $body1['billing_city'] = json_decode($orders_info, true)[0]['customers_city'];
                $body1['billing_pincode'] = json_decode($orders_info, true)[0]['customers_postcode'];
                $body1['billing_pincode'] = substr($body1['billing_pincode'],2,8);
                $body1['billing_state'] = json_decode($orders_info, true)[0]['customers_state'];
                $body1['billing_country'] = "India";
                $body1['billing_email'] = json_decode($orders_info, true)[0]['email'];
                $body1['billing_phone'] = json_decode($orders_info, true)[0]['billing_phone'];
                $body1['shipping_is_billing'] = true;
                
                $body1['order_items'] = $order_items;
                $body1['payment_method'] = json_decode($orders_info, true)[0]['payment_method'];
                $body1['sub_total'] = $order_price_per;
                $body1['length'] = "10";
                $body1['breadth'] = "10";
                $body1['height'] = "10";
                $body1['weight'] = $total_shipping_weight;

                $response1 = $vendor_client->post("https://apiv2.shiprocket.in/v1/external/orders/create/adhoc", [ 'body' => json_encode($body1) ]);

                $body1 = json_decode($response1->getBody(), true);

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
                    'admin_id' => auth()->user()->id,
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

        return 'success';
    }    


    //
    public function fetchorder($request)
    {
        $reportBase = $request->reportBase;
        $language_id = '1';
        $orders = DB::table('orders')
            ->LeftJoin('currencies', 'currencies.code', '=', 'orders.currency')
            ->get();

        $index = 0;
        $total_price = array();
        foreach ($orders as $orders_data) {
            $orders_products = DB::table('orders_products')
                ->select('final_price', DB::raw('SUM(final_price) as total_price'))
                ->where('orders_id', '=', $orders_data->orders_id)
                ->groupBy('final_price')
                ->get();

            $orders[$index]->total_price = $orders_products[0]->total_price;

            $orders_status_history = DB::table('orders_status_history')
                ->LeftJoin('orders_status', 'orders_status.orders_status_id', '=', 'orders_status_history.orders_status_id')
                ->LeftJoin('orders_status_description', 'orders_status_description.orders_status_id', '=', 'orders_status.orders_status_id')
                ->select('orders_status_description.orders_status_name', 'orders_status_description.orders_status_id')
                ->where('orders_id', '=', $orders_data->orders_id)
                ->where('orders_status_description.language_id', '=', $language_id)
                ->where('role_id', '<=', 2)
                ->orderby('orders_status_history.date_added', 'DESC')->limit(1)->get();

            $orders[$index]->orders_status_id = $orders_status_history[0]->orders_status_id;
            $orders[$index]->orders_status = $orders_status_history[0]->orders_status_name;

            $index++;
        }

        $compeleted_orders = 0;
        $pending_orders = 0;
        foreach ($orders as $orders_data) {

            if ($orders_data->orders_status_id == '2') {
                $compeleted_orders++;
            }
            if ($orders_data->orders_status_id == '1') {
                $pending_orders++;
            }
        }

        $result['orders'] = $orders->chunk(10);
        $result['pending_orders'] = $pending_orders;
        $result['compeleted_orders'] = $compeleted_orders;
        $result['total_orders'] = count($orders);

        $result['inprocess'] = count($orders) - $pending_orders - $compeleted_orders;
        //add to cart orders
        $cart = DB::table('customers_basket')->get();

        $result['cart'] = count($cart);

        //Rencently added products
        $recentProducts = DB::table('products')
            ->leftJoin('products_description', 'products_description.products_id', '=', 'products.products_id')
            ->where('products_description.language_id', '=', $language_id)
            ->orderBy('products.products_id', 'DESC')
            ->paginate(8);

        $result['recentProducts'] = $recentProducts;

        //products
        $products = DB::table('products')
            ->leftJoin('products_description', 'products_description.products_id', '=', 'products.products_id')
            ->where('products_description.language_id', '=', $language_id)
            ->orderBy('products.products_id', 'DESC')
            ->get();

        //low products & out of stock
        $lowLimit = 0;
        $outOfStock = 0;
        foreach ($products as $products_data) {
            $currentStocks = DB::table('inventory')->where('products_id', $products_data->products_id)->get();
            if (count($currentStocks) > 0) {
                if ($products_data->products_type == 1) {


                } else {
                    $stockIn = 0;

                    foreach ($currentStocks as $currentStock) {
                        $stockIn += $currentStock->stock;
                    }
                    /*print $stocks;
                    print '<br>';*/
                    $orders_products = DB::table('orders_products')
                        ->select(DB::raw('count(orders_products.products_quantity) as stockout'))
                        ->where('products_id', $products_data->products_id)->get();
                    //print($product->products_id);
                    //print '<br>';
                    $stocks = $stockIn - $orders_products[0]->stockout;

                    $manageLevel = DB::table('manage_min_max')->where('products_id', $products_data->products_id)->get();
                    $min_level = 0;
                    $max_level = 0;
                    if (count($manageLevel) > 0) {
                        $min_level = $manageLevel[0]->min_level;
                        $max_level = $manageLevel[0]->max_level;
                    }

                    /*print 'min level'.$min_level;
                    print '<br>';
                    print 'max level'.$max_level;
                    print '<br>';*/

                    if ($stocks >= $min_level) {
                        $lowLimit++;
                    }
                    $stocks = $stockIn - $orders_products[0]->stockout;
                    if ($stocks == 0) {
                        $outOfStock++;
                    }

                }
            } else {
                $outOfStock++;
            }
        }

        $result['lowLimit'] = $lowLimit;
        $result['outOfStock'] = $outOfStock;
        $result['totalProducts'] = count($products);

        $customers = DB::table('customers')
            ->LeftJoin('customers_info', 'customers_info.customers_info_id', '=', 'customers.customers_id')
            ->leftJoin('images', 'images.id', '=', 'customers.customers_picture')
            ->leftJoin('image_categories', 'image_categories.image_id', '=', 'customers.customers_picture')
            ->where('image_categories.image_type', '=', 'THUMBNAIL')
            ->select('customers.created_at', 'customers_id', 'customers_firstname', 'customers_lastname', 'customers_dob', 'email', 'user_name', 'customers_default_address_id', 'customers_telephone', 'customers_fax'
                , 'password', 'customers_picture', 'path')
            ->orderBy('customers.created_at', 'DESC')
            ->get();

        $result['recentCustomers'] = $customers->take(6);
        $result['totalCustomers'] = count($customers);
        $result['reportBase'] = $reportBase;

    //  get function from other controller
    //  $myVar = new AdminSiteSettingController();
    //  $currency = $myVar->getSetting();
    //  $result['currency'] = $currency;

        return $result;
    }

    public function deleteRecord($request){
        DB::table('orders')->where('orders_id', $request->orders_id)->delete();
        DB::table('orders_products')->where('orders_id', $request->orders_id)->delete();
        return 'success';
    }

    public function reverseStock($request){
        $orders_products = DB::table('orders_products')->where('orders_id', '=', $request->orders_id)->get();

        foreach ($orders_products as $products_data) {

            $product_detail = DB::table('products')->where('products_id', $products_data->products_id)->first();
            //dd($product_detail);
            $date_added = date('Y-m-d h:i:s');
            $inventory_ref_id = DB::table('inventory')->insertGetId([
                'products_id' => $products_data->products_id,
                'stock' => $products_data->products_quantity,
                'admin_id' => auth()->user()->id,
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
        return 'success';
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
