@extends('layouts.master')

@section('content')
    <div id="console-wrapper">
        <p class="console">
            <span id="login" class="termPrompt">{{\Auth::user()->username}}@uc-insight:~$</span><span id="caption"></span><span id="cursor">|</span>
        </p>
    </div>
@endsection

@section('body_bottom')
<script>
    var termPrompt = $('span#login').text();
    var newLine = $('span#login').clone();
    var captionLength = 0;
    var caption = 'Welcome to UC Insight.</br>';

    $(document).ready(function() {
        setInterval ('cursorAnimation()', 600);
        captionEl = $('#caption');

        if(window.location.pathname == '/')
        {
            type();
        } else {
            captionEl.html(caption);
            captionEl.append(newLine);
        }
    });

    function type() {
        captionEl.html(caption.substr(0, captionLength++));
        if(captionLength < caption.length+1) {
            setTimeout('type()', 50);
        } else {
            captionLength = 0;
            caption = '';
            captionEl.append(newLine);
            $('body').removeClass('sidebar-collapse');

        }
    }

    function cursorAnimation() {
        $('#cursor').animate({
            opacity: 0
        }, 'fast', 'swing').animate({
            opacity: 1
        }, 'fast', 'swing');
    }
</script>
@stop