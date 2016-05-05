@if(isset($viewVariables))
@foreach($viewVariables as $key => $section)
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title config-panel-title">{{$key}}</h3>
    </div>
    <div class="panel-body">
        @foreach($section as $viewVar)
        <div class="form-group">
            {!! Form::label($viewVar[1],$viewVar[1]) !!}
            {!! Form::text($viewVar[0], null, ['class' => 'form-control']) !!}
        </div>
        @endforeach
    </div>
</div>
<div>
    <input type="hidden" name="fileName" value="{{$fileName}}"/>
</div>
@endforeach
@endif