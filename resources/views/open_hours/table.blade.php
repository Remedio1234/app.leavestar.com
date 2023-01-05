
<?php
$weeklist = [
    '1' => 'Monday',
    '2' => 'Tuesday',
    '3' => 'Wednesday',
    '4' => 'Thurday',
    '5' => 'Friday',
    '6' => 'Saturday',
    '7' => 'Sunday',
];
?>
<table class="table table-responsive table-striped" id="openHours-table">
    <thead>

    <th>Day of the week</th>
    <th>Start time</th>
    <th>End time</th>
    <th>Number of hours</th>
    <th>Breaks(In Minutes)</th>
    <th colspan="2">Action</th>
</thead>
<tbody>
    @foreach($openHours as $openHour)
    <tr>

        <td>{!! $weeklist [$openHour->dayOfWeek] !!}</td>
        <td>{!! $openHour->start_time !!}</td>
        <td>{!! $openHour->end_time !!}</td>
        <td>{!! $openHour->numOfHours !!}</td>
        <td>{!! ($openHour->breakHours)*60+$openHour->breakMins !!}</td>
        <td>
            {!! Form::open(['route' => ['openHours.destroy', $openHour->id], 'method' => 'delete','class'=>'form-render2']) !!}
            <div class='btn-group'>
                <a href="javascript:return false;" data-href="{!! route('openHours.edit', ['id'=>$openHour->id,'org_id'=>$organisationStructure->id ]) !!}" class='  button-open-right'><i class="glyphicon glyphicon-edit"></i></a>
                {!! Form::hidden('org_id',$organisationStructure->id , ['class' => 'form-control']) !!}
                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => ' btn-hidden', 'onclick' => "return confirm('Are you sure?')"]) !!}
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</tbody>
</table>
