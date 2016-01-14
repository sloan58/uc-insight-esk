@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('eraser/bulk/general.page.index.table-title') }}</h3>
                &nbsp;
                <div class="box-tools pull-right">
                    <div class="col-md-6 text-right">
                        <a type="button" class="btn btn-success btn-md" href="{{ route('eraser.bulk.create') }}" role="button">
                            <i class="fa fa-plus-circle fa-lg"></i>
                            Erase in Bulk
                        </a>
                    </div>
                </div>
                <div class="box-body">

                    <div class="table-responsive">
                        <table id="bulks-table" class="table table-striped row-border">
                            <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Process ID</th>
                                <th>Phones Processed</th>
                                <th>Result</th>
                                <th>Submitted</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($bulks as $bulk)
                            <tr>
                                <td>{{ $bulk->file_name }}</td>
                                <td>{{ $bulk->process_id }}</td>
                                <td>{{ $bulk->erasers()->count() }}</td>
                                <td>{{ $bulk->result }}</td>
                                <td>{{ $bulk->created_at->toDayDateTimeString() }}</td>
                            </tr>
                            @endforeach
                            </tbody>
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
