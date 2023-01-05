<table class="table table-responsive table-striped" id="settings-table">
    <thead>
    <th>Logo</th>
    <th>Timezone</th>
    <th>Leave Rules</th>
    <th>Leave Type</th>
    <th>Block Date</th>
    <th>Custom Holidays</th>
    <th>Sick Leave</th>
    <th colspan="3">Action</th>
</thead>
<tbody>
    @foreach($settings as $setting)
    <tr>
        <td>{!! $setting->logo !!}</td>
        <td>{!! $setting->timezone !!}</td>
        <td>{!! $setting->leave_rules !!}</td>
        <td>{!! $setting->leave_type !!}</td>
        <td>{!! $setting->block_date !!}</td>
        <td>{!! $setting->custom_holidays !!}</td>
        <td>{!! $setting->sick_leave !!}</td>
        <td>
            {!! Form::open(['route' => ['settings.destroy', $setting->id], 'method' => 'delete']) !!}
            <div class='btn-group'>
                <a href="{!! route('settings.show', [$setting->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                <a href="{!! route('settings.edit', [$setting->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>

<div class="pagination"> {{ $settings->links() }} </div>