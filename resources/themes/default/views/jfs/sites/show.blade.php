@extends('layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-10 col-md-offset-1'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Showing progress for JFS Site <b>{{ $site->name }}</b></h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">

                    @include('partials._jfs-site-status_form')

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection

@section('body_bottom')
    <script>
        function save_checkbox(site_id, task_id) {
            $.get( '{{ route('jfs.site.task.update') }}' , { site : site_id, task : task_id },
            function( response ) {
//                alert(response);
                console.log(response);
                $( "#result" ).html( response );
            }
        );
        }
    </script>
@endsection