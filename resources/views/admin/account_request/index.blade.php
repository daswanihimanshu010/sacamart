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
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>@sortablelink('languages_id', trans('labels.ID') )</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Business Name</th>
                                            <th>Status</th>
                                            <th>Account Request</th>
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
                                                      
                                                    <td>{{ $vendor_list->email }}</td>
                                               
                                                   
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
                                                    <td>
                                                        <a href="javascript:void(0)" class="badge bg-light-blue"> {{($vendor_list->account_inactive_status == 1) ? 'Request':'No Request'}} </a></td>
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
