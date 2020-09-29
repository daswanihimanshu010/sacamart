<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Borrower;
use App\LoanField;
use App\LoanType;
use App\Loan;
use App\LoanBank;
use App\LoanDocument;
use App\LoanPendingDocument;
use App\LoanUploadPendingDocument;
use App\LoanDocumentDtatus;
use App\DocumentGroup;
use App\DocumentField;
use App\LoanStatus;
use App\LoanBankStatus;
use App\Emi;
use App\ReferalEarn;
use App\Master;
use App\MasterValue;
use App\TodoTask;
use App\Banner;
use App\Contact;
use App\Blog;
use App\LoanUserDocumentType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use DB;
use URL;
class DataController extends Controller
{
    
	
	
	public function loanList(Request $request)
    {
		$loans = LoanType::where('status', 1)->get();
        return response()->json([
            'status' => true,
			'base_url' => URL::to('/').'/',
            'data' => $loans
        ]);
    }
	
    public function bannerlist()
    {
		$bannerlist = Banner::where('status', 1)->get();
        $bannerarray = array();
        foreach($bannerlist as $singlebanner){
            $singlebannern = array();
            $singlebannern['id'] = $singlebanner->id; 
            $singlebannern['title'] = $singlebanner->title; 
            $singlebannern['banner'] = url('/').'/'.$singlebanner->banner; 
            $singlebannern['link'] = $singlebanner->link; 
            $bannerarray[] = $singlebannern;
        }
        return response()->json([
            'status' => true,
			'base_url' => URL::to('/').'/',
            'data' => $bannerarray
        ]);
    }
   
	
	
	public function createLoanForm(Request $request)
    {
		$loanType = LoanType::where('id', $request->loan_type)->first();
		$loanfieldsArray = json_decode($loanType->loan_fields);
		$loanfields = implode(',',json_decode($loanType->loan_fields));
		//$fields = LoanField::whereIn('id', $loanfields)->get();
		$fields = LoanField::where('status', 1)
		->whereIn('id', $loanfieldsArray)
		->orderByRaw("FIELD(id, $loanfields)")->get()
		->map(function ($f) {			
			$f->condition_field = (is_null($f->condition_field )) ? "" : $f->condition_field ;			
			$f->condition_value = (is_null($f->condition_value )) ? "" : $f->condition_value ;			
			return $f;
		});
		
		
		//$fields = $fields->sortBy(function ($model) use ($loanfields) {
		//	return array_search($model->id, $loanfields);
		//});
		$borrowers = Borrower::where('status', 1)->get();
		
		
		
		return response()->json([
            'status' => true,
            'loanType' => $loanType,
            'fields' => $fields,
            'borrowers' => $borrowers
        ]);
		
    }
	
	
	public function saveLoanForm(Request $request)
    {
		//dd($request);
		//get loan fields
		$loanType = LoanType::where('id', $request->loan_type)->first();
		$loanfields = json_decode($loanType->loan_fields);	
		$fields = LoanField::whereIn('id', $loanfields)->get();
        
        if(!isset($request->loan_type)){
             return response()->json([
                'status' => false,
                'errors' => 'loan type required'
			]);
        }
        if(!isset($request->borrower)){
             return response()->json([
                'status' => false,
                'errors' => 'borrower required'
			]);
        }
        
        $rules = [
			'borrower' => 'required|exists:borrower,id',
			'loan_type' => 'required|exists:loantype,id'
		];
		
		$data = $this->createRule($rules, $fields, $request);
		
		$rules = $data['rules'];
		$fields = $data['fields'];
		
		
		
        $validator = Validator::make($request->all(),  $rules);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Error in input fields'
			]);
        }
		
		
        
        $loan = new Loan();
        $loan->borrower_id = $request->borrower;
        $loan->loan_type_id = $request->loan_type;
        $loan->fields = json_encode($fields);
        $loan->save();


        return response()->json([
            'status' => true,
            'msg' => 'Loan created successfully',
            'data' => $loan->id
            
			]);

    }


	
	
	public function borrowerLoanList(Request $request){
		
		$validator = Validator::make($request->all(), [
            'borrower_id' => 'required|exists:borrower,id',
        ]);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid borrower'
			]);
        }
		
		$loans = Loan::with('assign_detail')
		->with('borrower_detail')
		->with('loan_type_detail')
		->where('borrower_id', $request->borrower_id)
		->latest()->get();
		//print_r($loans);die;
		return response()->json([
            'status' => true,
            'site_url' => URL::to('/').'/',
            'data' => $loans
        ]);
		
	}
	
	public function borrowerLoanDetail(Request $request){
		
		$validator = Validator::make($request->all(), [
            'loan_id' => 'required|exists:loan,id',
        ]);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid loan id'
			]);
        }
        
        
        
        
		
//		$loans = Loan::with('borrower_detail')
//		->with('loan_type_detail')
//		->with(['loan_bank' => function($query){
//		    $query->with('bank_detail');
//			$query->with('bank_assign_to');
//			$query->with(['loan_bank_all_status_detail' => function($q){
//				$q->with('loan_status_detail');
//				$q->with('loan_sub_status_detail');
//			}]);
//			
//			
//		}])
//		->where('id', $request->loan_id)
//		->first();
        
        $loans = array();
        $mainloan = Loan::with('borrower_detail')->where('id', $request->loan_id)->first();
        $loantype = LoanType::where('id', $mainloan->loan_type_id)->first();
        
         //echo"<pre>";print_r($mainloan->toArray());die;
        $loandata = json_decode($mainloan->fields);
       // echo"<pre>";print_r($loanfieldsArray);die;
        $loans['id'] = $mainloan->id;
        $loans['Loan_Amount'] ="0.00";
        if(isset($loandata->Loan_Amount)){
             $loans['Loan_Amount'] = $loandata->Loan_Amount;
        }
       
        $loans['borrower_id'] = $mainloan->borrower_id;
        $loans['loan_type_id'] = $mainloan->loan_type_id;
        $loans['loantype'] = $loantype->title;
        $loans['fields'] = $mainloan->fields;
        $loans['loan_status'] = $mainloan->loan_status;
        $loans['assign_to'] = $mainloan->assign_to;
        $loans['status'] = $mainloan->status;
        $assignto = array();
        if(!empty($mainloan->borrower_detail)){
            $assignto['id'] = $mainloan->borrower_detail->id;
            $assignto['name'] = $mainloan->borrower_detail->name;
            $assignto['email'] ="";
            if(!empty($mainloan->borrower_detail->email)){
                 $assignto['email'] = $mainloan->borrower_detail->email;
            }
             $assignto['mobile_no'] ="";
            if(!empty($mainloan->borrower_detail->mobile_no)){
                 $assignto['mobile_no'] = $mainloan->borrower_detail->mobile_no;
            }
             $assignto['pin_code'] ="";
            if(!empty($mainloan->borrower_detail->pin_code)){
                 $assignto['pin_code'] = $mainloan->borrower_detail->pin_code;
            }
             $assignto['address'] ="";
            if(!empty($mainloan->borrower_detail->address)){
                 $assignto['address'] = $mainloan->borrower_detail->address;
            }
             $assignto['dob'] ="";
            if(!empty($mainloan->borrower_detail->dob)){
                 $assignto['dob'] = $mainloan->borrower_detail->dob;
            }
     
        }
        $loans['borrower_detail'] =  $assignto; 
        
         $statusbank = LoanBank::with('bank_detail')
            ->with('bank_assign_to')
            ->with(['loan_bank_all_status_detail' => function($q){
				$q->with('loan_status_detail');
				$q->with('loan_sub_status_detail');
			}])->where('loan_id', $request->loan_id)->get();
        
        //print_r($statusbank->toArray());
        $bankdata = array();
        if($statusbank->count()>0){
                foreach($statusbank as $singlebank){
                    //print_R($singlebank->toArray());die;
                    $singlebankn = array();
                    $singlebankn['id'] = $singlebank->bank_id;
                    $singlebankn['bank_id'] = $singlebank->id;
                    $singlebankn['bank_name'] = $singlebank->bank_detail->title;
                    $singlebankn['assign_to'] = $singlebank->assign_to;
                    $singlebankn['created_at'] = $singlebank->created_at->format('Y-m-d H:i:s');
                    $singlebankn['updated_at'] = $singlebank->updated_at->format('Y-m-d H:i:s');
                    $assignto = array();
                    $assignto['name'] = "";
                    $assignto['email'] = "";
                    $assignto['mobile'] = "";
                    if(!empty($singlebank->assign_to)){
                        $assignto['name'] = $singlebank->bank_assign_to->name;
                        $assignto['email'] = $singlebank->bank_assign_to->email;
                        $assignto['mobile'] = $singlebank->bank_assign_to->mobile_no;
                    }
                    $singlebankn['assignto'] = $assignto;
                    $mainstatus = array();
                    foreach($singlebank->loan_bank_all_status_detail as $singlestatusbank){
                        //echo "<pre>";print_r($singlestatusbank->toArray());die;
                        $singlestatus = array();
                        $singlestatus['id'] = $singlestatusbank->id;
                        $singlestatus['loan_status'] = $singlestatusbank->loan_status;
                        $singlestatus['loan_name'] = $singlestatusbank->loan_status_detail->title;
                        $singlestatus['loan_sub_status'] = $singlestatusbank->loan_sub_status;
                        $singlestatus['loan_sub_status_name'] ="";
                        if(isset($singlestatusbank->loan_sub_status_detail->title)){
                            //print_r($singlestatusbank->loan_sub_status_detail);die;
                            $singlestatus['loan_sub_status_name'] = $singlestatusbank->loan_sub_status_detail->title;
                        }
                        
                        $singlestatus['external_comment'] = $singlestatusbank->external_comment;
                        $singlestatus['internal_comment'] = $singlestatusbank->internal_comment;
                        $singlestatus['created_at'] = $singlestatusbank->created_at->format('Y-m-d H:i:s');
                        $substatusarraymain = array();
                        if(isset($singlestatusbank->loan_sub_status_detail)){
                            //print_r($singlesubst);die;
                           $singlesubst =  $singlestatusbank->loan_sub_status_detail;
                            $mysinglesub = array();
                            $mysinglesub['id'] = $singlesubst->id;
                            $mysinglesub['substatus'] = $singlesubst->title;
                            $mysinglesub['status'] = $singlestatus['loan_name'];
                            $mysinglesub['substatus'] = $singlesubst->title;
                            $mysinglesub['created_at'] = $singlesubst->created_at->format('Y-m-d H:i:s');
                            $substatusarraymain[] = $mysinglesub;
                        }
                        $singlestatus['substatus'] =$substatusarraymain;
                        
                        $mainstatus[] = $singlestatus;
                        
                    }
                    
                    $singlebankn['status'] = $mainstatus;
                    
                    $bankdata[] = $singlebankn;
                    
                }
               
            
        }
        $loans['banks'] =$bankdata;
        
        //echo"<pre>";print_r($loans);die;
	//$loans = 	$this->setData("loandata", $loans->toArray());
		//print_r($loans);die;
		return response()->json([
            'status' => true,
            'site_url' => URL::to('/').'/',
            'data' => $loans
        ]);
		
	}
	
	protected function setData($key, $value)
    {
        array_walk_recursive($value, function (&$item, $key) {
            $item = null === $item ? '' : $item;
        });
        $this->data[$key] = $value;
        return $this->data;
    }

	
	public function createRule($rules, $fields, $request){
		
		$input = array();
		foreach($fields as $field){
			
			$name = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', $field->title));
			$input[$name] = $request->input($name);
			if($field->field_type == 'Select'){
				$option = json_decode($field->options_value);
				$option = implode(',', $option);
				if($field->field_required){
					$rules[$name] = "required|in:".$option;
				}else{
					$rules[$name] = "nullable|in:".$option;
				}
				
				
			}
			
			else if($field->field_type == 'Text'){
				if($field->field_required){
					$rules[$name] = "required";
				}else{
					$rules[$name] = "";
				}
				
			}
			
			else if($field->field_type == 'Numeric'){
				if($field->field_required){
					$rules[$name] = "required|numeric";
				}else{
					$rules[$name] = "nullable|numeric";
				}
				
			}
			
			else if($field->field_type == 'Textarea'){
				if($field->field_required){
					$rules[$name] = "required";
				}else{
					$rules[$name] = "";
				}
				
			}
			
			else if($field->field_type == 'Date'){
				if($field->field_required){
					$rules[$name] = "required|date_format:Y-m-d";
				}else{
					$rules[$name] = "nullable|date_format:Y-m-d";
				}
				
			}
			
			else if($field->field_type == 'Mobile'){
				if($field->field_required){
					$rules[$name] = "required|min:10";
				}else{
					$rules[$name] = "nullable|min:10";
				}
				
			}
			
			else if($field->field_type == 'Email'){
				if($field->field_required){
					$rules[$name] = "required|email";
				}else{
					$rules[$name] = "nullable|email";
				}
				
			}
			
			else if($field->field_type == 'Credit Card'){
				if($field->field_required){
					$rules[$name] = "required";
				}else{
					$rules[$name] = "";
				}
				
			}
			
			else if($field->field_type == 'Aadhar'){
				if($field->field_required){
					$rules[$name] = "required";
				}else{
					$rules[$name] = "";
				}
				
			}
			
			else if($field->field_type == 'Pan'){
				if($field->field_required){
					$rules[$name] = "required";
				}else{
					$rules[$name] = "";
				}
				
			}
			
			
		}
		$data['rules'] = $rules;
		$data['fields'] = $input;
		return $data;
		
	}
    
    function loandocumentlist(Request $request){
		
		$validator = Validator::make($request->all(), [
            'loan_id' => 'required|exists:loan,id',
        ]);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid loan id'
			]);
        }
        
        $id = $request->loan_id;
        $loan = Loan::with('assign_detail')
		->with('loan_type_detail')->where('id', $id)->first();
		$loan_type_id = $loan->loan_type_id;
		$loanType = LoanType::where('id', $loan->loan_type_id)->first();
		
		$loanfields = json_decode($loanType->loan_fields);
		$document_group = json_decode($loanType->document_group);
		
        $fielddataarray = json_decode($loan->fields);
        if(!empty($fielddataarray)){
            if(isset($fielddataarray->Type_Of_Employment)){
                $Type_Of_Employment = $fielddataarray->Type_Of_Employment;
                $loangroupbyuserdata = LoanUserDocumentType::where(['loan_type_id'=>$loan->loan_type_id,'user_type'=>$Type_Of_Employment])->get();
                if($loangroupbyuserdata->count()>0){
                    $document_group = json_decode($loangroupbyuserdata[0]->loan_fields);
                } 
            }
        }
       
		$alldocset = $this->get_doc_list_array($document_group,$id);
        //print_r($alldocset);die;
        return response()->json([
            'status' => true,
            'site_url' => URL::to('/').'/',
            'data' => $alldocset
        ]);
		
        
        
    }

	
	public function get_doc_list_array($loanfields,$loanid=0,$folder=0){

		$docarrayall = array();
		//print_r( $loanfields);die;
		$docgroup = DocumentGroup::whereIn('id', $loanfields)->where('status',1)->get();
		$docgroupnew = array();
		foreach($docgroup as $singledocgroup){
           
			$mainarray = array();
			$mainarray['id'] = $singledocgroup->id;
			$mainarray['title'] = $singledocgroup->title;
            $docsetlistarr = array();
			$docsetlistarr = json_decode($singledocgroup->document_fields);
            
            $loanpath = str_replace('public/','',url('storage/loandocument'));
            $currentloanpath = $loanpath.'/'.$loanid.'/'.str_replace(' ','_',$singledocgroup->title);
            $docarray = DocumentField::whereIn('id', $docsetlistarr)->get();
            
			$loangroupname = $singledocgroup->title;
			$alldoc = array();
			foreach($docarray as $docfild){
               
				$singlearray = array();
				$singlearray['id'] = $docfild->id;
				$singlearray['title'] = $docfild->title;
				$singlearray['field_required'] = $docfild->field_required;
				$singlearray['no_of_document'] = $docfild->no_of_document;
                $singlearray['status'] =0;
                $singlearray['status_name'] ="Pending";
                 $docexits = LoanDocumentDtatus::where(['group_id'=>$singledocgroup->id,'doc_id'=>$docfild->id,'loan_id'=>$loanid])->first();
                if(isset($docexits->id)){
                    $singlearray['status'] =$docexits->status;
                     $singlearray['status_name'] =config('global.DOCSTATUS')[$docexits->status];
                }
				$singlearray['path'] = $currentloanpath."/".str_replace(' ','_',$docfild->title);
                $uploaddoc = array();
				$alluploaddoc = LoanDocument::where(['document_id'=>$docfild->id, 'loan_id'=>$loanid])->get();
                if($alluploaddoc->count()>0){
                    foreach($alluploaddoc as $singledocupdd){
                        $singleupdoc  =array();
                        $singleupdoc['id'] = $singledocupdd->id;
                        $singleupdoc['document_type'] = $singledocupdd->document_type;
                        $singleupdoc['document_file'] = $singledocupdd->document_file;
                        $singleupdoc['created_at'] = $singledocupdd->created_at->format('Y-m-d H:i:s');
                        $uploaddoc[] = $singleupdoc;
                    }
                    
                }
				$singlearray['data'] = $uploaddoc;
				$alldoc[] = $singlearray;
			}
			//$singledoc['data'] = $alldoc;
			
	
		$mainarray['data'] = $alldoc;
		$docarrayall[] = $mainarray;

	}
        
        /**********Pending Document**************/
            $mainarray = array();
			$mainarray['id'] = 999999;
			$mainarray['title'] = 'Pending Docuemt';
            $docsetlistarr = array();
			
            
            $loanpath = str_replace('public/','',url('storage/loandocument'));
            $currentloanpath = $loanpath.'/'.$loanid.'/pendingdoc';
            $pendingdoc = LoanPendingDocument::with('documentdetial')->where('loan_id', $loanid)->get();
            //echo "<pre>";print_R($pendingdoc->toArray());die;
			$loangroupname = $singledocgroup->title;
			$alldoc = array();
			foreach($pendingdoc as $docfild){
				$singlearray = array();
				$singlearray['id'] = $docfild->id;
				$singlearray['title'] = $docfild->documentdetial->title;
				$singlearray['field_required'] = $docfild->documentdetial->field_required;
				$singlearray['no_of_document'] = $docfild->documentdetial->no_of_document;
                $singlearray['status'] =0;
                 $docexits = LoanDocumentDtatus::where(['group_id'=>999999,'doc_id'=>$docfild->id,'loan_id'=>$loanid])->first();
                if(isset($docexits->id)){
                    $singlearray['status'] =$docexits->status;
                }
				$singlearray['path'] = $currentloanpath."/".str_replace(' ','_',$docfild->documentdetial->title);
                $uploaddoc = array();
				$alluploaddoc = LoanUploadPendingDocument::where(['document_id'=>$docfild->id, 'loan_id'=>$loanid])->get();
                if($alluploaddoc->count()>0){
                    foreach($alluploaddoc as $singledocupdd){
                        $singleupdoc  =array();
                        $singleupdoc['id'] = $singledocupdd->id;
                        $singleupdoc['document_type'] = $singledocupdd->document_type;
                        $singleupdoc['document_file'] = $singledocupdd->document_file;
                        $singleupdoc['created_at'] = $singledocupdd->created_at->format('Y-m-d H:i:s');
                        $uploaddoc[] = $singleupdoc;
                    }
                    
                }
				$singlearray['data'] = $uploaddoc;
             
				$alldoc[] = $singlearray;
			}
        $mainarray['data'] = $alldoc;
        $docarrayall[] = $mainarray;
        //print_R($docarrayall);die;
		return $docarrayall;
	}

    
    
    public function uploaddoc(Request $request){
		
//        echo "<pre>";
//        print_r($request->all());die;
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required|exists:loan,id',
            'group_id' => 'required',
            'document_id' => 'required',
        ]);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Please send loan id, group id, document id'
			]);
        }
        
        
        $loan_bank_id = $request->loan_id;
		$loanpath = storage_path('loandocument');
		$currentloanpath = $loanpath.'/'.$loan_bank_id;
		if(!is_dir($loanpath)){
			mkdir($loanpath, 0777);
			mkdir($currentloanpath, 0777);
		}
		if(!is_dir($currentloanpath)){
			mkdir($currentloanpath, 0777);
		}
		$singledoc = $request->docarr;
		$group = $request->group_id;
		$document_id = $request->document_id;
    
		if(!empty($singledoc) && count($singledoc)>0){
            
			
			
               //print_r($group);die;
                if($group !=999999){
                    $docarray = DocumentGroup::where('id', $group)->first();
                    $loangroupname = $docarray->title;
                    $loangrouppath = $currentloanpath.'/'.str_replace(' ','_',$loangroupname);
                    if(!is_dir($loangrouppath)){
                        mkdir($loangrouppath, 0777);
                    }
                    $docname = DocumentField::where('id', $document_id)->first();
                    $docsetpath = $loangrouppath.'/'.str_replace(' ','_',$docname->title);
                    if(!is_dir($docsetpath)){
                        mkdir($docsetpath, 0777);
                    }

                    foreach($singledoc as $key=>$singlefileup){
                            $file = $singlefileup;
                            $destinationPath = $docsetpath; 
                            $extension = $file->getClientOriginalExtension();
                            $fileName = str_replace(' ','_',$docname->title).'_'.time().'_'.$key.'.'.$extension;
                            $file->move($destinationPath, $fileName);
                            $loandocstore = new LoanDocument();
                            $loandocstore->loan_id = $loan_bank_id;
                            $loandocstore->document_id = $document_id;
                            $loandocstore->document_type = $extension;
                            $loandocstore->document_file = $fileName;
                            $loandocstore->save();
                       
                    }  
                
                }
                else
                {
           
                        $loangrouppath = $currentloanpath.'/pendingdoc';
                        if(!is_dir($loangrouppath)){
                            mkdir($loangrouppath, 0777);
                        }
                        $pendingdoc = LoanPendingDocument::with('documentdetial')->where('id', $document_id)->first();
                        $docsetpath = $loangrouppath.'/'.str_replace(' ','_',$pendingdoc->documentdetial->title);
                        if(!is_dir($docsetpath)){
                            mkdir($docsetpath, 0777);
                        }
                       
                            foreach($singledoc as $singlefileup){
                                $file = $singlefileup;
                                $destinationPath = $docsetpath; 
                                $extension = $file->getClientOriginalExtension();
                                $fileName = str_replace(' ','_',$pendingdoc->documentdetial->title).'_'.time().'_'.$key.'.'.$extension;
                                $file->move($destinationPath, $fileName);
                                $loandocstore = new LoanUploadPendingDocument();
                                $loandocstore->loan_id = $loan_bank_id;
                                $loandocstore->document_id = $pendingdoc->id;
                                $loandocstore->document_type = $extension;
                                $loandocstore->document_file = $fileName;
                                $loandocstore->save();
                            }
                       
                }
				

			return response()->json([
				'status' => true,
                'site_url' => URL::to('/').'/',
				'msg' => 'Document Successfully uploaded.'
				]);

		}else{
			return response()->json([
				'status' => false,
				'msg' => 'Please select document to upload'
				]);
		}
	}
    
    
    
	/*public function uploaddoc(Request $request){
		

        $validator = Validator::make($request->all(), [
            'loan_id' => 'required|exists:loan,id',
        ]);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => $validator->errors()
			]);
        }
        
        
        $loan_bank_id = $request->loan_id;
		$loanpath = storage_path('loandocument');
		$currentloanpath = $loanpath.'/'.$loan_bank_id;
		if(!is_dir($loanpath)){
			mkdir($loanpath, 0777);
			mkdir($currentloanpath, 0777);
		}
		if(!is_dir($currentloanpath)){
			mkdir($currentloanpath, 0777);
		}
		$alldoc = $request->docarr;
    
		if(!empty($alldoc) && count($alldoc)>0){
            
			
			foreach($alldoc as $group=>$singledoc){
               //print_r($group);die;
                if($group !=999999){
                    $docarray = DocumentGroup::where('id', $group)->first();
                    $loangroupname = $docarray->title;
                    $loangrouppath = $currentloanpath.'/'.str_replace(' ','_',$loangroupname);
                    if(!is_dir($loangrouppath)){
                        mkdir($loangrouppath, 0777);
                    }

                    foreach($singledoc as $docid=>$docfilearr){
                        $docname = DocumentField::where('id', $docid)->first();
                        $docsetpath = $loangrouppath.'/'.str_replace(' ','_',$docname->title);
                        if(!is_dir($docsetpath)){
                            mkdir($docsetpath, 0777);
                        }
                        foreach($docfilearr as $key=>$singlefileup){
                            $file = $singlefileup;
                            $destinationPath = $docsetpath; 
                            $extension = $file->getClientOriginalExtension();
                            $fileName = str_replace(' ','_',$docname->title).'_'.time().'_'.$key.'.'.$extension;
                            $file->move($destinationPath, $fileName);
                            $loandocstore = new LoanDocument();
                            $loandocstore->loan_id = $loan_bank_id;
                            $loandocstore->document_id = $docid;
                            $loandocstore->document_type = $extension;
                            $loandocstore->document_file = $fileName;
                            $loandocstore->save();
                        }
                    }  
                }else{
                        //$docarray = DocumentGroup::where('id', $group)->first();
                        //$loangroupname = $docarray->title;
                        $loangrouppath = $currentloanpath.'/pendingdoc';
                        if(!is_dir($loangrouppath)){
                            mkdir($loangrouppath, 0777);
                        }
                        
                        foreach($singledoc as $docid=>$docfilearr){
                            //$docname = DocumentField::where('id', $docid)->first();
                            $pendingdoc = LoanPendingDocument::with('documentdetial')->where('id', $docid)->first();
                            //print_r($pendingdoc->toArray());die;
                            $docsetpath = $loangrouppath.'/'.str_replace(' ','_',$pendingdoc->documentdetial->title);
                            if(!is_dir($docsetpath)){
                                mkdir($docsetpath, 0777);
                            }
                            foreach($docfilearr as $key=>$singlefileup){
                                $file = $singlefileup;
                                $destinationPath = $docsetpath; 
                                $extension = $file->getClientOriginalExtension();
                                $fileName = str_replace(' ','_',$pendingdoc->documentdetial->title).'_'.time().'_'.$key.'.'.$extension;
                                $file->move($destinationPath, $fileName);
                                $loandocstore = new LoanUploadPendingDocument();
                                $loandocstore->loan_id = $loan_bank_id;
                                $loandocstore->document_id = $pendingdoc->id;
                                $loandocstore->document_type = $extension;
                                $loandocstore->document_file = $fileName;
                                $loandocstore->save();
                            }
                        }  
                    
                    
                }
				

				

			}
           
			return response()->json([
				'status' => true,
                'site_url' => URL::to('/').'/',
				'msg' => 'Document Successfully uploaded.'
				]);

		}else{
			return response()->json([
				'status' => false,
				'msg' => 'Please select document to upload'
				]);
		}
	}
    */
    
	   
    function addreferalandearn(Request $request){
		
		$validator = Validator::make($request->all(), [
            'borrower_id' => 'required|exists:borrower,id',
            'emp_type' => 'required',
            'loan_type' => 'required',
            'amount' => 'required',
            'full_name' => 'required',
            'mobile_no' => 'required|min:10',
            'email' => 'required|email'
            
        ]);
        
        if(!isset($request->borrower_id) || empty($request->borrower_id)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid borrower'
			]);
        }
        if(!isset($request->emp_type) || empty($request->emp_type)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid employee type'
			]);
        }
        if(!isset($request->loan_type) || empty($request->loan_type)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid loan type'
			]);
        }
        if(!isset($request->amount) || empty($request->amount)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid amount'
			]);
        }
        
         if(!isset($request->full_name) || empty($request->full_name)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid Full Name'
			]);
        }
         if(!isset($request->mobile_no) || empty($request->mobile_no)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid Mobile No.'
			]);
        }
         if(!isset($request->email) || empty($request->email)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid email.'
			]);
        }

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid data'
			]);
        }
        
        $refearn = new ReferalEarn();
        $refearn->borrower_id = $request->borrower_id;
        $refearn->emp_type = $request->emp_type;
        $refearn->loan_type = $request->loan_type;
        $refearn->amount = $request->amount;
        $refearn->full_name = $request->full_name;
        $refearn->mobile_no = $request->mobile_no;
        $refearn->email = $request->email;
        $refearn->status = 1;
        $refearn->created_by = $request->borrower_id;
        $refearn->updated_by = $request->borrower_id;
        $refearn->save();
        return response()->json([
            'status' => true,
            'msg' => 'Referal created successfully'
			]);
        
    }
    
    public function emilist(Request $request){
		
		$validator = Validator::make($request->all(), [
            'borrower_id' => 'required|exists:borrower,id',
        ]);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid borrower'
			]);
        }
        $REMINDMEARR = config('global.REMINDMEARR');
        $EMITYPE = config('global.EMITYPE');
        $EMISTATUS = config('global.EMISTATUS');
        
    $emilist = Emi::with('borrower_detail')
            ->with('emi_detail')
            ->where('borrower_id', $request->borrower_id)->get();
        $emiarray = array();
        foreach($emilist as $singlearr){
            $singlearraynew = array();
            $singlearraynew['id'] = $singlearr->id;
            $singlearraynew['emi_type'] = $singlearr->emi_detail->name;
            $singlearraynew['title'] = $singlearr->title;
            $singlearraynew['details'] = $singlearr->details;
            $singlearraynew['amount'] = $singlearr->amount;
            $singlearraynew['emi_date'] = date('d M Y',$singlearr->emi_date);
            $singlearraynew['type'] = $EMITYPE[$singlearr->type];
            $singlearraynew['remind_type'] = $REMINDMEARR[$singlearr->remind_type];
            $singlearraynew['repeat_type'] = $EMISTATUS[$singlearr->repeat_type];
             $emiarray[] = $singlearraynew;
        }
        return response()->json([
            'status' => true,
            'site_url' => URL::to('/').'/',
            'data' => $emiarray
        ]);
        
    }
    
    
    public function addnewemi(Request $request){
		
		$validator = Validator::make($request->all(), [
            'borrower_id' => 'required|exists:borrower,id',
            'emi_type' => 'required',
            'title' => 'required',
            'details' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'remind_type' => 'required',
            'repeat_type' => 'required',
            'emi_date' => 'required',
             
        ]);

         if(!isset($request->borrower_id) || empty($request->borrower_id)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid borrower'
			]);
        }
        if(!isset($request->emi_type) || empty($request->emi_type)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid Emi type'
			]);
        }
        if(!isset($request->title) || empty($request->title)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid title'
			]);
        }
        if(!isset($request->details) || empty($request->details)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid details'
			]);
        }
        
         if(!isset($request->type) || empty($request->type)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid type'
			]);
        }
         if(!isset($request->remind_type) || empty($request->remind_type)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid Remind Type.'
			]);
        }
         if(!isset($request->amount) || empty($request->amount)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid amount.'
			]);
        }
         if(!isset($request->repeat_type) || empty($request->repeat_type)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid Repeat Type.'
			]);
        }
         if(!isset($request->emi_date) || empty($request->emi_date)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid Emi date.'
			]);
        }

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid data'
			]);
        }
      
        
        $emi = new Emi();
        $emi->borrower_id = $request->borrower_id;
        $emi->emi_type = $request->emi_type;
        $emi->title = $request->title;
        $emi->details = $request->details;
        $emi->amount = $request->amount;
        $emi->emi_date = strtotime($request->emi_date);
        $emi->time = strtotime($request->emi_date);
        $emi->type = $request->type;
        $emi->remind_type = $request->remind_type;
        $emi->repeat_type = $request->repeat_type;
        $emi->status = 1;
        $emi->created_by = $request->borrower_id;
        $emi->updated_by = $request->borrower_id;
        $emi->save();
        return response()->json([
            'status' => true,
            'msg' => 'Emi created successfully'
			]);
	
    }
	
    
    function datalist(){
        
        $maindata = array();
        $masterdata = MasterValue::where(['master_id'=>1,'dstatus'=>0,'status'=>0])->orderby('name','asc')->get();
        $emiforall = array();
        foreach($masterdata as $singledata){
            $emiaalnew = array();
            $emiaalnew['id'] = $singledata->id;
            $emiaalnew['name'] = $singledata->name;
            $emiforall[] = $emiaalnew;
        }
        //$emiforall['emiforlist'] = $emiforall;
       
        $EMPTYPEARRAY = config('global.EMPTYPEARRAY');
        $REMINDMEARR = config('global.REMINDMEARR');
        $EMITYPE = config('global.EMITYPE');
        $EMISTATUS = config('global.EMISTATUS');
        $LOANTYPE = config('global.LOANTYPE');
        $emptypeall = array();
        foreach($EMPTYPEARRAY as $key=>$value){
            $singleemtypess = array();
            $singleemtypess['id'] = $key;
            $singleemtypess['name'] = $value;
            $emptypeall[] = $singleemtypess;
        }
       
        
        $remindertypeallarray = array();
        foreach($REMINDMEARR as $key=>$value){
            $retypesingle = array();
            $retypesingle['id'] = $key;
            $retypesingle['name'] = $value;
            $remindertypeallarray[] = $retypesingle;
        }
       
        
        
        $emitypeallarray = array();
        foreach($EMITYPE as $key=>$value){
            $retypesingle = array();
            $retypesingle['id'] = $key;
            $retypesingle['name'] = $value;
            $emitypeallarray[] = $retypesingle;
        }
        
      
        $emistatusallarray = array();
        foreach($EMISTATUS as $key=>$value){
            $retypesingle = array();
            $retypesingle['id'] = $key;
            $retypesingle['name'] = $value;
            $emistatusallarray[] = $retypesingle;
        }
        
        
        $loantypeallarray = array();
        foreach($LOANTYPE as $key=>$value){
            $retypesingle = array();
            $retypesingle['id'] = $key;
            $retypesingle['name'] = $value;
            $loantypeallarray[] = $retypesingle;
        }
       
       
        
        
         $datareturnnew[]= array(
            'emiforlist'=>$emiforall,
            'employeementtype'=>$emptypeall,
            'remindertypearray'=>$remindertypeallarray,
            'emitypearray'=>$emitypeallarray,
            'emistatus'=>$emistatusallarray,
            'loan_type'=>$loantypeallarray,
        );
        //print_r($datareturnnew);die;
        return response()->json([
            'status' => true,
            'site_url' => URL::to('/').'/',
            'data' => $datareturnnew
        ]);
        
    }
	
    
    function user_homepage(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        
        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid user id'
			]);
        }
        
        $loans = LoanBank::with(['loan_detail' => function($q){
				$q->with('borrower_detail');
                $q->with('loan_type_detail');
        }])
       
		->with(['loan_bank_status_detail' => function($d){
				$d->with('loan_status_detail');
				$d->with('loan_sub_status_detail');
				
        }])
		->where('assign_to', $request->user_id)
		->latest()->get();
        //print_r($loans->toArray());die;
        $dataretun = array();
        foreach($loans as $singleloan){
            $singleloanarr = array();
            $singleloanarr['id'] = $singleloan->id;
            $singleloanarr['bank_id'] = $singleloan->bank_id;
            $singleloanarr['loan_id'] = $singleloan->loan_id;
            $singleloanarr['bank_name'] = $singleloan->bank_detail->title;
            $singleloanarr['loantype_id'] = $singleloan->loan_detail->loan_type_detail->id;
            $singleloanarr['loantype'] = $singleloan->loan_detail->loan_type_detail->title;
            $singleloanarr['borrower_id'] = $singleloan->loan_detail->borrower_detail->id;
            $singleloanarr['borrower_name'] = $singleloan->loan_detail->borrower_detail->name;
            $singleloanarr['borrower_email'] = $singleloan->loan_detail->borrower_detail->email;
            $singleloanarr['borrower_mobile_no'] = $singleloan->loan_detail->borrower_detail->mobile_no;
            $singleloanarr['status'] ="";
            if(isset($singleloan->loan_bank_status_detail->loan_status_detail->title)){
                $singleloanarr['status'] = $singleloan->loan_bank_status_detail->loan_status_detail->title;
            }
            $singleloanarr['substatus'] ="";
            if(isset($singleloan->loan_bank_status_detail->loan_sub_status_detail->title)){
                $singleloanarr['substatus'] = $singleloan->loan_bank_status_detail->loan_sub_status_detail->title;
            }
           
            $singleloanarr['created_at'] = $singleloan->created_at->format('Y-m-d H:i:s');
           $dataretun[] = $singleloanarr;
        }
        
		//print_r($loans->toArray());die;
		return response()->json([
            'status' => true,
            'site_url' => URL::to('/').'/',
            'data' => $dataretun,
            'totalloans' => count($dataretun)
        ]);
        
        
    }
    
    
    function todotask(Request $request){
        
//        $data = $this->createRule($rules, $fields, $request);
        //print_R(json_decode($request->task,true));die;
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required',
            'description' => 'nullable',
            'date' => 'required',
        ]);

        if(!isset($request->user_id) || empty($request->user_id)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid user.'
			]);
        }
         if(!isset($request->title) || empty($request->title)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid title.'
			]);
        }
         if(!isset($request->date) || empty($request->date)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid date.'
			]);
        }

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid data'
			]);
        }
        
        $titles = $request->title;
        $descriptions = $request->description;
        $dates = $request->date;
        $user_id = $request->user_id;
        if(count($titles)>0){
            foreach($titles as $key=>$singletak){
                $taskdata = new TodoTask();
                $taskdata->user_id = $user_id;
                $taskdata->title = $titles[$key];
                $taskdata->description = $descriptions[$key];
                $taskdata->nextflowupdate = strtotime($dates[$key]);
                $taskdata->created_by = $user_id;
                $taskdata->updated_by = $user_id;
                $taskdata->save();
                
            }
            return response()->json([
                'status' => true,
                'site_url' => URL::to('/').'/',
                'message' => 'Task successfully created'
            ]);
            
            
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Please send task data'
            ]); 
        }
        
        
        
    }
    
	
    function todotaskupdate(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required',
            'taskid' => 'required',
            'status' => 'required',
            'description' => 'nullable',
            'date' => 'required',
        ]);

         if(!isset($request->user_id) || empty($request->user_id)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid user.'
			]);
        }
         if(!isset($request->title) || empty($request->title)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid title.'
			]);
        }
        if(!isset($request->taskid) || empty($request->taskid)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid task to update.'
			]);
        }
        
         if(!isset($request->status) || empty($request->status)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid status to update.'
			]);
        }
         if(!isset($request->date) || empty($request->date)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid date to update.'
			]);
        }
        
         if(!isset($request->date) || empty($request->date)){
            return response()->json([
			'status' => false,
			'errors' => 'Invalid date.'
			]);
        }

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid data'
			]);
        }
       
        $titles = $request->title;
        $descriptions = $request->description;
        $dates = $request->date;
        $status = $request->status;
        $taskid = $request->taskid;
        $user_id = $request->user_id;
        if(count($titles)>0){
            foreach($taskid as $key=>$singletak){
                $taskdata = TodoTask::find($singletak);
                $taskdata->user_id = $user_id;
                $taskdata->title = $titles[$key];
                $taskdata->description = $descriptions[$key];
                $taskdata->nextflowupdate = strtotime($dates[$key]);
                $taskdata->status = $status[$key];
                $taskdata->updated_by = $user_id;
                $taskdata->save();
                
            }
            return response()->json([
                'status' => true,
                'site_url' => URL::to('/').'/',
                'message' => 'Task successfully updated'
            ]);
            
            
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Please send task data'
            ]); 
        }
        
        
        
    }
    
    function todotasklist(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required',
        ]);
        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Please send user and date'
			]);
        }
        $TODOLISTSTATUS = config('global.TODOLISTSTATUS');
         $startdate = strtotime(date('Y-m-d 00:00:00',strtotime($request->date)));
        $datesting = strtotime(date('Y-m-d 23:59:59',strtotime($request->date)));
        $taskdata = TodoTask::where('nextflowupdate','<=',$datesting)->where('nextflowupdate','>=',$startdate)->where('user_id',$request->user_id)->orderBy('status','asc')->get();
        $tasklistall = array();
        foreach($taskdata as $singletasklist){
            $singletasknew = array();
            $singletasknew['id'] = $singletasklist->id;
            $singletasknew['title'] = $singletasklist->title;
            $singletasknew['description'] = $singletasklist->description;
            $singletasknew['nextflowupdate'] = date('d M Y',$singletasklist->nextflowupdate);
            $singletasknew['status'] = $singletasklist->status;
            $singletasknew['status_name'] = $TODOLISTSTATUS[$singletasklist->status];
            $singletasknew['created_at'] = $singletasklist->created_at->format('Y-m-d H:i:s');
            $tasklistall[] = $singletasknew;
        }
        
        
        $datesting = strtotime(date('Y-m-d 00:00:00',strtotime($request->date)));
        $taskdata = TodoTask::where('nextflowupdate','<',$datesting)->where('status',0)->where('user_id',$request->user_id)->orderBy('status','asc')->get();
        $tasklistallovercome = array();
        foreach($taskdata as $singletasklist){
            $singletasknew = array();
            $singletasknew['id'] = $singletasklist->id;
            $singletasknew['title'] = $singletasklist->title;
            $singletasknew['description'] = $singletasklist->description;
            $singletasknew['nextflowupdate'] = date('d M Y',$singletasklist->nextflowupdate);
            $singletasknew['status'] = $singletasklist->status;
            $singletasknew['status_name'] = $TODOLISTSTATUS[$singletasklist->status];
            $singletasknew['created_at'] = $singletasklist->created_at->format('Y-m-d H:i:s');
            $tasklistallovercome[] = $singletasknew;
        }
        
        
        
        $datareturn = array(
            'todaytask'=>$tasklistall,
            'overduetask'=>$tasklistallovercome,
        );
        
        $statusall = array();
        foreach($TODOLISTSTATUS as $key=>$singlest){
            $singlestn = array();
            $singlestn['id'] = $key;
            $singlestn['name'] = $singlest;
            $statusall[] = $singlestn;
        }
        
       return response()->json([
            'status' => true,
            'site_url' => URL::to('/').'/',
            'data' => $datareturn,
            'statusarr' => $statusall,
        ]); 
        
    }
    
    function referalandearnlist(Request $request){
       $validator = Validator::make($request->all(), [
            'borrower_id' => 'required|exists:borrower,id',
        ]);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Invalid borrower'
			]);
        }
        
        $referalandearnarr = ReferalEarn::where('borrower_id',$request->borrower_id)->get();
        $returndata = array();
         $EMPTYPE = config('global.EMPTYPEARRAY');
        //print_r($EMITYPE);
        $LOANTYPE = config('global.LOANTYPE');
        foreach($referalandearnarr as $singlearr){
            
            //print_r($singlearr);die;
            $singlearrne = array();
             $singlearrne['id'] = $singlearr->id;
             $singlearrne['emp_type'] = $EMPTYPE[$singlearr->emp_type];
             $singlearrne['loan_type'] = $LOANTYPE[$singlearr->loan_type];
             $singlearrne['amount'] = $singlearr->amount;
             $singlearrne['full_name'] = $singlearr->full_name;
             $singlearrne['mobile_no'] = $singlearr->mobile_no;
             $singlearrne['email'] = $singlearr->email;
             $singlearrne['created_at'] = $singlearr->created_at->format('Y-m-d H:i:s');
             $returndata[] = $singlearrne;
        }
 
         return response()->json([
            'status' => true,
            'site_url' => URL::to('/').'/',
            'data' => $returndata
        ]);
        
        
    }
    
     public function loanstatus()
    {
		$status = LoanStatus::with('childstatus')->where(['parent_id'=>0])->get();
        return response()->json([
            'status' => true,
			'base_url' => URL::to('/').'/',
            'data' => $status
        ]);
    }
	
    
    function update_loan_bank_status(Request $request){
       $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'bank_id' => 'required|exists:loan_bank,id',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Please send user, bank and status'
			]);
        }
        
        
        if(!empty($request->sub_status)){
            $bank_loan = LoanBankStatus::where('loan_bank_id', $request->bank_id)->where('loan_sub_status', $request->sub_status)->first();

            if(isset($bank_loan->loan_bank_id)){

                return response()->json([
                'status' => false,
                'errors' => "Sub Status Already added"
                ]);

            }
        }else{
            //echo 1111;die;
             $bank_loan = LoanBankStatus::where('loan_bank_id', $request->bank_id)->where('loan_status', $request->status)->first();
           
            if(isset($bank_loan->loan_bank_id)){
                return response()->json([
                'status' => false,
                'errors' => "Status Already added"
                ]);

            } 
        }
        $loan = new LoanBankStatus();
		$loan->loan_bank_id = $request->bank_id;
		$loan->loan_status = $request->status;
		$loan->loan_sub_status = $request->sub_status;
		$loan->internal_comment = $request->internal_comment;
		$loan->external_comment = $request->external_comment;
		$loan->ps_loan_date = date('Y-m-d');
		$loan->updated_by = $request->user_id;
		$loan->save(); 
		
		return response()->json([
            'status' => true,
            'msg' => 'Status Updated'
			]);
			 
        
        
        
        
    }
	
 public function bloglist(){
        $data = Blog::with('category_detail')->where(['status'=>1,'dstatus'=>0])->latest()->get();
        $bloglist = array();
        foreach($data as $singledata){
            $singlbbb = array();
            $singlbbb['id'] = $singledata->id;
            $singlbbb['title'] = $singledata->title;
            $singlbbb['short_desc'] = $singledata->short_desc;
            $singlbbb['image'] = $singledata->image;
            $singlbbb['content'] = $singledata->content;
            $singlbbb['category'] = $singledata->category_detail->name;
            $singlbbb['created_at'] = $singledata->created_at->format('Y-m-d H:i:s');
            $bloglist[] = $singlbbb;
            
        }
        return response()->json([
            'status' => true,
			'base_url' => URL::to('/').'/',
            'data' => $bloglist
        ]);
    }
    
    public function contact(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
		   return response()->json([
			'status' => false,
			'errors' => 'Please send name, phone,email and message'
			]);
        }
        
        
        $post = new Contact();
        $post->name = $request->name;
        $post->phone = $request->phone;
        $post->email = $request->email;
        $post->message = $request->message;
        $post->status = 1;
        $post->save();
        
        return response()->json([
            'status' => true,
            'msg' => 'Contact Created'
			]);
    }
	
	
    
	
	
}
