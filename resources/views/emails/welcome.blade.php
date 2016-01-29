@extends('beautymail::templates.widgets')

@section('content')

@include('beautymail::templates.widgets.articleStart')

    <h4 class="secondary"><strong>CUCM None Partition Report</strong></h4>
    <p>Please see the attached report of DN's found in the CUCM 'None' Partition</p>

@include('beautymail::templates.widgets.articleEnd')

@include('beautymail::templates.widgets.newfeatureEnd')

@stop