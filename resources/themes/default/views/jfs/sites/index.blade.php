@extends('layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-10 col-md-offset-1'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Search Duo Users</h3>
                    <div class="box-tools pull-right">
                        <form action="{{ route('jfs.sites.index')  }}" method="GET">
                            <input type="text" name="search" value=""/>
                            <button type"submit">Search</button>
                        </form>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Site Name</th>
                                    <th>Completed Tasks</th>
                                    <th>Incomplete Tasks</th>
                                </tr>
                                </thead>
                                <tfoot>
                                </tfoot>
                                <tbody>
                                @if(isset($sites))
                                    @foreach($sites as $site)
                                        <tr>
                                            <td>
                                                <a class="sql-link" href="{{ route('jfs.sites.show', [ $site->id ]) }}">
                                                    {{ $site->name }}
                                                </a>
                                            </td>
                                            <td>{{ $site->completedTasks()->count() }}</td>
                                            <td>{{ $site->incompleteTasks()->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $sites->appends(['search' => Input::get('search')])->render() !!}
                            @endif
                        </div> <!-- table-responsive -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>
@endsection
