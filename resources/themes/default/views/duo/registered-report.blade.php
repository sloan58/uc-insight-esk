@extends('layouts.master')
@section('content')
    <div class='row'>
        <div class='col-md-10 col-md-offset-1'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Registered Duo User Report</h3>
                    &nbsp;
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="table" class="table">
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
                            @endif
                        </div> <!-- table-responsive -->

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->

        </div><!-- /.row -->
        @endsection

        <!--    DataTables  -->
        @include('partials._dataTables',['column' => '0'])

        <!-- Optional bottom section for modals etc... -->
        @section('body_bottom')
            <script language="JavaScript">

            </script>
@endsection
