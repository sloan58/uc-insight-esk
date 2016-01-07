<!-- Cluster Name Form Input -->
<div class="form-group">
    {!! Form::label('Cluster Name', trans('cluster/general.columns.name')) !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>
<!-- IP Address Form Input -->
<div class="form-group">
    {!! Form::label('Publisher IP Address', trans('cluster/general.columns.ip')) !!}
    {!! Form::text('ip', null, ['class' => 'form-control']) !!}
</div>
<!-- Username Form Input -->
<div class="form-group">
    {!! Form::label('Username', trans('cluster/general.columns.username')) !!}
    {!! Form::text('username', null, ['class' => 'form-control']) !!}
</div>
<!-- Password Form Input -->
<div class="form-group">
    {!! Form::label('Password', trans('cluster/general.columns.password')) !!}
    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '********************']) !!}
</div>
<!-- User Type Form Input -->
<div class="form-group">
    {!! Form::label('UserType', trans('cluster/general.columns.user_type')) !!}
    {!! Form::select('user_type', ['AppUser' => 'Application User', 'User' => 'End User'] , $userType) !!}
</div>
<!-- Version Form Input -->
<div class="form-group">
    {!! Form::label('Version', trans('cluster/general.columns.version')) !!}
    {!! Form::select('version', $versions , $version) !!}
</div>
<!-- Verify Peer Form Input -->
<div class="form-group">
    {!! Form::label('Verify Peer', trans('cluster/general.columns.verify')) !!}
    {!! Form::checkbox('verify_peer', 'verify_peer') !!}
</div>
<!-- Active Form Input -->
<div class="form-group">
    {!! Form::label('Active', trans('cluster/general.columns.active')) !!}
    {!! Form::checkbox('active', 'active', $active) !!}
</div>