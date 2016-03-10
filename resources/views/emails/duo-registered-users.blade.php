@extends('emails.templates.widgets')

@section('content')

@include('emails.templates.widgets.articleStart')

<h4 class="secondary"><strong>Duo Registered Users Report</strong></h4>
<p>Please see the Duo Registered Users daily attached reports</p>

@include('emails.templates.widgets.articleEnd')

@include('emails.templates.widgets.newfeatureEnd')

@stop