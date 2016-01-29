@extends('emails.templates.widgets')

@section('content')

@include('emails.templates.widgets.articleStart')

    <h4 class="secondary"><strong>CUCM None Partition Report</strong></h4>
    <p>Please see the attached report of DN's found in the CUCM 'None' Partition</p>

@include('emails.templates.widgets.articleEnd')

@include('emails.templates.widgets.newfeatureEnd')

@stop