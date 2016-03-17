@if(isset($sections))
    @foreach($sections as $section)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title ios-config-header"><b>{{$section['Header']}}</b></h3>
        </div>
        <div class="panel-body">
            @foreach($section['Vars'] as $var)
            <div class="form-group">
                {!! Form::label($var[1],$var[1]) !!}
                {!! Form::text($var[0], null, ['class' => 'form-control']) !!}
            </div>
            <div>
                <input type="hidden" name="fileName" value="{{$fileName}}"/>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
@endif