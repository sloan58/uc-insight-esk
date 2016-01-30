@extends('layouts.master')

@section('content')
<div class='row'>
    <div class="col-md-8 col-md-offset-3">
        <form method="POST" action="/sql"
              class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <div class="col-sm-8">
                    <textarea type="textarea" id="sqlStatement" name="sqlStatement" placeholder="Enter SQL Statement Here..."
                              class="form-control">{{ $sql or '' }}</textarea>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        Submit Query
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
    <script>

        //Codemirror
        var myCodeMirror = CodeMirror.fromTextArea(sqlStatement, {
            mode: "text/x-mysql",
            lineNumbers: true,
            lineWrapping: true
        });
        myCodeMirror.setSize("100%", 300);
    </script>
@endsection