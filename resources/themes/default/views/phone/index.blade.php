@extends('layouts.master')
@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('phone/general.page.index.table-title') }}</h3>
                    &nbsp;
                    <div class="box-tools pull-right">
                    </div>
                    <div class="box-body">

                        <div class="table-responsive">
                            <table id="table" class="table table-striped row-border">
                                <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Type</th>
                                    <th>Result</th>
                                    <th>Fail Reason</th>
                                    <th>Sent On</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($phone->erasers()->get() as $attempt)
                                    <tr>
                                        <td>{{ $attempt->ipAddress->ip_address }}</td>
                                        <td>{{ strtoupper($attempt->type) }}</td>
                                        <td >
                                            <i class="{{ $attempt->result == 'Success' ? 'fa fa-check' : 'fa fa-times' }}"></i>
                                        </td>
                                        <td>{{ $attempt->fail_reason}}</td>
                                        <td>{{ $attempt->created_at->toDayDateTimeString()}}</td>
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


        {{-- Create Folder Modal --}}
        <div class="modal fade" id="modal-erase-itl">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{route('itl.store')}}"
                          class="form-horizontal">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="folder" value="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                ×
                            </button>
                            <h4 class="modal-title">Erase itl File</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">
                                    MAC Address
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" id="name" name="name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Optional bottom section for modals etc... -->
        @section('body_bottom')
            <script language="JavaScript">

                // Modal
                function erase_itl() {
                    $("#modal-erase-itl").modal("show");
                }

            </script>
@endsection
