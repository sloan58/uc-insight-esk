@extends('layouts.master')

@section('content')
<div class='row'>
    <div class="col-md-8 col-md-offset-3">
        <form method="POST" action="/sql"
              class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <div class="col-sm-8">
                    <textarea type="textarea" id="sqlStatement" name="sqlStatement" placeholder="Enter SQL Statement Here..."
                              class="form-control">{{ $sql or '' }}</textarea>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        Submit Query
                    </button>
                </div>
            </div>
        </form>
    </div>
    @if(isset($data) && $data != '')
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('sql/general.page.index.table-title') }}</h3>
                &nbsp;
                <div class="box-tools pull-right">
                    <div class="col-md-6 text-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="sql-table">
                            <thead>
                            <tr>
                                @foreach($format as $header)
                                <th>{{ ucfirst($header) }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tfoot>
                            </tfoot>
                            <tbody>
                            @foreach($data as $row)
                            <tr>
                                @foreach($format as $header)
                                <td>{{ $row->$header }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div> <!-- table-responsive -->
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
        @endif
    </div><!-- /.row -->
@endsection

    <!-- Optional bottom section for modals etc... -->
    @section('body_bottom')
    <script>

        // DataTable
        $(function() {
            $("#sql-table").DataTable({
                order: [[0, "asc"]],
                dom: '<"top">Bfrt<"bottom"lip><"clear">',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });

        //Codemirror
        var myCodeMirror = CodeMirror.fromTextArea(sqlStatement, {
            mode: "text/x-mysql",
            lineNumbers: true,
            lineWrapping: true
        });
        myCodeMirror.setSize("100%", 300);
    </script>
    @endsection