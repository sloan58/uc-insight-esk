@extends('layouts.master')
@section('content')
<div class='row'>
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">IOS Config Generator</h3>
                &nbsp;
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table" class="table">
                            <thead>
                            <tr>
                                <th>File Name</th>
                            </tr>
                            </thead>
                            <tfoot>
                            </tfoot>
                            <tbody>
                            @if(isset($shortNames))
                            @foreach ($shortNames as $shortName)
                            <tr>
                                <td>
                                    <a href="/ios-config-generator/{{$shortName}}">
                                        <div>
                                            {{ $shortName }}
                                        </div>
                                    </a>
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


    <!--    DataTables  -->
    @include('partials._dataTables',['column' => '5'])

    <!-- Optional bottom section for modals etc... -->
    @section('body_bottom')
    @endsection
