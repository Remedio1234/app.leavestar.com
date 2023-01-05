@extends('layouts.app')

@section('content')
<div class="content">
    <section class="content-header">
        <h1>Manage Leave</h1>
    </section>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">


            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#Manage_Leaves" aria-controls="Manage_Leaves" role="tab" data-toggle="tab">Leave Applications</a></li>
                <li role="presentation"><a href="#Upcoming_Leaves" aria-controls="Upcoming_Leaves" role="tab" data-toggle="tab">Leave History</a></li>

            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="Manage_Leaves"> 

                    <div class="dropdown  pull-right">
                        <button type="button" class="btn btn-block dropdown-toggle menu-widget-text-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span><?php
                                switch ($queryType) {
                                    case "pending":
                                        echo "Pending Leave ";
                                        break;
                                    case "approved":
                                        echo "Approved Leave ";
                                        break;
                                    case "upcoming":
                                        echo "Upcoming Leave ";
                                        break;
                                    default:
                                        echo "Pending Leave ";
                                        break;
                                }
                                ?>  </span><span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <!-- <li><a class="dropdown-item" href="/leaveApplication/manage/?queryType=pending">Pending Leave</a></li> -->
                            <li><a class="dropdown-item" href="/leaveApplication/manage/?queryType=approved">Approved Leave</a></li>
                            <li><a class="dropdown-item" href="/leaveApplication/manage/?queryType=upcoming">Upcoming Leave</a></li>
                        </ul>
                    </div>

                    <div id="leaveType" class="app-entity leave-panel"><?php echo strtoupper($queryType); ?> LEAVE</div>
                    <div id="emptyRecords" style="display: none;" class="app-entity leave-panel">No Records to display</div>
                    @include('leave_applications.table')                 
                </div>
                <div role="tabpanel" class="tab-pane" id="Upcoming_Leaves"> @include('leave_applications.leaveHistory')</div>

            </div>


        </div>
    </div>
</div>


@endsection