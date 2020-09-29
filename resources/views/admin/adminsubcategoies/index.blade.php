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
            <h1>  {{ trans('Vendor Sub Category') }} <small>{{ trans('vendor sub category list') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> {{ trans('Vendor Sub Category') }}</li>
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
                                
                                <a data-toggle="tooltip" data-placement="bottom"  title="category_add" id="addvendorscat" type="button" style="display:inline-block; width: auto; margin-top: 0;" class="btn btn-block btn-primary">{{ trans('labels.AddNew') }}</a>
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
                            <?php


                             ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>@sortablelink('languages_id', trans('labels.ID') )</th>
                                            <th>Category Name</th>
                                            <th>Sub Category Name</th>
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
                                                      
                                                    <td>{{ $vendors_list->categories_name }}</td>

                                                      <td>{{ $vendors_list->subcategory }}</td>
                                               
                                                   
                                                    <td>{{ date('d-M-Y h:i a', strtotime($vendors_list->created)) }}</td>
                                                    <td>

                                                         <?php /*<a data-toggle="tooltip" data-placement="bottom" title=" {{ $vendors_list->categories_name }}" id="editvendorscategory" languages_id ="{{ $vendors_list->id }}" class="badge bg-light-blue"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> */ ?>
                                                       
                                                            <a data-toggle="tooltip" data-placement="bottom" title=" {{ $vendors_list->categories_name }}" id="deletevendorscat" languages_id ="{{ $vendors_list->id }}" languagescat_id ="{{ $vendors_list->id }}" class="badge bg-red"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                        
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
            <!-- /.addvendorcatModel -->
            <div class="modal fade" id="addvendorscatModal" tabindex="-1" role="dialog" aria-labelledby="addanguagesModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="addanguagesModalLabel">Add Sub Vendor Category</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/languages/add_sub_vendors_category', 'name'=>'addlanguages', 'id'=>'addlanguages', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                        {!! Form::hidden('action',  'add', array('class'=>'form-control')) !!}
                        <input type="hidden" name="user_id" value="{{$request_url}}">
                        <div class="modal-body">
                           <div class="form-group">
                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Category Name') }}</label>
                                <div class="col-sm-10 col-md-4">
                                    <select name="category_id" class="form-control"  onchange="getvendorsubCategory(this.value, <?php echo $request_url ?>)" required>
                                     <option value="">--Select Category--</option>
                                       @foreach ($categories as $key=>$categoty)
                                         <option value="{{ $categoty->categories_id }}">{{ $categoty->categories_name }}</option>
                                        @endforeach
                                     </select>
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Sub Category Name') }}</label>
                                <div class="col-sm-10 col-md-4">
                                    <select name="sub_category_id" class="form-control" id="subcats"  required>
                                     <option value="">--Select Sub Category--</option>
                                     </select>
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.Close') }}</button>
                            <button type="submit" class="btn btn-primary" id="addlanguages">{{ trans('Save') }}</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <!-- editvendorcateModal -->
            <!-- <div class="modal fade" id="editvendorscatModal" tabindex="-1" role="dialog" aria-labelledby="editLanguagesModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="editLanguagesModalLabel">Vendor Category</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/languages/update_vendors_address', 'name'=>'editlanguages', 'id'=>'editlanguages', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                        {!! Form::hidden('action',  'edit', array('class'=>'form-control')) !!}
                        {!! Form::hidden('id',  '', array('class'=>'form-control', 'id'=>'languages_id')) !!}
                        <div class="modal-body">
                           <div class="form-group">
                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('Category Name') }}</label>
                                <div class="col-sm-10 col-md-4">
                                    <select name="category_id" class="form-control"  required>
                                     <option value="">--Select Category--</option>
                                       @foreach ($categories as $key=>$categoty)
                                         <option value="{{ $categoty->categories_id }}">{{ $categoty->categories_name }}</option>
                                        @endforeach
                                     </select>
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.exampleLanguageName') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.Close') }}</button>
                            <button type="submit" class="btn btn-primary" id="editlanguages">{{ trans('Save') }}</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div> -->
            <!--  row -->

             <!-- deletelanguagesModal -->
            <div class="modal fade" id="deletevendorscatModal" tabindex="-1" role="dialog" aria-labelledby="deleteLanguagesModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteLanguagesModalLabel">Delete Vendor SubCategory</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/languages/delete_sub_vendors_category', 'name'=>'deletelanguages', 'id'=>'deletelanguages', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
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

            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection
