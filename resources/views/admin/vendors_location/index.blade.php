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
            <h1>  Vendors Location <small>List of all vendor location...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> Vendors Location</li>
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
                                <form  name='registration' id="registration" class="registration" method="get" action="{{url('admin/languages/filter')}}">
                                    <input type="hidden"  value="{{csrf_token()}}">
                                    <div class="input-group-form search-panel ">
                                        <select type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown" name="FilterBy" id="FilterBy"  >
                                            <option value="" selected disabled hidden>{{trans('labels.Filter By')}}</option>
                                            <option value="Language"  @if(isset($filter)) @if  ($filter == "Name") {{ 'selected' }} @endif @endif>{{trans('labels.Language')}}</option>
                                            <option value="Code" @if(isset($filter)) @if  ($filter == "E-mail") {{ 'selected' }}@endif @endif>{{trans('labels.Code')}}</option>
                                        </select>
                                        <input type="text" class="form-control input-group-form " name="parameter" placeholder="Search term..." id="parameter" @if(isset($parameter)) value="{{$parameter}}" @endif >
                                        <button class="btn btn-primary " id="submit" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                                        @if(isset($parameter,$filter))  <a class="btn btn-danger " href="{{url('admin/languages/display')}}"><i class="fa fa-ban" aria-hidden="true"></i> </a>@endif
                                    </div>
                                </form>
                                <div class="col-lg-4 form-inline" id="contact-form12"></div>
                            </div>
                            <div class="box-tools pull-right">
                               <a data-toggle="tooltip" data-placement="bottom"  title="location_add" id="addvendorslocation" type="button" style="display:inline-block; width: auto; margin-top: 0;" class="btn btn-block btn-primary">{{ trans('labels.AddNew') }}</a>
                            </div>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    @if ($errors)
                                        @if($errors->any())
                                            <div @if ($errors->first()=='Default can not Deleted!!') class="alert alert-danger alert-dismissible" @else class="alert alert-success alert-dismissible" @endif role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                {{$errors->first()}}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div class="row default-div hidden">
                                <div class="col-xs-12">
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        {{ trans('labels.DefaultLanguageChangedMessage') }}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>@sortablelink('languages_id', trans('labels.ID') )</th>
                                            <th>Country Name</th>
                                            <th>State Name</th>
                                            <th>City Name</th>
                                            <th>Location Name</th>
                                            <th>Created at</th>
                                            <th>{{trans('labels.Action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($result['languages'])
                                            @foreach ($vendors as $key=>$vendors_list)
                                                <tr>
                                                    <td>
                                                        {{ $vendors_list->id}}
                                                    </td>
                                                      
                                                    <td>{{ $vendors_list->country_name }}</td>
                                                    <td>{{ $vendors_list->state_name }}</td>
                                                    <td>{{ $vendors_list->city_name }}</td>
                                                    <td>{{ $vendors_list->location_name }}</td>
                                               
                                                    <td>{{ date('d-M-Y h:i a', strtotime($vendors_list->created_at)) }}</td>
                                                    <td>
                                                        <a data-toggle="tooltip" data-placement="bottom" title=" {{ $vendors_list->location_name }}" href="{{ URL::to('admin/vendorlocation/edit/'.$vendors_list->id)}}" class="badge bg-light-blue"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                       
                                                            <a data-toggle="tooltip" data-placement="bottom" title=" {{ $vendors_list->location_name }}" id="deletevendorsaddress" languages_id ="{{ $vendors_list->id }}" class="badge bg-red"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                        
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">{{ trans('labels.Nolanguageexist') }}</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                        
                                        <div class="col-xs-12 text-right">
                                            {{$vendors->links()}}
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
             <div class="modal fade" id="addvendorslocationModal" tabindex="-1" role="dialog" aria-labelledby="deleteLanguagesModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteLanguagesModalLabel">Add Address</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/vendorlocation/insert', 'name'=>'deletelanguages', 'id'=>'deletelanguages', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                        @csrf
                        {!! Form::hidden('action',  'add', array('class'=>'form-control')) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Country Name') }}</label>
                                <div class="col-sm-10 col-md-4">
                                    <select name="country_id" class="form-control" id="v_country_id" required>
                                         <option value="">--SELECT Country--</option>
                                        @foreach ($countrylist as $key=>$countrylist)
                                         <option value="{{ $countrylist->id }}">{{ $countrylist->name }}</option>
                                        @endforeach
                                     </select>
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('State Name') }}</label>
                            <div class="col-sm-10 col-md-4">
                                <select name="state_id" class="form-control states" id="v_state_id" required>
                                 <option value="">--SELECT State--</option>
                                    @foreach ($statelist as $key=>$statelist)
                                     <option value="{{ $statelist->id }}">{{ $statelist->name }}</option>
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
                                 </select>
                                <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                            </div>
                        </div>

                         <div class="form-group">
                            <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Shipping Charges') }}</label>
                            <div class="col-sm-10 col-md-4">
                                {!! Form::number('shipping_fees',  null, array('class'=>'form-control', 'id'=>'shipping_fees'))!!}
                               
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Minimum Order Value') }}</label>
                            <div class="col-sm-10 col-md-4">
                                {!! Form::number('min_order',  null, array('class'=>'form-control', 'id'=>'min_order'))!!}
                               
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.Close') }}</button>
                            <button type="submit" class="btn btn-primary" id="deletelanguages">{{ trans('Save') }}</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <!-- deletelanguagesModal -->
            <div class="modal fade" id="addvendorsaddressModal" tabindex="-1" role="dialog" aria-labelledby="deleteLanguagesModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteLanguagesModalLabel">Delete Address</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/vendorlocation/delete', 'name'=>'deletelanguages', 'id'=>'deletelanguages', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
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

            <!--  row -->

            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection
