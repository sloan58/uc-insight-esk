@if(isset($viewVariables))
@foreach($viewVariables as $viewVar)
<div class="form-group">

    {!! Form::label($viewVar[1],$viewVar[1]) !!}
    {!! Form::text($viewVar[0], null, ['class' => 'form-control']) !!}
</div>
<div>
    <input type="hidden" name="fileName" value="{{$fileName}}"/>
</div>
@endforeach
@endif