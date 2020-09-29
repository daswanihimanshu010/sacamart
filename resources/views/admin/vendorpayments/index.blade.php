@extends('admin.layout')
@section('content')
<?php

  $uri_path = $_SERVER['REQUEST_URI']; 
 $uri_parts = explode('/', $uri_path);
 $request_url = end($uri_parts);

 ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>  {{ trans('Payment Requests') }} <small>{{ trans('Payment Requests') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> {{ trans('Payment Requests') }}</li>
            </ol>
        </section>

        <!--  content -->
        <section class="content">
            <!-- Info boxes -->

            <!-- /.row -->
            <div class="row">
                <div class="col-lg-4 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h3>Rs {{ !empty($totalearning) ? number_format($totalearning, 2):0 }}</h3>
                            <p>Total Amount Earned:</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    
                  </div>
                </div>

                <div class="col-lg-4 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-light-blue">
                    <div class="inner">
                      <h3>Rs {{ !empty($totaleftamount) ? number_format($totaleftamount, 2):0 }}</h3>
                            <p>Amount Left:</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    
                  </div>
                </div>

                <div class="col-lg-4 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-light-blue">
                    <div class="inner">
                      <h3>Rs {{ !empty($totalwithdrawal) ? number_format($totalwithdrawal, 2):0 }}</h3>
                            <p>Amount Withdrawn:</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    
                  </div>
                </div>
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            
                            <div class="box-tools pull-right">
                                
                                <a data-toggle="tooltip" data-placement="bottom"  title="category_add" id="addvendorscat" type="button" style="display:inline-block; width: auto; margin-top: 0;" class="btn btn-block btn-primary">{{ trans('labels.AddNew') }}</a>
                            </div>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                           

                            <div class="row" style="margin-top:20px;">
                                <div class="col-xs-12">
                                        <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Reference No</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Updated</th>
                                           
                                        </tr>
                                        </thead>
                                        <tbody>
                                      
                                            @foreach($paymentrequests as $key=>$location_list)
                                                <tr>
                                                    <td>
                                                        {{ $location_list->id}}
                                                    </td>
                                                      
                                                    <td>{{ $location_list->reference_no }}</td>
                                                    <td>{{ $location_list->amount }}</td>
                                                    <td>
                                                    @if($location_list->status == 0) {{ 'Pending' }} @elseif($location_list->status == 1) {{ 'Approve' }} @else {{ 'Reject' }} @endif
                                                    </td>
                                                    <td>{{ date('d-M-Y h:i a', strtotime($location_list->created_at)) }}</td>
                                                    <td>{{ date('d-M-Y h:i a', strtotime($location_list->updated_at)) }}</td>
                                                   
                                                </tr>
                                            @endforeach
                                      
                                        </tbody>
                                    </table>
                                    
                                       
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            
            <!-- /.row -->
            <!-- /.addvendorcatModel -->
            <div class="modal fade" id="addvendorscatModal" tabindex="-1" role="dialog" aria-labelledby="addanguagesModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="addanguagesModalLabel">Add Payment Request</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/managements/vendorpayments', 'name'=>'addlanguages', 'id'=>'addlanguages', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                        {!! Form::hidden('action',  'add', array('class'=>'form-control')) !!}
                        <input type="hidden" name="user_id" value="{{$request_url}}">
                        <div class="modal-body">
                           <div class="form-group">
                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Amount to Withdraw') }}</label>
                                <div class="col-sm-10 col-md-4">
                                    <input type="text" name="amount" class="form-control" required >
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('Withdraw') }}</span>
                                    <span class="help-block hidden">{{ trans('Enter amount to Withdraw') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.Close') }}</button>
                            <button type="submit" class="btn btn-primary" id="addlanguages">{{ trans('Save') }}</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
   

             <!-- deletelanguagesModal -->
            <div class="modal fade" id="deletevendorscatModal" tabindex="-1" role="dialog" aria-labelledby="deleteLanguagesModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteLanguagesModalLabel">Delete Vendor Category</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/languages/delete_vendors_category', 'name'=>'deletelanguages', 'id'=>'deletelanguages', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                        {!! Form::hidden('action',  'delete', array('class'=>'form-control')) !!}
                        {!! Form::hidden('id',  '', array('class'=>'form-control', 'id'=>'languages_id')) !!}
                        <div class="modal-body">
                            <p>{{ trans('Confirm Are You Sure want to delete ?') }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.Close') }}</button>
                            <button type="submit" class="btn btn-primary" id="deletelanguages">{{ trans('labels.Delete') }}</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection
