@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('eraser/bulk/general.page.index.table-title') }}</h3>
                &nbsp;
                <div class="box-body">

                    <div class="table-responsive">
                        <table id="bulk-table" class="table table-striped row-border">
                            <thead>
                            <tr>
                                <th>Phone Name</th>
                                <th>IP Address</th>
                                <th>Type</th>
                                <th>Result</th>
                                <th>Fail Reason</th>
                                <th>Sent On</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($bulk->erasers as $eraser)
                            <tr>
                                <td>{{ $eraser->device->name }}</td>
                                <td>{{ $eraser->ipAddress->ip_address }}</td>
                                <td>{{ $eraser->type }}</td>
                                <td >
                                    <i class="{{ $eraser->result == 'Success' ? 'fa fa-check' : 'fa fa-times' }}"></i>
                                </td>
                                <td>{{ $eraser->fail_reason}}</td>
                                <td>{{ $eraser->created_at->toDayDateTimeString()}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        </table>
                    </div> <!-- table-responsive -->

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
    @endsection

    <!-- Optional bottom section for modals etc... -->
    @section('body_bottom')
    <script language="JavaScript">
        $(function() {
            $("#bulks-table").DataTable({
                order: [[4, "desc"]],
                "aoColumnDefs": [
                    {
                        "aTargets": [ 0 ], // Column to target
                        "mRender": function ( data, type, full ) {
                            // 'full' is the row's data object, and 'data' is this column's data
                            // e.g. 'full is the row object, and 'data' is the phone mac
                            return '<a href="/bulk/' + full[1] + '">' + data + '</a>';
                        }
                    }
                ]
            });
        });
    </script>
    @endsection
