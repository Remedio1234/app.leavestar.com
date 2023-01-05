<?php
$period[0] = 'Day';
$period[1] = 'Week';
$period[2] = 'Month';
$period[3] = 'Year';

$options[0] = "Accrual On Going";
$options[1] = "Accrual at the end of selected period";
?> 


<div class="content-normal">
    <section class="content-header">
        <h1>
            Leave Accrual Setting
        </h1>
    </section>
    <div class="box box-primary">

        <div class="box-body">
            <div class="row">
                {!! Form::open(['action' => 'LeaveAccrualSettingController@StoreSetting'  ]) !!}

                <!-- Period Field -->
                <div class="form-group col-sm-12">
                    {!! Form::label('leave type id', 'Leave Type:') !!}
                    <?= \App\Models\LeaveType::find($leave_type_id)->name ?>
                </div>

                <!-- Period Field -->
                <div class="form-group col-sm-12">
                    {!! Form::label('period', 'Period:') !!}
                    {!! Form::select('period', $period , null, ['class' => 'form-control']) !!}
                </div>

                <!-- Seconds Field -->
                <div class="form-group col-sm-12">
                    {!! Form::label('seconds', 'Hours:') !!}
                    {!! Form::number('seconds', null, ['class' => 'form-control']) !!}
                </div>


                <!-- Options Field -->
                <div class="form-group col-sm-12">
                    {!! Form::label('options', 'Options:') !!}
                    {!! Form::select('options',$options, null, ['class' => 'form-control']) !!}
                </div>


                <div class="form-group col-sm-12">
                    {!! Form::label('balance', 'Current Balance:') !!}
                    {!! Form::text('balance', null, ['class' => 'form-control' ,'placeholder'=>'Hours']) !!}
                </div>


                <!--Hidden Field for Org ID-->
                <div class="form-group col-sm-12">
                    {!! Form::hidden('org_id', $org_id , ['class' => 'form-control']) !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::hidden('user_id', $user_id , ['class' => 'form-control']) !!}
                </div>

                <div class="form-group col-sm-12">
                    {!! Form::hidden('leave_type_id', $leave_type_id , ['class' => 'form-control']) !!}
                </div>
                <!-- Submit Field -->
                <div class="form-group col-sm-12">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary  ']) !!}

                    <a class=' button-close-right' role='button' >Cancel</a>
                </div>

                <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
                {!! $validator !!}



                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
