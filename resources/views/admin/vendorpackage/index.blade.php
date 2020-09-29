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
            <h1>  {{ trans('Package') }} <small>{{ trans('Package') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> {{ trans('Package') }}</li>
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
                                  @if(!empty($packagelist))  
                                  @foreach($packagelist as $key => $value)
                                  <div class="col-md-3 col-sm-6 col-xs-12">
                                      <div class="info-box">
                                        <span class="info-box-icon"><img src="{{ asset('images/'.$value->image) }}" width="100" height="50" alt=""></span>

                                        <div class="info-box-content">
                                          <span class="info-box-text">Packahe Name</span>
                                          <span class="info-box-number">{{ $value->package_name }}</span>
                                        </div>
                                        <div class="info-box-content">
                                          <span class="info-box-text">Package Time</span>
                                          <span class="info-box-number">{{ $value->package_time }}</span>
                                        </div>
                                        <div class="info-box-content">
                                          <span class="info-box-text">Price</span>
                                          <span class="info-box-number">{{ $value->price }}</span>
                                        </div>
                                        <div class="info-box-content">
                                          <span class="info-box-text">No of Products</span>
                                          <span class="info-box-number">{{ $value->no_of_product }}</span>
                                        </div>
                                        <!-- /.info-box-content -->

                                      </div>
                                      <!-- /.info-box -->
                                      <!--<input type="hidden" name="pruchasedate" pruchasedate="{{ $value->id }}"  class="pruchasedate">
                                      <bttton name="subscribe" class="btn btn-primary subscribeid"  language="{{ $value->id }}">Subscribe</bttton>-->
                                 </div>
                                 @endforeach
                                 @else
                                 <p>No Package Found</p>
                                 @endif    
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
