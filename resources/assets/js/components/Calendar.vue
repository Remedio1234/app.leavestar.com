<template>
<div class="calendar-filter">
    <div class="row">
        <div class="col-xs-12">
            <h4>FILTER CALENDAR</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <label>Department</label>
            <v-select   :on-change="refreshEvents" :value.sync="selection1" :options="departments"  	placeholder="Search Available Departments..."></v-select>
        </div>
        <div class="col-sm-12 col-md-6">
            <label>Leave Type</label>
            <v-select  :on-change="refreshEvents"  :value.sync="selection2" :options="leaveTypes"  	placeholder="Search Available leaveType..."></v-select>
        </div>
    </div>
    <hr/>
</div>



<full-calendar  v-ref:calendar :event-sources="eventSources" :default-view="defaultView" :custom-buttons="customButtons" :header="header"  >
</full-calendar>
</template>
<script>
    import vSelect from 'vue-select'

    export default {
        name: 'Calendar',
        components: {
            vSelect
        },
        data() {
            return {
                defaultView: "month",
                customButtons: {
                    myCustomButton: {
                        text: 'FILTER',
                        class:'btn-primary',
                        click: function() {
                            $(".calendar-filter").slideToggle();
                        }
                    }
                },
                header: {
//                    left: 'today listYear',
                    left: 'myCustomButton',
                    center: 'prev title next',
                    right: 'month,agendaWeek,agendaDay'
                },
                selection1: null,
                selection2: null,
                departments: this.getDepartments(),
                leaveTypes: this.getleaveTypes(),
            }
        },
        computed: {
            leavetype_q: function() {
                if (this.selection2 !== null) {
                    return this.selection2.value;
                } else {
                    return "";
                }
            },
            department_q: function() {
                if (this.selection1 !== null) {
                    return this.selection1.value;
                } else {
                    return "";
                }
            },
            eventSources: function() {
                return [{
                    url: '/calendar/feed',
                    type: 'Get',
                    data: {
                        leavetype: this.leavetype_q,
                        org: this.department_q,
                    },
                }];
            }
        },
        methods: {
            refreshEvents: function() {

                $(this.$refs.calendar.$el).fullCalendar('removeEvents');
                $(this.$refs.calendar.$el).fullCalendar('removeEventSources');
                this.eventSources.map(event => {
                    $(this.$refs.calendar.$el).fullCalendar('addEventSource', event)
                });

                //this.$refs.calendar.$el.$emit('rebuild-sources');
            },
            getleaveTypes: function() {
                var url = "/calendar/leaveType";
                this.$http.get(url).then((response) => {
                    if (!!response.body) {
                        this.leaveTypes = JSON.parse(response.body);
                    }
                }, (response) => {
                    return response;
                });

            },
            getDepartments: function() {
                var url = "/calendar/department";
                this.$http.get(url).then((response) => {
                    if (!!response.body) {
                        this.departments = JSON.parse(response.body);
                    }
                }, (response) => {
                    return response;
                });

            }
        },
    }
</script>
