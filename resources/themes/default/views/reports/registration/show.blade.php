@extends('layouts.master')
@section('content')
    <div class='row'>
        <div class='col-md-10 col-md-offset-1'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">CUCM Device Registration Report</h3>
                    &nbsp;
                    <div class="box-body">
                        <div class="table-responsive" id="vue-table">
                            <table id="table" class="table table-striped row-border">
                                <thead>
                                <tr>
                                    <th>Device Name</th>
                                    <th>Product</th>
                                    <th>Description</th>
                                    <th>Registration</th>
                                    <th>IP Address</th>
                                    <th>Model</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($registrationReport as $device)
                                    <tr>
                                        <td>{{$device['DeviceName']}}</td>
                                        <td>{{$device['Product']}}</td>
                                        <td>{{$device['Description']}}</td>
                                        <td>{{$device['IsRegistered'] ? 'Registered' : 'Unregistered/Unknown'}}</td>
                                        <td>{{$device['IpAddress']}}</td>
                                        <td>{{$device['Model'] or 'Unavailable'}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div> <!-- table-responsive -->

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>
    @endsection

<!--    DataTables  -->
@include('partials._dataTables',['column' => '0'])

<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
    <script>
        new Vue({
            el: '#vue-table',
            data: {
                itls: []
            },
            ready: function() {
                this.$http.get('/api/v1/eraser/itls', function(itls) {
                    this. itls = itls;
                    console.log(this.itls);
                }.bind(this));
            }
        })
    </script>
@endsection
