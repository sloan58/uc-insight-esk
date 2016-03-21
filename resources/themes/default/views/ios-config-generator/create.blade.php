@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-10 col-md-offset-1'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Populate IOS Configs for <b>{{$fileName}}</b></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">

                {!! Form::open( ['route' => 'ios-config-generator.store'] ) !!}

                @include('partials._ios-config-generator_form')

                <!-- If the file has no variables, don't show the submit button-->
                @if(!count($viewVariables) == 0)
                <!--  Form Submit -->
                <div class="form-group">
                    {!! Form::submit('Submit', ['class' => 'btn btn-primary form-control']) !!}
                </div>
                @else
                <div class="form-group">
                    <h6>No variables available in this file.</h6>
                </div>
                @endif

                {!! Form::close() !!}

            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->

</div><!-- /.row -->
@endsection
