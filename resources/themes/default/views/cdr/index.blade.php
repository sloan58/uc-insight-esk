@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('cdrs/general.page.create.section-title') }}</h3>
                &nbsp;
                <div class="box-tools pull-right">
                    <div class="col-md-6 text-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table">
                            <thead>
                            <th data-field="dialed" data-sortable="true">Dialed Number</th>
                            <th data-field="callerid" data-sortable="true">Caller ID</th>
                            <th data-field="type" data-sortable="true">Call Type</th>
                            <th data-field="message" data-sortable="true">Message</th>
                            <th data-field="successful" data-sortable="true">Result</th>
                            <th data-field="reason" data-sortable="true">Fail Reson</th>
                            <th data-field="timestamp" data-sortable="true">Timestamp</th>
                            </thead>
                            <tbody>
                            @if(isset($cdrs))
                            @foreach($cdrs as $cdr)
                            <tr>
                                <td>{{$cdr->dialednumber}}</td>
                                <td>{{$cdr->callerid}}</td>
                                <td>{{$cdr->calltype}}</td>
                                <td>{{$cdr->message}}</td>
                                <td>{{$cdr->successful ? 'Success' : 'Fail'}}</td>
                                <td>{{$cdr->failurereason}}</td>
                                <td>{{$cdr->created_at->format('m-d-Y H:i:s')}}</td>
                            </tr>
                            @endforeach
                            @endif
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
    <script>

    </script>
    @endsection