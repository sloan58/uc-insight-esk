@extends('layouts.master')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Search JFS Sites</h3>
                    <div class="box-tools pull-right">
                        <form action="{{ route('jfs.sites.index')  }}" method="GET">
                            <input type="text" name="search" value=""/>
                            <button type"submit">Search</button>
                        </form>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Site Name</th>
                                    <th>Completed Tasks</th>
                                    <th>Incomplete Tasks</th>
                                </tr>
                                </thead>
                                <tfoot>
                                </tfoot>
                                <tbody>
                                @if(isset($sites))
                                    @foreach($sites as $site)
                                        <tr>
                                            <td>
                                                <a class="sql-link" href="{{ route('jfs.sites.show', [ $site->id ]) }}">
                                                    {{ $site->name }}
                                                </a>
                                            </td>
                                            <td>{{ $site->completedTasks()->count() }}</td>
                                            <td>{{ $site->incompleteTasks()->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $sites->appends(['search' => Input::get('search')])->render() !!}
                            @endif
                        </div> <!-- table-responsive -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>
    <div class="row">
        @foreach($reportData as $flowName => $tasks)
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

    @foreach($reportData as $flowName => $flow)

    <script>

        var ctx = $("#{{ str_replace(' ','',$flowName) }}");

        var data = {
            labels: [
            @foreach($flow as $taskName => $count)

                "{{ $taskName }}",

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
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true,
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