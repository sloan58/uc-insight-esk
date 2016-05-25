@extends('layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-10 col-md-offset-1'>
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
                                    <th>Integration</th>
                                    <th>Factor</th>
                                    <th>Device</th>
                                    <th>IP Address</th>
                                    <th>New Enrollment</th>
                                    <th>Reason</th>
                                    <th>Result</th>
                                    <th>Timestamp</th>
                                    <th>Username</th>
                                    <th>Group</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>Integration</th>
                                    <th>Factor</th>
                                    <th>Device</th>
                                    <th>IP Address</th>
                                    <th>New Enrollment</th>
                                    <th>Reason</th>
                                    <th>Result</th>
                                    <th>Timestamp</th>
                                    <th>Username</th>
                                    <th>Group</th>
                                </tr>
                                </tfoot>
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
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('duo.auth.logs.data') !!}',
            columns: [
                {data: 'integration', name: 'duo_logs.integration'},
                {data: 'factor', name: 'duo_logs.factor'},
                {data: 'device', name: 'duo_logs.device'},
                {data: 'ip', name: 'duo_logs.ip'},
                {data: 'new_enrollment', name: 'duo_logs.new_enrollment'},
                {data: 'reason', name: 'duo_logs.reason'},
                {data: 'result', name: 'duo_logs.result'},
                {data: 'timestamp', name: 'duo_logs.timestamp'},
                {data: 'username', name: 'duo_users.username'},
                {data: 'name', name: 'duo_groups.name'}
            ],
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    $(input).appendTo($(column.footer()).empty())
                        .on('change', function () {
                            column.search($(this).val(), false, false, true).draw();
                    });
                });
            }
        });
    </script>

@endsection