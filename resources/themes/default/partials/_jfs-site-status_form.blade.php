@foreach($site->workflows as $workflow)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title config-panel-title">{{ $workflow->name }}</h3>
        </div>
        <div class="panel-body">
            @foreach($workflow->tasks as $task)
            <ul>
                <li>{{ $task->name }}
                    <input type="checkbox"
                    @if($site->tasks()->where('id',$task->id)->first()->pivot->completed)
                        checked
                    @endif
                        onclick="save_checkbox('{{ $site->id }}', '{{$task->id }}')">
                    <br>
                    {{--<i class="fa fa-check" aria-hidden="true"></i>--}}
                </li>
            </ul>
            @endforeach
        </div>
    </div>
@endforeach

