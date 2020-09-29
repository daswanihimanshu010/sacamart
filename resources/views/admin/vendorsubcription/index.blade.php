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
            <h1>  {{ trans('Subcription') }} <small>{{ trans('Subcription') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> {{ trans('Subcription') }}</li>
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
                           

                            <div class="row" style="margin-top:20px;">
                                <div class="col-xs-12">
                                        <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                            <th>Duration</th>
                                            <th>Description</th>
                                            <th>Purchase</th>
                                            <th>Expired</th>
                                           
                                        </tr>
                                        </thead>
                                        <tbody>
                                      <?php //echo "<pre>"; print_r($paymentrequests);die;?>
                                            @foreach($paymentrequests as $key=>$location_list)
                                                <tr>
                                                    <td>
                                                        {{ $location_list->id}}
                                                    </td>
                                                      
                                                    <td>{{ $location_list->name }}</td>
                                                    <td>{{ $location_list->price }}</td>
                                                    <td>{{ $location_list->package_time }} Month</td>
                                                     <td>{{ $location_list->package_desc }}</td>
                                                    <td>{{ date('d-M-Y h:i a', strtotime($location_list->purchase_date)) }}</td>
                                                    <td>{{ date('d-M-Y h:i a', $location_list->expiry_date) }}</td>
                                                   
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
            
           
        </section>
        <!-- /.content -->
    </div>
@endsection
