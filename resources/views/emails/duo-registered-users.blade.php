@extends('emails.templates.widgets')

@section('content')

@include('emails.templates.widgets.articleStart')

<h4 class="secondary"><strong>Duo Registered Users Report</strong></h4>
<p>Please see the attached Duo Registered Users daily report</p>
<p><b>Note</b> - duo_reports@ao.uscourts.gov is not a monitored account.</p>
<p>If you need assistance with this report, please forward this message with the attachment to martin_sloan@ao.uscourts.gov and fadi_tahan@ao.uscourts.gov.</p>

@include('emails.templates.widgets.articleEnd')

@include('emails.templates.widgets.newfeatureEnd')

@stop