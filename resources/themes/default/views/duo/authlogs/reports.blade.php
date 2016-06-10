@extends('layouts.master')

@section('content')
    <div class="row">
        @foreach($graphData as $flowName => $tasks)
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ ucfirst($flowName) }}</h3>

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
                        label: 'Percentage',
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