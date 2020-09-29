@extends('admin.layout')
@section('content')
<?php //dd($vendors);?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> {{ trans('Account Setting') }} <small>{{ trans('Account Setting') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li><a href="{{ URL::to('admin/languages/display')}}"><i class="fa fa-language"></i>{{ trans('Account Setting') }}</a></li>
                <li class="active">{{ trans('Account Setting') }}</li>
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
                          <h3 class="box-title">{{ trans('Account Setting') }}</h3>
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
                                                
                                            {!! Form::open(array('url' =>'admin/managements/vendoraccountsetting', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}

                                            <div class="form-group">
                                                <img style="margin-left: 330px; width: 100px;height: 100px;" src="<?php echo URL::to('/'); ?>/public/images/vendorimages/<?php echo $vendors->vendor_pic; ?>"/>
                                            </div>

                                            <div class="form-group">

                                                

                                                <input type="hidden" name="vendor_old_pic" id="vendor_old_pic" value="<?php echo $vendors->vendor_pic; ?>">
                                                <label class="col-sm-2 col-md-3 control-label" for="vendor_pic">Vendor photo</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <input name="vendor_pic" type="file" id="vendor_pic" value="<?php echo $vendors->vendor_pic; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">Business Name</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('business_name', $vendors->business_name, array('class'=>'form-control field-validate', 'id'=>'business_name'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">Business Name</span>
                                                    <span class="help-block hidden">Business Name Required</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">Business Address</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('business_address', $vendors->business_address, array('class'=>'form-control field-validate', 'id'=>'business_address'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">Business Address</span>
                                                    <span class="help-block hidden">Business Address Required</span>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Account Number') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('account_no', $vendors->account_no, array('class'=>'form-control field-validate', 'id'=>'account_no'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('Account No') }}</span>
                                                    <span class="help-block hidden">{{ trans('Account NO Required') }}</span>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Bank Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('bank_name', $vendors->bank_name, array('class'=>'form-control field-validate', 'id'=>'bank_name'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('Bank Name') }}</span>
                                                    <span class="help-block hidden">{{ trans('Bank Name Required') }}</span>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('IFSC Code') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('ifsc_code',  $vendors->ifsc_code, array('class'=>'form-control field-validate', 'id'=>'ifsc_code'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('IFSC Code') }}</span>
                                                    <span class="help-block hidden">{{ trans('IFSC Code Required') }}</span>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Branch Address') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('branch_address',  $vendors->branch_address, array('class'=>'form-control field-validate', 'id'=>'branch_address'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('Branch Address') }}</span>
                                                    <span class="help-block hidden">{{ trans('Branch Address Required') }}</span>
                                                </div>
                                            </div>
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
