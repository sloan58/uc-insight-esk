@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('cluster/general.page.index.table-title') }}</h3>
                &nbsp;
                @if(\Auth::user()->hasRole(['admins']))
                    <a class="btn btn-default btn-sm" href="{!! route('cluster.create') !!}" title="{{ trans('cluster/general.button.create') }}">
                        <i class="fa fa-plus-square"></i>
                    </a>
                    &nbsp;
                @endif
                <div class="box-tools pull-right">
                    <div class="col-md-6 text-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('cluster/general.columns.name') }}</th>
                                <th>{{ trans('cluster/general.columns.active') }}</th>
                                <th>{{ trans('cluster/general.columns.ip') }}</th>
                                <th>{{ trans('cluster/general.columns.username') }}</th>
                                <th>{{ trans('cluster/general.columns.actions') }}</th>

                            </tr>
                            </thead>
                            <tfoot>
                            </tfoot>
                            <tbody>
                            @foreach($clusters as $cluster)
                            <tr>
                                <td>{{$cluster->name}}</td>
                                <td>{{ $activeClusterId == $cluster->id ? 'Active' : ''}}</td>
                                <td>{{$cluster->ip}}</td>
                                <td>{{$cluster->username}}</td>
                                <td>
                                        <a href="{!! route('cluster.edit', $cluster->id) !!}" title="{{ trans('general.button.edit') }}"><i class="fa fa-pencil-square-o"></i></a>
<!--                                        <i class="fa fa-pencil-square-o text-muted" title="{{ trans('cluster/general.error.cant-be-edited') }}"></i>-->

                                        <a href="{!! route('cluster.confirm-delete', $cluster->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash-o deletable"></i></a>
<!--                                        <i class="fa fa-trash-o text-muted" title="{{ trans('cluster/general.error.cant-be-deleted') }}"></i>-->
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $clusters->render() !!}
                    </div> <!-- table-responsive -->
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
    @endsection

    <!-- Optional bottom section for modals etc... -->
    @section('body_bottom')
    <script language="JavaScript">
        function erase_ctl() {
            $("#modal-erase-ctl").modal("show");
        }
    </script>
    @endsection