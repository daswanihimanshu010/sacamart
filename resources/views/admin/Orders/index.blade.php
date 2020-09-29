@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> {{ trans('labels.Orders') }} <small>{{ trans('labels.ListingAllOrders') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active">{{ trans('labels.Orders') }}</li>
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
                            <h3 class="box-title">{{ trans('labels.ListingAllOrders') }} </h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="col-md-12">
                                <form  name='registration' id="registration" class="registration" method="get">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    
                                    <div class="col-sm-2 form-group">
                                        <label>Customer Name</label>
                                         {!! Form::text("name",request()->query('name'),["class"=>"form-control",'placeholder'=>'Customer Name','id'=>'name']) !!}
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>Order Status</label>
                                         <select name="status" class="form-control">
                                             <option value="">--Select Status--</option>
                                             <option value="1" <?php if( request()->query('status')== 1 && request()->query('status')!=''){ echo 'selected';} ?>>Pending</option>
                                             <option value="2" <?php if( request()->query('status')== 2 && request()->query('status')!=''){ echo 'selected';} ?>>Completed</option>
                                             <option value="3" <?php if( request()->query('status')== 3 && request()->query('status')!=''){ echo 'selected';} ?>>Cancel</option>
                                             <option value="4" <?php if( request()->query('status')== 4 && request()->query('status')!=''){ echo 'selected';} ?>>Return</option>
                                             <option value="5" <?php if( request()->query('status')== 5 && request()->query('status')!=''){ echo 'selected';} ?>>Confirm</option>
                                             <option value="6" <?php if( request()->query('status')== 6 && request()->query('status')!=''){ echo 'selected';} ?>>Processing</option>
                                             <option value="7" <?php if( request()->query('status')== 7 && request()->query('status')!=''){ echo 'selected';} ?>>Out for delivery</option>
                                         </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>Order Number</label>
                                         {!! Form::text("o_no",request()->query('o_no'),["class"=>"form-control",'placeholder'=>'Order Number','id'=>'o_no']) !!}
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

                                    <div class="col-sm-2 form-group">
                                        <label>Area</label>
                                          <select name="location_name" class="form-control address">
                                             <option value="">--Select Area--</option>
                                             @foreach ($locations as $key=>$location)
                                             <option value="{{ $location->id }}" <?php if( request()->query('location_name')==$location->id && request()->query('location_name')!=''){ echo 'selected';} ?>>{{ $location->location }}</option>
                                              @endforeach
                                          </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>From Date</label>
                                         {!! Form::text("fromdate",request()->query('fromdate'),["class"=>"form-control datepicker",'placeholder'=>'Date From','id'=>'fromdate']) !!}
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>To Date</label>
                                         {!! Form::text("todate",request()->query('todate'),["class"=>"form-control datepicker",'placeholder'=>'Date To','id'=>'todate']) !!}
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label></label><br>
                                         <input type="submit" name="search" value="Search" class="btn btn-warning">
                                        
                                          <a href="" class="btn btn-success">Reset</a>
                                    </div>
                                </form>
                                <button type="button" id="btnExportOrders" class="btn btn-primary pull-right exporting" data-toggle="tooltip" title="" data-original-title="Generate Excel"><i class="fa fa-file-excel-o"></i> Generate Excel</button>
                            </div>
                        </div>
                        
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
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>{{ trans('labels.ID') }}</th>
                                            <th>{{ trans('labels.CustomerName') }}</th>
                                            <th>{{ trans('labels.OrderTotal') }}</th>
                                            <th>{{ trans('labels.DatePurchased') }}</th>
                                            <th>{{ trans('labels.Status') }} </th>
                                            <th>{{ trans('labels.Action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($listingOrders['orders'])>0)
                                            @foreach ($listingOrders['orders'] as $key=>$orderData)
                                                <tr>
                                                    <td>{{ $orderData->orders_id }}</td>
                                                    <td>{{ $orderData->customers_name }}</td>
                                                    <td>
                                                        
                                                        @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $orderData->order_price }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif</td>
                                                    <td>{{ date('d/m/Y', strtotime($orderData->date_purchased)) }}</td>
                                                    <td>
                                                        @if($orderData->orders_status_id==1)
                                                            <span class="label label-warning">
                                                        @elseif($orderData->orders_status_id==2)
                                                            <span class="label label-success">
                                                        @elseif($orderData->orders_status_id==3)
                                                            <span class="label label-danger">
                                                        @else
                                                            <span class="label label-primary">
                                                        @endif
                                                        {{ $orderData->orders_status }}
                                                            </span>
                                                    </td>
                                                    <td>
                                                        <a data-toggle="tooltip" data-placement="bottom" title="View Order" href="vieworder/{{ $orderData->orders_id }}" class="badge bg-light-blue"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>

                                                        <a data-toggle="tooltip" data-placement="bottom" title="Delete Order" id="deleteOrdersId" orders_id ="{{ $orderData->orders_id }}" class="badge bg-red"><i class="fa fa-trash" aria-hidden="true"></i></a>

                                                    </td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6"><strong>{{ trans('labels.NoRecordFound') }}</strong></td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                    <div class="col-xs-12 text-right">
                                        {{$listingOrders['orders']->links()}}
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

            <!-- deleteModal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteModalLabel">{{ trans('labels.DeleteOrder') }}</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/orders/deleteOrder', 'name'=>'deleteOrder', 'id'=>'deleteOrder', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                        {!! Form::hidden('action',  'delete', array('class'=>'form-control')) !!}
                        {!! Form::hidden('orders_id',  '', array('class'=>'form-control', 'id'=>'orders_id')) !!}
                        <div class="modal-body">
                            <p>{{ trans('labels.DeleteOrderText') }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.Close') }}</button>
                            <button type="submit" class="btn btn-primary" id="deleteOrder">{{ trans('labels.Delete') }}</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            <!-- Main row -->

            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection
