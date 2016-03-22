@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-10 col-md-offset-1'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Search Duo Users</h3>
                <div class="box-tools pull-right">
                    <form action="/duo" method="GET">
                        <input type="text" name="search" value=""/>
                        <button type"submit">Search</button>
                    </form>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Real Name</th>
                                <th>User Name</th>
                                <th>Email Address</th>
                                <th>Status</th>
                                <th>Last Login</th>
                            </tr>
                            </thead>
                            <tfoot>
                            </tfoot>
                            <tbody>
                            @if(isset($users))
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <a class="sql-link" href="/duo/user/{{$user->id}}">
                                        {{$user->realname}}
                                    </a>
                                </td>
                                <td>{{$user->username}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->status}}</td>
                                <td>{{$user->last_login}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $users->appends(['search' => Input::get('search')])->render() !!}
                        @endif
                    </div> <!-- table-responsive -->
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection

@section('body_bottom')
@endsection
