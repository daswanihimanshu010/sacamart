@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>  Shipping Zones
            <small>Listing Shipping zones...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> Shipping Zones</li>
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
                            
                            <div class="box-tools pull-right">
                                <a href="{{url('admin/languages/shipping_zone_add')}}" type="button" class="btn btn-block btn-primary">Add Shipping Zone</a>
                            </div>
                        </div>
                        <br/><br/>
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
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Shipping Zone Id</th>
                                            <th>Shipping Zone Name</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($tax_class as $key=>$data)
                                            <tr>
                                                <td>{{ $data->shipping_zone_id }}</td>
                                                <td>{{ $data->shipping_zone_title }}</td>
                                                <td>
                                                    <a data-toggle="tooltip" data-placement="bottom" title="{{ trans('labels.Delete') }}" id="deleteShippingZoneId" shipping_zone_id ="{{ $data->shipping_zone_id }}" class="badge bg-red"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    

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
            <!-- deleteTaxClassModal -->
            <div class="modal fade" id="deleteShippingZoneModal" tabindex="-1" role="dialog" aria-labelledby="deleteShippingZoneModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteTaxClassModalLabel">Delete Shipping Zone</h4>
                        </div>
                        {!! Form::open(array('url' =>'admin/languages/shipping_zone_delete', 'name'=>'deleteShippingZoneClass', 'id'=>'deleteShippingZoneClass', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                        {!! Form::hidden('action',  'delete', array('class'=>'form-control')) !!}
                        {!! Form::hidden('id',  '', array('class'=>'form-control', 'id'=>'shipping_zone_id')) !!}
                        <div class="modal-body">
                            <p>Delete Shipping Zone</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.Close') }}</button>
                            <button type="submit" class="btn btn-primary" id="deleteTaxClass">{{ trans('labels.Delete') }}</button>
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
