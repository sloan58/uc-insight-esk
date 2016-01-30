@extends('layouts.master')

@section('content')
        <div class='col-md-10 col-md-offset-1'>
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
                            <table class="table table-hover" id="table">
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
        </div><!-- /.row -->
@endsection

<!--    DataTables  -->
@include('partials._dataTables',['column' => '0'])