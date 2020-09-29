@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
             <h1>  Weight Slab <small>Add Weight Slabs...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li><a href="{{ URL::to('admin/languages/display')}}"><i class="fa fa-language"></i>{{ trans('labels.languages') }}</a></li>
                <li class="active">Weight Slabs</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Info boxes -->

            <!-- /.row -->
            <div class="row">
                <div class="col-md-12">

                    <div class="box">
                      <div class="box-header">
                          <h3 class="box-title">Add Weight Slab</h3>
                      </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    @if (count($errors) > 0)
                                        @if($errors->any())
                                            <div class="alert alert-success alert-dismissible" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                {{$errors->first()}}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box box-info">
                                        <!-- form start -->
                                        <div class="box-body">

                                            {!! Form::open(array('url' =>'admin/languages/weight_slab_add', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">Shipping Zone</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="shipping_zone_id" class="form-control" required>
                                                         <option value="">--SELECT Shipping Zone--</option>
                                                        @foreach ($shipping_zone_list as $key=>$shipping_zone)
                                                         <option value="{{ $shipping_zone->shipping_zone_id }}">{{ $shipping_zone->shipping_zone_title }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">Shipping Zone Name</span>
                                                    <span class="help-block hidden">Shipping Zone required</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">Slab Name</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('slab_name', null, array('class'=>'form-control field-validate', 'id'=>'slab_name'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">Slab Name</span>
                                                    <span class="help-block hidden">Slab name required</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">Range Start</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('range_start', null, array('class'=>'form-control field-validate', 'id'=>'range_start'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">Weight Start, in kg</span>
                                                    <span class="help-block hidden">Weight start required</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">Range End</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('range_end', null, array('class'=>'form-control field-validate', 'id'=>'range_end'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">Weight End, in kg</span>
                                                    <span class="help-block hidden">Weight end required</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">Shipping Fee</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('shipping_fee', null, array('class'=>'form-control field-validate', 'id'=>'shipping_fee'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">Shipping Fee</span>
                                                    <span class="help-block hidden">Shipping fee required</span>
                                                </div>
                                            </div>

                                            

                                            <!-- /.box-body -->
                                            <div class="box-footer text-right">
                                                <div class="col-sm-offset-2 col-md-offset-3 col-sm-10 col-md-4">
                                                    <button type="submit" class="btn btn-primary">{{ trans('labels.Submit') }}</button>
                                                    <a href="{{ URL::to('admin/languages/shipping_zones_slabs')}}" type="button" class="btn btn-default">{{ trans('labels.back') }}</a>
                                                </div>
                                            </div>
                                            <!-- /.box-footer -->
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
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

            <!-- Main row -->

            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection
