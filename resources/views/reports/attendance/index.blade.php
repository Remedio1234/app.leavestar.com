@extends('layouts.app')

@section('content')

<div class="content">
    <section class="content-header">
        <h1 class="pull-left">Reports</h1>
        <h1 class="pull-right">
            <!--           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('organisationUsers.create') !!}">Invite</a>-->
            <!-- <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('userRegisters.create') !!}">Invite</a> -->
        </h1>
    </section>
    <div class="clearfix"></div>

    <!-- @include('flash::message') -->

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">


            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#current_user" aria-controls="current_user" role="tab" data-toggle="tab">Attendance</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="current_user">
                <div class="container-fluid">
                    <!-- <div class="row">
                        <div style="float:right;">
                            <button type="button" class="btn btn-primary" id="btnExport">
                                <span class="fa fa-download"></span> Export
                            </button>
                        </div>
                    </div> -->
                    <div class="row"  style="margin-bottom:20px;">
                        <div class="col-md-2" style="padding-left:0px;">
                        Date From:
                        <input
                            readonly
                            class="form-control" 
                            type="text" 
                            name="date_from" 
                            value="<?php echo date("m/d/Y", strtotime("-1 months"));?>" 
                        />
                        </div>
                        <div class="col-md-2">
                        Date To:
                        <input
                            readonly
                            class="form-control" 
                            type="text" 
                            name="date_to" 
                            value="<?php echo date('m/d/Y')?>" 
                        />
                        </div>
                        <div class="col-md-2">
                            <!-- <br>
                        <button type="button" class="btn btn-primary" id="btnSearch">
                                <span class="fa fa-search"></span> Search
                            </button> -->
                        </div>
                        <div class="col-md-6">
                            <br/>
                            <button type="button" class="btn btn-primary" id="btnExport" style="float:right;">
                                <span class="fa fa-download"></span> Export
                            </button>
                        </div>
                    </div>
                </div>
                    <table class="table table-responsive table-striped" id="attendance_table">
                        <thead>
                            <tr style="display:none;">
                                <th>From:</th>
                                <th align="left"><span id="txt_from"><?php echo date('d/m/Y')?></span></th>
                                <th>To:</th>
                                <th align="left"> <span id="txt_to"><?php echo date('d/m/Y')?></span></th>
                                <th>&nbsp;</th>
                            </tr>
                            <tr  style="display:none;">
                                <td colSpan="5"></td>
                            </tr>
                            <tr>
                                <th>Number</th>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Balance</th>
                                <th>Taken</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

