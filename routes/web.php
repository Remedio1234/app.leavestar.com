<?php
//if (env('APP_ENV') === 'production') {
    URL::forceSchema('https');
//}
/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | This file is where you may define all of the routes that are handled
  | by your application. Just tell Laravel the URIs it should respond
  | to using a Closure or controller method. Build something great!
  |
 */

Route::get('/', function () {
    return redirect('home');
});


Auth::routes();

Route::get('/home', 'HomeController@index');

//Route specific for organisation switch function
Route::get('/home/changeorg/{org_id}', 'HomeController@changeOrg');
Route::get('/home/terminateAccount', 'HomeController@terminateAccount');
Route::get('/setNotificationRead/', 'HomeController@setNotificationRead');
Route::get('/home/end-guide', 'HomeController@endGuide');

// route for org structure change 
Route::post('/organisationStructures/dataupdate', 'OrganisationStructureController@dataUpdate');
Route::get('/organisationUser/enableTour', 'OrganisationUserController@enableTour');
Route::get('/organisationStructures/create/{id}', 'OrganisationStructureController@create');
Route::get('/organisationStructures/datadelete/{id}', 'OrganisationStructureController@dataDelete');

//calendar
Route::get('/calendar/feed', 'CalendarController@feed');
Route::get('/calendar/leaveType', 'CalendarController@leaveType');
Route::get('/calendar/department', 'CalendarController@Department');
Route::get('/calendar/employee', 'CalendarController@Employee');
Route::get('/calendar/download', 'CalendarController@DownloadFeed');

//normal resource route
//Route::resource('settings', 'SettingController');
Route::resource('organisationStructures', 'OrganisationStructureController');
Route::resource('organisationUsers', 'OrganisationUserController');
Route::resource('accounts', 'AccountController');
Route::resource('leaveTypes', 'LeaveTypeController');
Route::resource('blockDates', 'BlockDateController');
Route::resource('customHolidays', 'CustomHolidayController');
Route::resource('sickLeaves', 'SickLeaveController');
Route::resource('leaveApplications', 'LeaveApplicationController');
Route::resource('comments', 'CommentController');
Route::resource('openHours', 'OpenHourController');
//Route::resource('accountingTokens', 'AccountingTokenController'); 
Route::resource('userRegisters', 'UserRegisterController');
Route::resource('leaveAccrualSettings', 'LeaveAccrualSettingController');
Route::resource('leaveCapacities', 'LeaveCapacityController');
Route::resource('registerCapacities', 'RegisterCapacityController');
Route::resource('customizedFeeds', 'customizedFeedController');
Route::resource('accountingSoftwares', 'accountingSoftwareController');

// sepical route for setting creation
Route::post('/leaveTypes/create/', 'LeaveTypeController@create');
Route::post('/blockDates/create/', 'BlockDateController@create');
Route::post('/customHolidays/create/', 'CustomHolidayController@create');
Route::post('/sickLeaves/create/', 'SickLeaveController@create');
Route::post('/openHours/create/', 'OpenHourController@create');
Route::post('/leaveAccrualSettings/create/', 'LeaveAccrualSettingController@create');

// special route for leave applciation creation
Route::post('/leaveApplications/create/', 'LeaveApplicationController@create');

Route::get('/leaveApplication/manage/', 'LeaveApplicationController@manageLeave');
Route::get('/leaveApplication/myLeaves/', 'LeaveApplicationController@myLeaveApplication');
Route::get('/leaveApplication/check-application/', 'LeaveApplicationController@checkApplication');


//Xero AU
Route::get('/xero/synchronizeuser/{org_id}', 'XeroController@SynchronizeUser');
Route::get('/xero/synchronizelt/{org_id}', 'XeroController@SynchronizeLT');
Route::get('/xero/callback/', 'XeroController@CallBack');
Route::get('/xero/connect/', 'XeroController@XeroConntect');
Route::get('/xero/', 'XeroController@index');
Route::get('/xero/disconnect/', 'XeroController@Disconnect');
Route::get('/xero/skip/', 'XeroController@SkipOne');
Route::get('/xero/matching-leavetype/', 'XeroController@MatchingLeaveType');
Route::get('/xero/matching-user/', 'XeroController@MatchingUser');
Route::get('/xero/ltcomplete/', 'XeroController@LTcomplete');
Route::get('/xero/usercomplete/', 'XeroController@Usercomplete');
Route::post('xero/invite/', 'XeroController@Invite');
Route::get('/xero/synchronizeapp/{org_id}', 'XeroController@SynchronizeLeaveApp');


//Xero USA
Route::get('/xerousa/synchronizeuser/{org_id}', 'XeroUsaController@SynchronizeUser');
Route::get('/xerousa/synchronizelt/{org_id}', 'XeroUsaController@SynchronizeLT');
Route::get('/xerousa/callback/', 'XeroUsaController@CallBack');
Route::get('/xerousa/connect/', 'XeroUsaController@XeroConntect');
Route::get('/xerousa/skip/', 'XeroUsaController@SkipOne');
Route::get('/xerousa/matching-leavetype/', 'XeroUsaController@MatchingLeaveType');
Route::get('/xerousa/matching-user/', 'XeroUsaController@MatchingUser');
Route::get('/xerousa/ltcomplete/', 'XeroUsaController@LTcomplete');
Route::get('/xerousa/usercomplete/', 'XeroUsaController@Usercomplete');
Route::post('xerousa/invite/', 'XeroUsaController@Invite');


Route::get('/userRegisters/registerFromToken/{token}', 'UserRegisterController@RegisterFromToken');
Route::post('/userRegisters/register', 'UserRegisterController@Register');


Route::get('/gmail/connect', 'MailSettingController@GmailConnect');
Route::get('/gmail/callback', 'MailSettingController@GmailCallBack');
Route::get('/outlook/connect', 'MailSettingController@OutlookConnect');
Route::get('/outlook/callback', 'MailSettingController@OutlookCallBack');

Route::get('/account/signup', 'AccountController@Signup');


Route::get('/userRegister/returnlist/', 'UserRegisterController@ReturnList');

Route::get('/leaveAccrualSetting/new/', 'LeaveAccrualSettingController@NewSetting');
Route::post('/leaveAccrualSetting/save/', 'LeaveAccrualSettingController@StoreSetting');
Route::get('/leaveAccrualSetting/edit/', 'LeaveAccrualSettingController@EditSetting');
Route::post('/leaveAccrualSetting/update/', 'LeaveAccrualSettingController@UpdateSetting');

Route::get('/sickLeave/render-partical', 'SickLeaveController@renderPartical');

Route::get('/organisationUser/editUser', 'OrganisationUserController@editUser');
Route::get('/organisationUser/deleteProfile', 'OrganisationUserController@deleteProfile');
Route::get('/organisationUser/editEmail', 'OrganisationUserController@editEmail');
Route::patch('/organisationUser/updateUser', 'OrganisationUserController@updateUser');
Route::get('/organisationUser/getEmail', 'OrganisationUserController@getEmail');
Route::get('/organisationUser/removeXerotoken/{id}', 'OrganisationUserController@removeXerotoken');


Route::get('/leaveCapacity/checkcapacity/{id}', 'LeaveCapacityController@CheckCapacity');

Route::get('/weeklyNotifications/manage/', 'WeeklyNotificationsController@isWeeklyReport');


Route::get('/image/{filename}', function ($filename) {
    $path = storage_path('app/image/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

//Auto running script
Route::get('/leaveAccrualSetting/autoAccrualing/{token}', 'LeaveAccrualSettingController@AutoAccrualling');
Route::get('/account/weeklycheck/{token}', 'AccountController@WeeklyCheck');
Route::get('/leaveApplication/crudCheck', 'LeaveApplicationController@crudMatchHalfDayLeave');
Route::get('/weeklyNotifications/send/{token}', 'WeeklyNotificationsController@send');


Route::get('/test-mail', function () {
	$user = \App\User::find(74);
    $message = "There is nothing upcoming this week.";
    $user->notify(new \App\Notifications\WeeklyNotification($message));
});
Route::get('/xero/test', 'XeroController@test');

// Reports
Route::get('reports', 'ReportsController@index');
Route::get('reports/attendance', 'ReportsController@getAttendanceReports');
Route::post('reports/leave-taken', 'ReportsController@updateLeaveTaken');