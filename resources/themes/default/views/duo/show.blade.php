@extends('layouts.master')

@section('head_extra')
<!-- Select2 css -->
@include('partials._head_extra_select2_css')
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h2 class="box-title">Viewing Duo User - {{$user->username}}</h2>
                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <a href="{{route('duo.user.group.report',[$user->id])}}" class="btn btn-success btn-md">
                            On-Demand Report
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="{{route('duo.user.sync',[$user->id])}}" class="btn btn-warning btn-md">
                            Sync User with Duo API
                        </a>
                    </div>
                    <div class="btn-group">
                        <div class="dropdown">
                            <button class="btn btn-danger btn-md dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                Migrate User Data
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <li><a onclick="confirm_migration()">Migrate Now</a></li>
                                <li><a onclick="set_custom_name()">Set Custom Name</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Real Name</th>
                        <th>Email Address</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Last Sync from Duo API</th>
                    </tr>
                    </thead>
                    <tfoot>
                    </tfoot>
                    <tbody>
                    <tr>
                        <td>{{$user->username}}</td>
                        <td>{{$user->realname}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->status}}</td>
                        <td>{{$user->last_login}}</td>
                        <td>{{$user->updated_at}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Registered Phones</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table">
                    <tbody>
                    @if(count($user->duoPhones()->get()))
                    <tr>
                        <th>Phone ID</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Type</th>
                        <th>Platform</th>
                    </tr>
                    @foreach($user->duoPhones()->get() as $phone)
                    <tr>
                        <td>{{$phone->phone_id}}</td>
                        <td>{{$phone->name}}</td>
                        <td>{{$phone->number}}</td>
                        <td>{{$phone->type}}</td>
                        <td>{{$phone->platform}}</td>
                    </tr>
                    @endforeach
                    @else
                    <h6>No Registered Phones</h6>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Registered Tokens</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-bordered">
                    <tbody>
                    @if(count($user->duoTokens()->get()))
                    <tr>
                        <th>Serial Number</th>
                        <th>Token ID</th>
                        <th>Type</th>
                    </tr>
                    @foreach($user->duoTokens()->get() as $token)
                    <tr>
                        <td>{{$token->serial}}</td>
                        <td>{{$token->token_id}}</td>
                        <td>{{$token->type}}</td>
                    </tr>
                    @endforeach
                    @else
                    <h6>No Registered Tokens</h6>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Registered Reports</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form class="form-horizontal" role="form" method="POST" action="/duo/user/{{$user->id}}">
                    <input type="hidden" name="_method" value="PUT"/>
                    {!! csrf_field() !!}
                    <fieldset>
                        <div class="input-group col-md-8">
                            <select multiple="" class="form-control" id="select-report" name="reports[]">
                                @if(isset($reports))
                                @foreach($reports as $report)
                                @if(in_array($report->id,$user->reports()->lists('id')->toArray()))
                                <option value="{{$report->id}}" selected>{{$report->name}}</option>
                                @else
                                <option value="{{$report->id}}">{{$report->name}}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                           <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Save</button>
                           </span>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Assigned Groups <h6>Note: Groups assigned within Duo itself cannot be removed here.</h6></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form class="form-horizontal" role="form" method="POST" action="/duo/user/groups/{{$user->id}}">
                    <input type="hidden" name="_method" value="PUT"/>
                    {!! csrf_field() !!}
                    <fieldset>
                        <div class="input-group col-md-8">
                            <select multiple="" class="form-control" id="select-group" name="groups[]">
                                @if(count($groups))
                                @foreach($groups as $group)
                                @if(in_array($group->id,$user->duoGroups()->lists('id')->toArray()))
                                <option value="{{$group->id}}" selected>{{$group->name}}</option>
                                @else
                                <option value="{{$group->id}}">{{$group->name}}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                           <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Save</button>
                           </span>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>
@endsection


{{-- Create Migrate Confirmation Modal --}}
<div class="modal fade" id="modal-confirm">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{route('duo.user.migrate',[$user->id])}}"
                  class="form-horizontal">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="folder" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">Confirm data migration:</h4>
                </div>
                <div class="modal-body">
                    Are you sure you want to migrate data for user {{ $user->username }}?
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

{{-- Create Migrate Custom Name Modal --}}
<div class="modal fade" id="modal-custom">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{route('duo.user.migrate',[$user->id])}}"
                  class="form-horizontal">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="folder" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">Set Custom Duo Username</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Username
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="username" name="username" class="form-control">
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

@section('body_bottom')
<script language="JavaScript">

    // Modal Confirm
    function confirm_migration() {
        $("#modal-confirm").modal("show");
    }

    // Modal Custom
    function set_custom_name() {
        $("#modal-custom").modal("show");
    }
</script>

    <!-- Select2 js -->
@include('partials._body_bottom_select2_js_report_search')
@include('partials._body_bottom_select2_js_group_search')
@endsection

