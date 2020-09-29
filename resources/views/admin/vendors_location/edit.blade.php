@extends('admin.layout')
@section('content')


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> Vendor Location <small>{{ trans('List of vendor Location') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li><a href="{{ URL::to('admin/languages/display')}}"><i class="fa fa-language"></i>{{ trans('labels.languages') }}</a></li>
                <li class="active">{{ trans('Vendor Location') }}</li>
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
                          <h3 class="box-title">{{ trans('Edit Vendor Location') }}</h3>
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

                                            {!! Form::open(array('url' =>'admin/vendorlocation/update', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}

                                            {!! Form::hidden('id',  $vendors->id, array('class'=>'form-control', 'id'=>'languages_id'))!!}

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Country Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="country_id" class="form-control" id="v_country_id" required>
                                                         <option value="">--SELECT Country--</option>
                                                        @foreach ($countrylist as $key=>$countrylist)
                                                         <option value="{{ $countrylist->id }}" {{ !empty($countrylist->id == $vendors->country_id) ? 'selected':''}}  >{{ $countrylist->name }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('State Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                   <select name="state_id" class="form-control states" id="v_state_id" required>
                                                     <option value="">--SELECT State--</option>
                                                        @foreach ($statelist as $key=>$statelist)
                                                         <option value="{{ $statelist->id }}" {{ !empty($statelist->id == $vendors->state_id) ? 'selected':''}}>{{ $statelist->name }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('City Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="city_id" class="form-control citys" id="v_city_id" required>
                                                     <option value="">--Select City--</option>
                                                      @foreach ($citylist as $key=>$citylist)
                                                         <option value="{{ $citylist->id }}" {{ !empty($citylist->id == $vendors->city_id) ? 'selected':''}}>{{ $citylist->name }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Address Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="location_name" class="form-control addresss" id="v_location_id" required>
                                                     <option value="">--Select Address--</option>
                                                       @foreach ($locationlist as $key=>$locationlist)
                                                         <option value="{{ $locationlist->id }}" {{ !empty($locationlist->id == $vendors->location_name) ? 'selected':''}}>{{ $locationlist->location }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Shipping Charges') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::number('shipping_fees',  $vendors->shipping_fees, array('class'=>'form-control field-validate', 'id'=>'shipping_fees'))!!}
                                                   
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Minimum Order Value') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::number('min_order',  $vendors->min_order, array('class'=>'form-control', 'id'=>'min_order'))!!}
                                                   
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Status') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select class="form-control field-validate" id="status" name="status">
                                                        <option value="1" @if($vendors->status==1) selected @endif>{{ trans('Active') }}</option>
                                                        <option value="0" @if($vendors->status==0) selected @endif>{{ trans('Deactive') }}</option>
                                                    </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.LanguageDirection') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
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
