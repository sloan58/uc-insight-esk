<!-- Custom Tabs -->
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class=""><a href="#tab_perms" data-toggle="tab" aria-expanded="false">Duo Reporting</a></li>
    </ul>
    <div class="tab-content">

        <div class="tab-pane" id="tab_roles">
            <div class="form-group">
                {!! Form::hidden('selected_roles', null, [ 'id' => 'selected_roles']) !!}
                <div class="input-group select2-bootstrap-append">
                    {!! Form::select('role_search', [], null, ['class' => 'form-control', 'id' => 'duo_user_search',  'style' => "width: 100%"]) !!}
                    <span class="input-group-btn">
                        <button class="btn btn-default"  id="btn-add-role" type="button">
                            <span class="fa fa-plus-square"></span>
                        </button>
                    </span>
                </div>
            </div>
        </div><!-- /.tab-pane -->
    </div><!-- /.tab-content -->
</div>