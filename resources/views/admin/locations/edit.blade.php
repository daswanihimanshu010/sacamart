@extends('admin.layout')
@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>  Location <small>List of all Location...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li><a href="{{ URL::to('admin/languages/display')}}"><i class="fa fa-language"></i>{{ trans('labels.languages') }}</a></li>
                <li class="active">{{ trans('Location') }}</li>
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
                          <h3 class="box-title">{{ trans('Edit Location') }}</h3>
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

                                            {!! Form::open(array('url' =>'admin/languages/update_location', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}
                                             {!! Form::hidden('id', $locations->id, array('id'=>'myid')) !!}
                                                @csrf


                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Country Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="country_id" class="form-control" required >
                                                         <option value="">--SELECT Country--</option>
                                                        @foreach ($countrylist as $key=>$countrylist)
                                                         <option value="{{ $countrylist->id }}" {{ !empty($countrylist->id == $locations->country_id) ? 'selected':''  }}>{{ $countrylist->name }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('State Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="state_id" class="form-control state"  required>
                                                     <option value="">--SELECT State--</option>
                                                        @foreach ($statelist as $key=>$statelist)
                                                         <option value="{{ $statelist->id }}" {{ !empty($statelist->id == $locations->state_id) ? 'selected':''  }}>{{ $statelist->name }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            
                                            
                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('City Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="city_id" class="form-control city"  required>
                                                     <option value="">--Select City--</option>
                                                       @foreach ($citylist as $key=>$citylist)
                                                         <option value="{{ $citylist->id }}" {{ !empty($citylist->id == $locations->city_id) ? 'selected':''  }}>{{ $citylist->name }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Location Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('location', $locations->location, array('class'=>'form-control field-validate', 'id'=>'location'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageCode') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">Shipping Zone</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="shipping_zone_id" class="form-control" required >
                                                         <option value="">--SELECT Shipping Zone--</option>
                                                        @foreach ($shipping_zones_charges as $key=>$shipping_zone)
                                                         <option value="{{ $shipping_zone->shipping_zone_id }}" {{ !empty($shipping_zone->shipping_zone_id == $locations->shipping_zone_id) ? 'selected':''  }}>{{ $shipping_zone->shipping_zone_title }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">Shipping Zone </span>
                                                    <span class="help-block hidden">Shipping Zone Required</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Status') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select class="form-control field-validate" id="status" name="status">
                                                        <option value="1" {{ !empty($locations->status == 1) ? 'selected':''  }}>{{ trans('Active') }}</option>
                                                        <option value="0" {{ !empty($locations->status == 0) ? 'selected':''  }}>{{ trans('Deactive') }}</option>
                                                    </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.LanguageDirection') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- /.box-body -->
                                            <div class="box-footer text-right">
                                                <div class="col-sm-offset-2 col-md-offset-3 col-sm-10 col-md-4">
                                                    <button type="submit" class="btn btn-primary">{{ trans('labels.Submit') }}</button>
                                                    <a href="{{ URL::to('admin/languages/manage_states')}}" type="button" class="btn btn-default">{{ trans('labels.back') }}</a>
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
