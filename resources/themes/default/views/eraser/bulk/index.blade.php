@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-10 col-md-offset-1'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('eraser/bulk/general.page.index.table-title') }}</h3>
                &nbsp;
                <div class="box-tools pull-right">
                    <div class="col-md-6 text-right">
                        <a type="button" class="btn btn-success btn-md" href="{{ route('eraser.bulk.create') }}" role="button">
                            <i class="fa fa-plus-circle fa-lg"></i>
                            Erase in Bulk
                        </a>
                    </div>
                </div>
                <div class="box-body" id="vue-table">

                    <div class="table-responsive">
                        <table id="table" class="table table-striped row-border">
                            <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Process ID</th>
                                <th>Phones Processed</th>
                                <th>Result</th>
                                <th>Submitted</th>
                            </tr>
                            </thead>
                            <tbody v-for="bulk in bulks">
                            <tr>
                                <td>
                                    <a href="/bulk/@{{ bulk.id }}">
                                        <div>@{{ bulk.file_name }}</div>
                                    </a>
                                </td>
                                <td>@{{ bulk.process_id }}</td>
                                <td>@{{ bulk.erasers.length }}</td>
                                <td>@{{ bulk.result }}</td>
                                <td>@{{ bulk.created_at }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div> <!-- table-responsive -->

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->

    </div><!-- /.row -->
    @endsection

    <!--    DataTables  -->
    @include('partials._dataTables',['column' => '4'])

    <!-- Optional bottom section for modals etc... -->
    @section('body_bottom')

    <script>
        new Vue({
            el: '#vue-table',
            data: {
                bulks: []
            },
            ready: function() {
                this.$http.get('/api/v1/eraser/bulk', function(bulks) {
                    this. bulks = bulks;
                    console.log(this.bulks);
                }.bind(this));
            }
        })
    </script>
    @endsection
