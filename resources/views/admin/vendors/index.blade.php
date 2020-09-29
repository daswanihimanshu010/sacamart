@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>  {{ trans('Vendors') }} <small>{{ trans('List of all vendors') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> Vendors</li>
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
                        </div>
                           <div class="box-body">
                            <div class="col-md-12">
                                <form  name='registration' id="registration" class="registration" method="get" action="{{url('admin/languages/manage_vendors')}}">
                                   <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <div class="col-sm-2 form-group">
                                        <label>Name</label>
                                         {!! Form::text("name",request()->query('name'),["class"=>"form-control",'placeholder'=>' Name','id'=>'name']) !!}
                                    </div>

                                    <div class="col-sm-2 form-group">
                                        <label>Email</label>
                                         {!! Form::text("email",request()->query('email'),["class"=>"form-control",'placeholder'=>'Email','id'=>'E-mail']) !!}
                                    </div>

                                    <div class="col-sm-2 form-group">
                                        <label>Phone</label>
                                         {!! Form::text("phone",request()->query('phone'),["class"=>"form-control",'placeholder'=>'Phone','id'=>'phone']) !!}
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>Status</label>
                                         <select name="status" class="form-control">
                                             <option value="">--Select Status--</option>
                                             <option value="0" <?php if( request()->query('status')== 0 && request()->query('status')!=''){ echo 'selected';} ?> >Pending</option>
                                            <option value="1" <?php if( request()->query('status')== 1 && request()->query('status')!=''){ echo 'selected';} ?>>Approved</option>
                                            <option value="2" <?php if( request()->query('status')== 2 && request()->query('status')!=''){ echo 'selected';} ?>>Rejected</option>
                                         </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>Country</label>
                                         <select name="country_id" class="form-control" >
                                             <option value="">--Select Country--</option>
                                             @foreach ($countrylist as $key=>$countrylist)
                                             <option value="{{ $countrylist->id }}" <?php if( request()->query('country_id')== $countrylist->id && request()->query('country_id')!=''){ echo 'selected';}?> >{{ $countrylist->name }}</option>
                                            @endforeach
                                         </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>State</label>
                                         <select name="state_id" class="form-control state">
                                            <option value="">--SELECT State--</option>
                                            @foreach ($statelist as $key=>$statelist)
                                             <option value="{{ $statelist->id }}" <?php if( request()->query('state_id')==$statelist->id && request()->query('state_id')!=''){ echo 'selected';} ?>>{{ $statelist->name }}</option>
                                            @endforeach
                                         </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>City</label>
                                         <select name="city_id" class="form-control city">
                                          <option value="">--Select City--</option>
                                          @foreach ($citylist as $key=>$citylist)
                                             <option value="{{ $citylist->id }}" <?php if( request()->query('city_id')==$citylist->id && request()->query('city_id')!=''){ echo 'selected';} ?>>{{ $citylist->name }}</option>
                                              @endforeach
                                         </select>
                                        
                                    </div>
                                    <!-- <div class="col-sm-2 form-group">
                                        <label>Area</label>
                                          <select name="location_name" class="form-control address">
                                             <option value="">--Select Area--</option>
                                          </select>
                                    </div> -->
                                    <div class="col-sm-3 form-group">
                                        <label></label><br>
                                         <input type="submit" name="search" value="Search" class="btn btn-warning">
                                        
                                          <a href="{{url('admin/languages/manage_vendors')}}" class="btn btn-success">Reset</a>
                                    </div>
                                    <div class="input-group-form search-panel ">
                                        <?php /*
                                        <select type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown" name="FilterBy" id="FilterBy"  >
                                            <option value="" selected disabled hidden>{{trans('labels.Filter By')}}</option>
                                            <option value="Name"  @if(isset($filter)) @if  ($filter == "Name") {{ 'selected' }} @endif @endif>{{trans('Name')}}</option>
                                            <option value="E-mail" @if(isset($filter)) @if  ($filter == "E-mail") {{ 'selected' }}@endif @endif>{{trans('Email')}}</option>
                                            <option value="Phone" @if(isset($filter)) @if  ($filter == "E-mail") {{ 'selected' }}@endif @endif>{{trans('Phone')}}</option>
                                        </select>
                                         
                                       
                                        <button class="btn btn-primary " id="submit" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                                        @if(isset($parameter,$filter))  <a class="btn btn-danger " href="{{url('admin/languages/manage_vendors')}}"><i class="fa fa-ban" aria-hidden="true"></i> </a>@endif */ ?>
                                    </div>
                                </form>
                                <div class="col-lg-4 form-inline" id="contact-form12"></div>
                            </div>
                            <div class="box-tools pull-right">
                                <a href="{{ URL::to('admin/languages/add_vendors')}}" type="button" style="display:inline-block; width: auto; margin-top: 0;" class="btn btn-block btn-primary">{{ trans('labels.AddNew') }}</a>
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
                                    <table id="" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <!-- <th>Email</th> -->
                                            <th>Mobile</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Subscription</th>
                                            <th>Expires On</th>
                                            <th>Products</th>
                                            <th>Created at</th>
                                            <th>{{trans('labels.Action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                             /*echo "<pre>";
                                              print_r($vendors);
                                              die;*/
                                             ?>
                                        @if($result['languages'])
                                            @foreach ($vendors as $key=>$vendor_list)
                                                <tr>
                                                    <td>
                                                        {{ $vendor_list->id}}
                                                    </td>

                                                    <td>
                                                        {{ $vendor_list->name}}
                                                    </td>
                                                      
                                                    <!-- <th>{{ $vendor_list->email }}</th> -->
                                               
                                                   
                                                    <td>{{ $vendor_list->phone }}</td>
                                                    <td>{{ $vendor_list->business_name }}</td>
                                                    <?php /*<td>{{ ($vendor_list->status == 1) ? 'Approved':'Declined ' }}</td> */ ?>
                                                    <td>
                                                        <input type="hidden" name="vendorid" id="vendorid" value="{{ $vendor_list->id}}">
                                                        <select name="vendor_status" class="form-control" onchange="venderregistation(<?php echo $vendor_list->id ?>, this.value) ">
                                                            <option value="0" {{ ($vendor_list->status == 0) ? 'selected':'' }}>Pending</option>
                                                            <option value="1" {{ ($vendor_list->status == 1) ? 'selected':'' }}>Approved</option>
                                                            <option value="2" {{ ($vendor_list->status == 2) ? 'selected':'' }}>Rejected</option>
                                                        </select>
                                                    </td>
                                                    <!-- <td>
                                                        <a href="javascript:void(0)" class="badge bg-light-blue"> {{($vendor_list->account_inactive_status == 1) ? 'Request':'No Request'}} </a></td> -->
                                                        <td><?php foreach ($vendor_list->package_id as $key => $value) {
                                                            echo '<a href="javascript:void(0)" class="badge bg-light-blue">'.$value->package_name.'</a>';
                                                        } ?></td>
                                                        <td><?php if ($vendor_list->package_expiery_date > time()) { ?><a href="javascript:void(0)" class="badge bg-green"> Active,  {{ date('d M Y', $vendor_list->package_expiery_date) }}</a><?php } else { ?><a href="javascript:void(0)" class="badge bg-red">Expired</a><?php } ?></td>

                                                    <td><?php if ($vendor_list->no_of_products) { ?>{{ $vendor_list->no_of_products }}<?php } ?></td>
                                                    <td>{{ date('d-M-Y h:i a', strtotime($vendor_list->created_at)) }}</td>
                                                    <td>
                                                        <a data-toggle="tooltip" data-placement="bottom" title=" {{ $vendor_list->name }}" href="{{ URL::to('admin/languages/edit_vendors/'.$vendor_list->id)}}" class="badge bg-light-blue"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>

                                                        <a data-toggle="tooltip" data-placement="bottom" title=" {{ $vendor_list->name }}" href="{{ URL::to('admin/languages/vendors_address/'.$vendor_list->id)}}" class="badge bg-light-blue">Location</a>

                                                        <a data-toggle="tooltip" data-placement="bottom" href="{{ URL::to('admin/languages/vendors_category/'.$vendor_list->id)}}" class="badge bg-yellow">Category</a>
                                                        
                                                        <a data-toggle="tooltip" data-placement="bottom" title=" {{ $vendor_list->name }}" id="deletevendors" languages_id ="{{ $vendor_list->id }}" class="badge bg-red"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
            <!-- deletelanguagesModal -->
            <div class="modal fade" id="deletevendorsModal" tabindex="-1" role="dialog" aria-labelledby="deleteLanguagesModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteLanguagesModalLabel">Delete Vendor</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/languages/delete_vendors', 'name'=>'deletelanguages', 'id'=>'deletelanguages', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
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
