@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-10 col-md-offset-1'>
        <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('cluster/general.page.create.section-title') }}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">

                    {!! Form::model($cluster, ['route' => ['cluster.update', $cluster->id], 'method' => 'PUT']) !!}

                    {!! Form::open(['route' => 'cluster.store']) !!}
                    @include('partials._cluster_form', [
                        'active' => $activeClusterId == $cluster->id ? true : false,
                        'version' => $cluster->version,
                        'userType' => $cluster->user_type
                    ])

                    <div class="form-group">
                        {!! Form::submit( trans('general.button.save'), ['class' => 'btn btn-primary'] ) !!}
                        <a href="{!! route('cluster.index') !!}" title="{{ trans('general.button.cancel') }}" class='btn btn-default'>{{ trans('general.button.cancel') }}</a>
                    </div>

                    {!! Form::close() !!}

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection
