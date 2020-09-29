<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{

protected $addHttpCookie = true;


protected $except = [
'paytm-callback', 'app_vendor_login', 'app_vendor_product_listing', 'app_vendor_orders_listing', 'app_vendor_orders_listing', 'app_vendor_update_qunty', 'app_update_product_price', 'app_update_order_status' 
];
}
