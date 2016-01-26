@extends('layouts.master')

@section('content')
<div class='row'>
    <div class='col-md-12'>
        <!-- Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('eraser/bulk/general.page.index.table-title') }}</h3>
                &nbsp;
                <div class="box-body">

                    <div class="table-responsive" id="vue-table">
                        <table id="table" class="table table-striped row-border">
                            <thead>
                            <tr>
                                <th>Phone Name</th>
                                <th>IP Address</th>
                                <th>Type</th>
                                <th>Result</th>
                                <th>Fail Reason</th>
                                <th>Sent On</th>
                            </tr>
                            </thead>
                            <tbody v-for="eraser in bulks.erasers">
                            <tr>
                                <td>@{{ eraser.device.name }}</td>
                                <td>@{{ eraser.ip_address.ip_address }}</td>
                                <td>@{{ eraser.type }}</td>
                                <td >
                                    <i class="@{{ eraser.result == 'Success' ? 'fa fa-check' : 'fa fa-times' }}"></i>
                                </td>
                                <td>@{{ eraser.fail_reason}}</td>
                                <td>@{{ eraser.created_at}}</td>
                            </tr>
                            </tbody>
                        </table>
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
    <script>
        new Vue({
            el: '#vue-table',
            data: {
                bulks: []
            },
            ready: function() {
                this.$http.get('/api/v1/eraser/bulk/{{$bulk->id}}', function(bulks) {
                    this. bulks = bulks;
                    console.log(this.bulks);
                }.bind(this));
            }
        })
    </script>
    @endsection
