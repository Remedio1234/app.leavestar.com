<table class="table table-responsive table-striped" id="customHolidays-table">
    <thead>

    <th>Start Date</th>
    <th>End Date</th>
    <th>Name</th>
    <th>Description</th>
    <th colspan="2">Action</th>
</thead>
<tbody>
    @foreach($custom_holidays as $customHoliday)
    <tr>
        <?php
        $setting = \App\Models\OrganisationStructure::find(Session::get('current_org'))->setting_id;
        $format = App\Models\Setting::find($setting)->data_format;
        $array = [
            '1' => 'Y-m-d',
            '2' => 'm/d/Y',
            '3' => 'd/m/Y',
        ];
        ?>
        <td>{!! date ($array[$format],strtotime($customHoliday->start_date))   !!}</td>
        <td>{!! date ($array[$format],strtotime($customHoliday->end_date))   !!}</td>
        <td>{!! $customHoliday->name !!}</td>
        <td>{!! $customHoliday->description !!}</td>
        <td>
            {!! Form::open(['route' => ['customHolidays.destroy', $customHoliday->id], 'method' => 'delete','class'=>'form-render2']) !!}
            <div class='btn-group'>
                {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
                <a href="javascript:return false;" data-href="{!! route('customHolidays.edit',  ['id'=>$customHoliday->id,'org_id'=>$organisationStructure->id ]) !!}" class='  button-open-right'><i class="glyphicon glyphicon-edit"></i></a>
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn-hidden ', 'onclick' => "return confirm('Are you sure?')"]) !!}
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>