@extends('layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Search Duo Logs</h3>
                    <div class="box-tools pull-right">
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="table">
                                <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>Factor</th>
                                    <th>Integration</th>
                                    <th>IP Address</th>
                                    <th>New Enrollment</th>
                                    <th>Reason</th>
                                    <th>Result</th>
                                    <th>Timestamp</th>
                                </tr>
                                </thead>
                            </table>
                        </div> <!-- table-responsive -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>
@endsection

@section('body_bottom')

    <script>
        $(function() {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('duo.auth.logs.data') !!}',
                dom: '<"top">Bfrt<"bottom"lip><"clear">',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
        });
    </script>

@endsection