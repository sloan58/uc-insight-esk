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
                                <th>Name</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Size</th>
                                <th data-sortable="false">Actions</th>
                            </tr>
                            </thead>
                            <tbody>

                            {{-- The Subfolders --}}
                            @foreach ($subfolders as $path => $name)
                            <tr>
                                <td>
                                    <a href="upload?folder={{ $path }}">
                                        <i class="fa fa-folder fa-lg fa-fw"></i>
                                        {{ $name }}
                                    </a>
                                </td>
                                <td>Folder</td>
                                <td>-</td>
                                <td>-</td>
                                <td>
                                    <a href="#" data-toggle="modal" onclick="delete_folder('{{ $name }}')"><i class="fa fa-trash-o deletable"></i></a>
                                </td>
                            </tr>
                            @endforeach

                            {{-- The Files --}}
                            @foreach ($files as $file)
                            <tr>
                                <td>
                                    <a href="{{ $file['webPath'] }}">
                                        @if (is_image($file['mimeType']))
                                        <i class="fa fa-file-image-o fa-lg fa-fw"></i>
                                        @else
                                        <i class="fa fa-file-o fa-lg fa-fw"></i>
                                        @endif
                                        {{ $file['name'] }}
                                    </a>
                                </td>
                                <td>{{ $file['mimeType'] or 'Unknown' }}</td>
                                <td>{{ $file['modified']->format('j-M-y g:ia') }}</td>
                                <td>{{ human_filesize($file['size']) }}</td>
                                @if(\Auth::user()->hasRole(['admins','ios-config-admin']))
                                <td>
                                    <a href="{!! route('ios-config-generator.download', $file['name']) !!}" title="{{ trans('general.button.edit') }}"><i class="fa fa-download"></i></a>
                                    <!--                                        <i class="fa fa-pencil-square-o text-muted" title="{{ trans('cluster/general.error.cant-be-edited') }}"></i>-->

                                    <a href="{!! route('ios-config-generator.confirm-delete', $file['name']) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash-o deletable"></i></a>
                                    <!--                                        <i class="fa fa-trash-o text-muted" title="{{ trans('cluster/general.error.cant-be-deleted') }}"></i>-->
                                </td>
                                @endif
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
    <div class="modal fade" id="modal-ios-configs">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{route('ios-config-generator.loadfile')}}"
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
                                <input type="file" id="file" name="file">
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

    @include('admin.upload._modals')

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

    <script>

        // Confirm file delete
        function delete_file(name) {
            $("#delete-file-name1").html(name);
            $("#delete-file-name2").val(name);
            $("#modal-file-delete").modal("show");
        }

        // Confirm folder delete
        function delete_folder(name) {
            $("#delete-folder-name1").html(name);
            $("#delete-folder-name2").val(name);
            $("#modal-folder-delete").modal("show");
        }

        // Preview image
        function preview_image(path) {
            $("#preview-image").attr("src", path);
            $("#modal-image-view").modal("show");
        }

        // Startup code
        $(function() {
            $("#uploads-table").DataTable();
        });
    </script>

    @endsection
