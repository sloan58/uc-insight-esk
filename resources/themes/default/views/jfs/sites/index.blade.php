@extends('layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Search JFS Sites</h3>
                    <div class="box-tools pull-right">
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Completed Tasks</th>
                                    <th>Incomplet Tasks</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Completed Tasks</th>
                                    <th>Incomplete Tasks</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div> <!-- table-responsive -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>
    <div class="row">
        @foreach($graphData as $flowName => $tasks)
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $flowName }}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <canvas id="{{ str_replace(' ','',$flowName) }}" style="height: 352px; width: 704px;" width="1408" height="704"></canvas>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        @endforeach
    </div>
@endsection

@section('body_bottom')

    {{-- Datatables serverSide --}}
    <script>
        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('jfs.sites.index.data') !!}',
            columns: [
                {data: 'name', name: 'name'},
                {data: 'Completed Tasks', name: 'Completed Tasks', searchable: false},
                {data: 'Incomplete Tasks', name: 'Incomplete Tasks', searchable: false},
            ]
        });
    </script>

    @foreach($graphData as $flowName => $flow)

    <script>

        var ctx = $("#{{ str_replace(' ','',$flowName) }}");

        var data = {
            labels: [
            @foreach($flow as $taskName => $count)
                "{!! $taskName !!}",
            @endforeach
        ],
        datasets: [
        {
            label: 'Percentage of Sites Completed',
            data: [
            @foreach($flow as $taskName)
                    {{ $taskName['count'] }},
            @endforeach
        ],
            backgroundColor: [
            @foreach($flow as $taskName)
                "{{ $taskName['backgroundColor'] }}",
            @endforeach

            ],
            hoverBackgroundColor: [
            @foreach($flow as $taskName)
                "{{ $taskName['hoverBackgroundColor'] }}",
            @endforeach
            ]
            }]
        };

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                scaleLabel: {
                    display: true
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true,
                            fontStyle: "bold"
//                            max:100
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            beginAtZero:true,
                            fontStyle: "bold"
//                            max:100
                        }
                    }]
                },
                legend: {
                    display: false
                }
            }
        });

    </script>

    @endforeach
@endsection