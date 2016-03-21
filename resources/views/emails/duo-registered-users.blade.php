@extends('emails.templates.widgets')

@section('content')

@include('emails.templates.widgets.articleStart')

<h4 class="secondary"><strong>Duo Registered Users Report</strong></h4>
<p>Please see the attached Duo Registered Users daily report</p>
<p>Note - duo_reports@ao.uscourts.gov is not monitored.  Do not reply to this message.</p>

@include('emails.templates.widgets.articleEnd')

@include('emails.templates.widgets.newfeatureEnd')

@stop