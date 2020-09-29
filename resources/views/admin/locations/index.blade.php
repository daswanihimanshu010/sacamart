@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
             <h1>  Location <small>List of all Location...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> {{ trans('Location') }}</li>
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
                            <form  name='registration' id="registration" class="registration" method="get">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <div class="col-sm-2 form-group">
                                        <label>Country</label>
                                         <select name="country_id" class="form-control" >
                                             <option>--Select Country--</option>
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
                                    
                                    <div class="col-sm-3 form-group">
                                        <label></label><br>
                                         <input type="submit" name="search" value="Search" class="btn btn-warning">
                                        
                                          <a href="" class="btn btn-success">Reset</a>
                                    </div>
                                </form>
                            <div class="box-tools pull-right">
                                <a href="{{ URL::to('admin/languages/add_location')}}" type="button" style="display:inline-block; width: auto; margin-top: 0;" class="btn btn-block btn-primary">{{ trans('labels.AddNew') }}</a>
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
                                            <th>Location</th>
                                            <th>Slab Name</th>
                                            <th>Weight Start</th>
                                            <th>Weight End</th>
                                            <th>Shipping fee</th>
                                            <th>Status</th>
                                            <th>Created at</th>
                                            <th>{{trans('labels.Action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($result['languages'])
                                            @foreach ($locations as $key=>$location_list)
                                                <tr>
                                                    <td>
                                                        {{ $location_list->id}}
                                                    </td>
                                                      
                                                    <td>{{ $location_list->country_name }}</td>
                                                    <td>{{ $location_list->state_name }}</td>
                                                    <td>{{ $location_list->city_name }}</td>
                                                    <td>{{ $location_list->location }}</td>
                                                    <td>{{ $location_list->slab_name }}</td>
                                                    <td>{{ $location_list->range_start }}</td>
                                                    <td>{{ $location_list->range_end }}</td>
                                                    <td>{{ $location_list->shipping_fee }}</td>
                                                    <td>{{ ($location_list->status == 1) ? 'Active':'Deactive' }}</td>
                                                    <td>{{ date('d-M-Y h:i a', strtotime($location_list->created_at)) }}</td>
                                                    <td>
                                                        <a data-toggle="tooltip" data-placement="bottom" title=" {{ $location_list->location }}" href="{{ URL::to('admin/languages/edit_location/'.$location_list->id)}}" class="badge bg-light-blue"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                       
                                                        <a data-toggle="tooltip" data-placement="bottom" title=" {{ $location_list->location }}" id="deletelocation" languages_id ="{{ $location_list->id }}" class="badge bg-red"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                        
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
                                            {{$locations->links()}}
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
            <div class="modal fade" id="deletelocationModal" tabindex="-1" role="dialog" aria-labelledby="deleteLanguagesModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteLanguagesModalLabel">{{ trans('Delete Location') }}</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/languages/delete_location', 'name'=>'deletelanguages', 'id'=>'deletelanguages', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                        {!! Form::hidden('action',  'delete', array('class'=>'form-control')) !!}
                        {!! Form::hidden('id',  '', array('class'=>'form-control', 'id'=>'languages_id')) !!}
                        <div class="modal-body">
                            <p>Confirm Are You Sure, want to delete ?  </p>
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
