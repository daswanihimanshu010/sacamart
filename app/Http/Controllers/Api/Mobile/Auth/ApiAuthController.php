<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Borrower;
use App\User;
use App\ForgotOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
class ApiAuthController extends Controller
{
    
	
	//send Login otp
	public function login(Request $request)
    {
     if($request->logintype=='borrower'){
		$validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits:10',
        ]);
     }else{
         $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits:10|exists:users,mobile_no',
        ]);
     }
		if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid mobile number or mobile number already register'
			]);
        }
		
		//send otp
		$otp = rand(1000,9999); 
		$signature = $request->signature;
        $this->sendSMS("<#> Your OTP is: ".$otp." ".$signature, $request->mobile);
		
		// forgot otp table also use for verify mobile
		$saveotp = new ForgotOtp;
		$saveotp->mobile = $request->mobile;
		$saveotp->otp = $otp;
		$saveotp->save();
		
		return response()->json([
			'status' => true,
			'msg' => 'Otp Send success'
			]);
		
    }
	
	
	
	
}
