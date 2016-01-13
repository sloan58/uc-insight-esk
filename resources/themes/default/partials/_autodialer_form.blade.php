<div class="form-group">
    {!! Form::label('Phone Number','Phone Number') !!}
    {!! Form::text('number', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Call Type','Call Type') !!}
    {!! Form::select('type', ['voice' => 'Voice', 'text' => 'Text Message'], 'S', ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('What Should We Say?','What Should We Say?') !!}
    {!! Form::textarea('say', null, ['class' => 'form-control']) !!}
</div>