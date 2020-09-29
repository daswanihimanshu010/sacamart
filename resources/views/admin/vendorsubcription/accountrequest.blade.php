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
            <h1>  {{ trans('Account Request') }} <small>{{ trans('Account Request') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> {{ trans('Account Request') }}</li>
            </ol>
        </section>

        <!--  content -->
        <section class="content">
            <!-- Info boxes -->

            <!-- /.row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <div class="col-lg-6 form-inline" id="contact-form">
                               
                                <div class="col-lg-4 form-inline" id="contact-form12"></div>
                            </div>
                           <!-- <div class="box-tools pull-right">
                                
                                <a data-toggle="tooltip" data-placement="bottom"  title="category_add" id="addvendorscat" type="button" style="display:inline-block; width: auto; margin-top: 0;" class="btn btn-block btn-primary">{{ trans('labels.AddNew') }}</a>
                            </div>-->
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                           
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box box-info">
                                        <!-- form start -->
                                        <div class="box-body">
                                                
                                            {!! Form::open(array('url' =>'admin/managements/vendoraccountrequest', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}
                                            @csrf 
                                             <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Account Deactive Request') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="account_inactive_status" class="form-control">
                                                        <option value="0" @if($vendors->account_inactive_status==0) {{'Selected'}} @endif>Active</option>
                                                        <option value="1" @if($vendors->account_inactive_status==1) {{'Selected'}} @endif>In Active</option>
                                                    </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('Account Request') }}</span>
                                                    <span class="help-block hidden">{{ trans('Account Request Required') }}</span>
                                                </div>
                                            </div>

                                            <!-- /.box-body -->
                                            <div class="box-footer text-right">
                                                <div class="col-sm-offset-2 col-md-offset-3 col-sm-10 col-md-4">
                                                    <button type="submit" class="btn btn-primary">{{ trans('labels.Submit') }}</button>
                                                    <a href="{{ URL::to('admin/languages/display')}}" type="button" class="btn btn-default">{{ trans('labels.back') }}</a>
                                                </div>
                                            </div>
                                            <!-- /.box-footer -->
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px;">
                                <div class="col-xs-12">
                                        <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                           <?php // dd($vendors); ?>
                                                <tr>
                                                    <td>
                                                        {{ $vendors->id}}
                                                    </td>
                                                    <td>{{ ($vendors->account_inactive_status == 1) ? 'Request Pending' :'Request Approve' }}</td>
                                                </tr>
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
            
           
        </section>
        <!-- /.content -->
    </div>
@endsection
