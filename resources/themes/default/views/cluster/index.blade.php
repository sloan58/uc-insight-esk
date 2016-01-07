@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('cluster/general.page.index.table-title') }}</h3>
                &nbsp;
                <div class="box-tools pull-right">
                    <div class="col-md-6 text-right">
                        @if(\Auth::user()->hasRole(['admins']))
                        <a type="button" class="btn btn-md btn-success pull-right" href="{{route('cluster.create')}}" role="button">
                            <i class="fa fa-plus-circle fa-lg"></i>
                            Add Cluster
                        </a>
                        @endif
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        @if(isset($clusters))
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('cluster/general.columns.name') }}</th>
                                <th>{{ trans('cluster/general.columns.active') }}</th>
                                <th>{{ trans('cluster/general.columns.ip') }}</th>
                                <th>{{ trans('cluster/general.columns.username') }}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            </tfoot>
                            <tbody>
                            @foreach($clusters as $cluster)
                            <tr>
                                <td>{{$cluster->name}}</td>
                                <td>{{\Auth::user()->clusters_id == $cluster->id ? 'Active' : ''}}</td>
                                <td>{{$cluster->ip}}</td>
                                <td>{{$cluster->username}}</td>
                                <td>
                                    <!-- edit this Cluster -->
                                    <a href="{!! route('cluster.edit', $cluster->id) !!}" title="Edit Cluster"><i class="fa fa-pencil-square-o fa-2x enabled editable"></i></a>

                                    <!-- delete this User -->
                                    <a href="{!! route('cluster.destroy', $cluster->id) !!}" data-toggle="modal" data-target="#modal-delete" title="Delete Clustetr"><i class="fa fa-trash-o fa-2x enabled deletable"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $clusters->render() !!}
                        @endif
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