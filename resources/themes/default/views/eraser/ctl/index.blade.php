@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('erasers/ctl/general.page.index.table-title') }}</h3>
                &nbsp;
                <div class="box-tools pull-right">
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-success btn-md"
                                onclick="erase_ctl()">
                            <i class="fa fa-plus-circle fa-lg"></i>
                            Erase CTLs
                        </button>
                </div>
            </div>
            <div class="box-body">

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>{{ trans('erasers/ctl/general.columns.name') }}</th>
                            <th>{{ trans('erasers/ctl/general.columns.description') }}</th>
                            <th>{{ trans('erasers/ctl/general.columns.ip_address') }}</th>
                            <th>{{ trans('erasers/ctl/general.columns.result') }}</th>
                            <th>{{ trans('erasers/ctl/general.columns.fail_reason') }}</th>
                            <th>{{ trans('erasers/ctl/general.columns.last_updated') }}</th>
                        </tr>
                        </thead>
                        <tfoot>
                        </tfoot>
                        <tbody>
                        @foreach ($ctls as $ctl)
                            @if(!$ctl->failure_reason)
                                {{$ctl->failure_reason == 'Passed'}}
                            @endif
                        <tr>
                            <td>{{ $ctl->phone->mac }}</td>
                            <td>{{ $ctl->phone->description}}</td>
                            <td>{{ $ctl->ip_address}}</td>
                            <td >
                                <i class="{{ $ctl->result == 'Success' ? 'fa fa-check' : 'fa fa-times' }}"></i>
                            </td>
                            <td>{{ $ctl->failure_reason}}</td>
                            <td>
                                {{ $ctl->updated_at->toDayDateTimeString() }}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {!! $ctls->render() !!}
                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->

</div><!-- /.row -->
@endsection


{{-- Create Folder Modal --}}
<div class="modal fade" id="modal-erase-ctl">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{route('ctl.store')}}"
                  class="form-horizontal">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="folder" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">Erase CTL File</h4>
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
    function toggleCheckbox() {
        checkboxes = document.getElementsByName('chkUser[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = !checkboxes[i].checked;
        }
    }

    function erase_ctl() {
        $("#modal-erase-ctl").modal("show");
    }
</script>
@endsection
