@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> Notify Me </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active"> Notify Me</li>
            </ol>
        </section>

        <!--  content -->
        <section class="content">
            <!-- Info boxes -->

            <!-- /.row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Products Id</th>
                                            <th>Customer Id</th>
                                            <th>Customer Name</th>
                                            <th>Email</th>
                                            <th>Product Name</th>
                                            <th>Weight</th>
                                            <th>Price</th>
                                            <th>{{ trans('labels.Action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($result['notifyme'] as $key=>$notifyme)
                                            <tr>
                                                <td>{{ $notifyme->products_id }}</td>
                                                <td>{{ $notifyme->id }}</td>
                                                <td>{{ $notifyme->first_name }} {{ $notifyme->last_name }}</td>
                                                <td>{{ $notifyme->email }}</td>
                                                <td>{{ $notifyme->products_name }}</td>
                                                <td>{{ $notifyme->products_weight }} {{  $notifyme->products_weight_unit }}</td>
                                                <td>Rs {{ $notifyme->products_price }}</td>
                                                <td><a data-toggle="tooltip" data-placement="bottom" title="Notify" href="{{ URL::to('admin/notifyinstock/'.$notifyme->notify_id) }}" class="badge bg-light-blue"><i class="fa fa-bell" aria-hidden="true"></i></a>
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
            <!-- deletetaxRateModal -->
            

            <!--  row -->

            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection
