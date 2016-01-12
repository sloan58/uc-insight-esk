@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('sql/general.page.favorites.table-title') }}</h3>
                &nbsp;
                <div class="box-tools pull-right">
                    <div class="col-md-6 text-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="sql-table" class="table">
                            <thead>
                            <th data-field="active" data-sortable="true">SQL Statement: Click to re-run queries</th>
                            <th data-field="ip" data-sortable="true">Manage Favorites</th>
                            </thead>
                            <tbody>
                            @if(isset($favorites))
                            @foreach($favorites as $sql)
                            <tr>
                                <td>
                                    <a class="sql-link" href="{!! route('sql.show', $sql->id) !!}">
                                        {{ $sql->sql }}
                                    </a>
                                </td>
                                <td>
                                    <div class="col-md-4">
                                        @if(\Auth::user()->sqls->contains($sql->id))
                                        {!! Form::open(['route' => 'favorite.destroy']) !!}
                                        {!! Form::hidden('_method', 'DELETE') !!}
                                        {!! Form::hidden('favorite', $sql->id) !!}
                                        {!! Form::submit('Remove Favorite', ['class' => 'btn btn-small btn-warning']) !!}
                                        {!! Form::close() !!}
                                        @else
                                        {!! Form::open(['route' => 'favorite.store']) !!}
                                        {!! Form::hidden('favorite', $sql->id) !!}
                                        {!! Form::submit('Add Favorite', ['class' => 'btn btn-small btn-success']) !!}
                                        {!! Form::close() !!}
                                        @endif
                                    </div>
                                </td>
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
    </script>
    @endsection