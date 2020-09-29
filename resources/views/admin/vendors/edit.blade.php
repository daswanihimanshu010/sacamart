@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>  {{ trans('Vendors') }} <small>{{ trans('List of all vendors') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li><a href="{{ URL::to('admin/languages/display')}}"><i class="fa fa-language"></i>{{ trans('labels.languages') }}</a></li>
                <li class="active">{{ trans('Vendors') }}</li>
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
                          <h3 class="box-title">{{ trans('Edit Vendor') }}</h3>
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
                                        <?php
                                        
                                        // print_r($vendors);
                                        
                                         ?>
                                        <div class="box-body">

                                            {!! Form::open(array('url' =>'admin/languages/update_vendors', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}

                                            {!! Form::hidden('id',  $vendors->id, array('class'=>'form-control', 'id'=>'languages_id'))!!}

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('name',  $vendors->name, array('class'=>'form-control field-validate', 'id'=>'name', 'readonly'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Email') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('email',  $vendors->email, array('class'=>'form-control field-validate', 'id'=>'email'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageCode') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Phone') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('phone',  $vendors->phone, array('class'=>'form-control field-validate', 'id'=>'phone'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageCode') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>  


                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Country Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select name="country_id" class="form-control" required >
                                                         <option value="">--SELECT Country--</option>
                                                        @foreach ($countrylist as $key=>$countrylist)
                                                         <option value="{{ $countrylist->id }}" {{ !empty($countrylist->id == $vendors->country_id) ? 'selected':''  }}>{{ $countrylist->name }}</option>
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
                                                         <option value="{{ $statelist->id }}" {{ !empty($statelist->id == $vendors->state_id) ? 'selected':''  }}>{{ $statelist->name }}</option>
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
                                                         <option value="{{ $citylist->id }}" {{ !empty($citylist->id == $vendors->city_id) ? 'selected':''  }}>{{ $citylist->name }}</option>
                                                        @endforeach
                                                     </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>

                                           <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-2 control-label">{{ trans('labels.Category') }}</label>
                                                    <div class="col-sm-10 col-md-9">
                                                    <?php print_r($result['categories']); ?>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ChooseCatgoryText') }}.</span>
                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        


                                             <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Business Name') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('business_name',  $vendors->business_name, array('class'=>'form-control field-validate', 'id'=>'business_name'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageCode') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>



                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Business Address') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    {!! Form::text('business_address',  $vendors->business_address, array('class'=>'form-control field-validate', 'id'=>'business_address'))!!}
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageCode') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div>
                                            
                                            


                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Documents') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <input type="file" name="doucmument[]" multiple>
                                                </div>
                                               
                                                <?php $someArray = json_decode($vendors->doucmument, true); ?>
                                                @if(!empty($someArray))
                                                @foreach ($someArray as $key => $value)
                                                <?php $extenstion =  pathinfo($value, PATHINFO_EXTENSION);  ?>
                                                @if($extenstion == 'jpg') 
                                                <img src="{{ asset('doucmument/'.$value) }}" alt="" width="100" height="50"> 
                                                @elseif($extenstion == 'png') 
                                                <img src="{{ asset('doucmument/'.$value) }}" alt="" width="100" height="50"> 
                                                @elseif($extenstion == 'jpeg') 
                                                <img src="{{ asset('doucmument/'.$value) }}" alt="" width="100" height="50"> 
                                                @elseif($extenstion == 'gif') 
                                                <img src="{{ asset('doucmument/'.$value) }}" alt="" width="100" height="50"> 
                                                @else
                                                <a href="{{ asset('doucmument/'.$value) }}">{{ $value }}</a>
                                                @endif
                                                <!-- <img src="{{ asset('doucmument/'.$value) }}" alt="" width="100" height="50"> -->
                                                @endforeach
                                                @endif
                                                
                                            </div>

                                           <?php /* <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Status') }}</label>
                                                <div class="col-sm-10 col-md-4">
                                                    <select class="form-control field-validate" id="status" name="status">
                                                        <option value="1" @if($vendors->status==1) selected @endif>{{ trans('Active') }}</option>
                                                        <option value="0" @if($vendors->status==0) selected @endif>{{ trans('Deactive') }}</option>
                                                    </select>
                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.LanguageDirection') }}</span>
                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                            </div> */ ?>

                

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
