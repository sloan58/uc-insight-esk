@foreach($site->workflows as $workflow)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title config-panel-title">{{ $workflow->name }}</h3>
        </div>
        <div class="panel-body">
            @foreach($workflow->tasks as $task)
            <ul>
                @if(\Auth::user()->hasRole(['admins','jfs-admin']))
                    <li>{{ $task->name }}
                        <input type="checkbox"
                        @if($site->tasks()->where('id',$task->id)->first()->pivot->completed)
                               checked
                               @endif
                               onclick="save_checkbox('{{ $site->id }}', '{{$task->id }}')">
                        <br>
                        {{--<i class="fa fa-check" aria-hidden="true"></i>--}}
                    </li>
                @else
                    <li>{{ $task->name }}
                        @if($site->tasks()->where('id',$task->id)->first()->pivot->completed)
                            <i class="fa fa-check task-complete" aria-hidden="true">Completed</i>
                            <br>
                        @endif
                    </li>
                @endif

            </ul>
            @endforeach
        </div>
    </div>
@endforeach

