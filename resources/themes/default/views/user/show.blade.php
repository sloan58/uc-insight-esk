@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')
@endsection

@section('content')
    <div class='row'>
        <div class='col-md-10 col-md-offset-1'>
            <div class="box-body">

                {!! Form::model($user, ['route' => 'admin.users.index', 'method' => 'GET']) !!}

                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_details" data-toggle="tab" aria-expanded="true">{!! trans('general.tabs.details') !!}</a></li>
                    </ul>
                    <div class="tab-content">

                        <div class="tab-pane active" id="tab_details">
                            <div class="form-group">
                                {!! Form::label('first_name', trans('admin/users/general.columns.first_name')) !!}
                                {!! Form::text('first_name', null, ['class' => 'form-control', 'readonly']) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('last_name', trans('admin/users/general.columns.last_name')) !!}
                                {!! Form::text('last_name', null, ['class' => 'form-control', 'readonly']) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('username', trans('admin/users/general.columns.username')) !!}
                                {!! Form::text('username', null, ['class' => 'form-control', 'readonly']) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('email', trans('admin/users/general.columns.email')) !!}
                                {!! Form::text('email', null, ['class' => 'form-control', 'readonly']) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('password', trans('admin/users/general.columns.password')) !!}
                                {!! Form::password('password', ['class' => 'form-control', 'readonly']) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('password_confirmation', trans('admin/users/general.columns.password_confirmation')) !!}
                                {!! Form::password('password_confirmation', ['class' => 'form-control', 'readonly']) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('auth_type', trans('admin/users/general.columns.type')) !!}
                                {!! Form::text('auth_type', null, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                        </div><!-- /.tab-pane -->


                <div class="form-group">
                    {!! Form::submit(trans('general.button.close'), ['class' => 'btn btn-primary']) !!}
                    @if ($user->isEditable())
                        <a href="{!! route('users.edit', $user->id) !!}" title="{{ trans('general.button.edit') }}" class='btn btn-default'>{{ trans('general.button.edit') }}</a>
                    @endif
                </div>

                {!! Form::close() !!}

            </div><!-- /.box-body -->
        </div><!-- /.col -->

    </div><!-- /.row -->

@endsection

@section('body_bottom')
    <!-- Select2 js -->
    @include('partials._body_bottom_select2_js_role_search')
@endsection
