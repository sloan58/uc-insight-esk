@extends('layouts.master')

@section('head_extra')
<!-- Select2 css -->
@include('partials._head_extra_select2_css')
@endsection

@section('content')
<div class='row'>
    <div class='col-md-8 col-md-offset-2'>
        <div class="row">
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h2 class="box-title">Viewing Duo User - {{$user->realname}}</h2>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
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
                            <tr>
                                <td>{{$user->realname}}</td>
                                <td>{{$user->username}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->status}}</td>
                                <td>{{$user->last_login}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
</div>
<div class='row'>
    <div class='col-md-8 col-md-offset-2'>
        <div class="row">
            <div class="col-md-10">
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
    </div>
</div>
<div class='row'>
    <div class='col-md-8 col-md-offset-2'>
        <div class="row">
            <div class="col-md-10">
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
    </div>
</div>

<div class='row'>
    <div class='col-md-8 col-md-offset-2'>
        <div class="row">
            <div class="col-md-10">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Registered Reports</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tbody>
                            @if(count($user->reports()))
                            <tr>
                                <th>Report Name</th>
                            </tr>
                            @foreach($user->reports() as $report)
                                <tr>
                                    <td>{{$report->name}}</td>
                                </tr>
                            @endforeach
                            @else
                                <h6>No Report Subscriptions</h6>
                            @endif
                            </tbody>
                        </table>
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
    </div>
</div>
@endsection

@section('body_bottom')
<!-- Select2 js -->
@include('partials._body_bottom_select2_js_report_search')
@endsection

