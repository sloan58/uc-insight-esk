@extends('layouts.master')
@section('content')
<div class='row'>
    <div class='col-md-8 col-md-offset-2'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">IOS Config Generator</h3>
                &nbsp;
                <div class="box-tools pull-right">
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-success btn-md"
                                onclick="load_ios_configs()">
                            <i class="fa fa-plus-circle fa-lg"></i>
                            Load new Configs
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table">
                            <thead>
                            <tr>
                                <th>File Name</th>
                                @if(\Auth::user()->hasRole(['admins','ios-config-admin']))
                                <th>Actions</th>
                                @endif
                            </tr>
                            </thead>
                            <tfoot>
                            </tfoot>
                            <tbody>
                            @if(isset($shortNames))
                            @foreach ($shortNames as $shortName)
                            <tr>
                                <td>
                                    <a href="{{ route('jfs.configs.create', [ $shortName ]) }}">
                                        <div>
                                            {{ $shortName }}
                                        </div>
                                    </a>
                                </td>
                                @if(\Auth::user()->hasRole(['admins','ios-config-admin']))
                                <td>
                                    <a href="{!! route('jfs.configs.download', $shortName) !!}" title="{{ trans('general.button.edit') }}"><i class="fa fa-download"></i></a>
                                    <a href="{!! route('jfs.configs.confirm-delete', $shortName) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash-o deletable"></i></a>
                                </td>
                                @endif
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


    {{-- Create Folder Modal --}}
    <div class="modal fade" id="modal-ios-configs">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('jfs.configs.loadfile') }}"
                      class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="folder" value="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            ×
                        </button>
                        <h4 class="modal-title">Load new IOS Configs</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file" class="col-sm-3 control-label">
                                File Selection
                            </label>
                            <div class="col-sm-8">
                                <input type="file" name="file">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="left"
                                title="Please load a text file (.txt) with IOS variables encapsulated within double less than/greater than braces: '<<VARNAME>>'.  Use hyphens for all multi-word variable names '<<VAR-NAME>>'">
                            File Format
                        </button>
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

    <!--    DataTables  -->
    @include('partials._dataTables',['column' => '5'])

    <!-- Optional bottom section for modals etc... -->
    @section('body_bottom')
    <script>
        // Modal
        function load_ios_configs() {
            $("#modal-ios-configs").modal("show");
        }
    </script>
    @endsection
