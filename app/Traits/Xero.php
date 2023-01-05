<?php

namespace App\Traits;

trait Xero {

    private $useragent = "Leave Star";
    private $application_type = "Partner";
    private $signatures = array(
        'consumer_key' => "GE9MAHYKMEM9QNTFE4TAIPISU569XP",
        'shared_secret' => "XUSOP0MJOKKXV6SHX4NT3KYLZFD6FQ",
        // API versions
        'core_version' => '2.0',
        'payroll_version' => '1.0',
        'file_version' => '1.0',
    );

    // Get the token from DB
    public function getTokenFromDb($org_id, $accsosft = 1) {
        $tokenTarget = \App\Models\AccountingToken::where(['org_str_id' => $org_id, 'accsoft_id' => $accsosft])->first();

        if (isset($tokenTarget)) {
            $tokenTarget = $this->refreshToken($tokenTarget);
        }
        return $tokenTarget;
        //return $tokenTarget;
    }

    //Save the token to DB
    public function saveTokenToDb($token, $org_id, $accsosft = 1) {
        $tokenTarget = \App\Models\AccountingToken::updateOrCreate([
                    'org_str_id' => $org_id,
                    'accsoft_id' => $accsosft], [
                    'token' => $token['oauth_token'],
                    'secret_token' => $token['oauth_token_secret'],
                    'refresh_token' => $token['oauth_session_handle']
        ]);
        return $tokenTarget;
    }

    public function refreshToken($access_token) {
        $XeroOAuth = $this->getXeroElement($access_token->token, $access_token->secret_token);
        $response = $XeroOAuth->refreshToken($access_token->token, $access_token->refresh_token);

        if ($XeroOAuth->response['code'] == 200) {
            $response = $XeroOAuth->extract_params($XeroOAuth->response ['response']);
            $new_token = $this->saveTokenToDb($response, $access_token->org_str_id);
            return $new_token;
        } else {
            //...   
            return false;
        }
    }

    //Return new Xero element 
    public function getXeroElement($oauth_token, $oauth_token_secret) {
        $XeroOAuth = new \XeroOAuth(array_merge(array(
                    'application_type' => $this->application_type,
                    'user_agent' => $this->useragent
                        ), $this->signatures));
        $XeroOAuth->config ['access_token'] = $oauth_token;
        $XeroOAuth->config ['access_token_secret'] = $oauth_token_secret;
        return $XeroOAuth;
    }

    //Get First Calendar ID from Xero
    //Return array
    public function getCalendarID($XeroOAuth) {
        $response = $XeroOAuth->request('GET', $XeroOAuth->url('PayrollCalendars', 'payroll'), array(), $xml = '', $format = "json");
        if ($XeroOAuth->response['code'] == 200) {
            $calendar = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            if (isset($calendar->PayrollCalendars[0]->PayrollCalendarID)) {
                return $calendar->PayrollCalendars[0]->PayrollCalendarID;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //Get first earning rates from Xero
    //Return array
    public function getEaringRates($XeroOAuth) {
        $response = $XeroOAuth->request('GET', $XeroOAuth->url('PayItems', 'payroll'), array(), $xml = '', $format = "json");
        if ($XeroOAuth->response['code'] == 200) {
            $EaringRates = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            if (isset($EaringRates->PayItems->EarningsRates[0]->EarningsRateID)) {
                return $EaringRates->PayItems->EarningsRates[0]->EarningsRateID;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //Get leave type list from Xero
    //Return array
    public function getLeaveType($XeroOAuth) {
        $response = $XeroOAuth->request('GET', $XeroOAuth->url('PayItems', 'payroll'), array(), $xml = '', $format = "json");
        if ($XeroOAuth->response['code'] == 200) {
            $leavetype = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            return $leavetype->PayItems->LeaveTypes;
        } else {
            return false;
        }
    }

    //Get Single employee details from Xero
    //Return array
    public function getSingleEmployee($XeroOAuth, $xero_id) {
        $response = $XeroOAuth->request('GET', $XeroOAuth->url('Employees/' . $xero_id, 'payroll'), array(), $xml = '', $format = "json");
        if ($XeroOAuth->response['code'] == 200) {
            $employee = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            return $employee->Employees[0]->LeaveBalances;
        } else {
            return false;
        }
    }

    //Get employees list from Xero
    //Return array
    public function getEmployees($XeroOAuth) {
        $response = $XeroOAuth->request('GET', $XeroOAuth->url('Employees', 'payroll'), array('Where' => 'Status=="ACTIVE"'), $xml = '', $format = "xml");
        if ($XeroOAuth->response['code'] == 200) {
            $employee = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            return $employee->Employees;
        } else {
            return false;
        }
    }

    //Get leave application  from Xero
    //Return array
    public function getLeaves($XeroOAuth, $last_check_time = null) {
        if ($last_check_time !== null) {
            $array = [
                'If-Modified-Since' => $last_check_time,
            ];
        } else {
            $array = [];
        }
        $response = $XeroOAuth->request('GET', $XeroOAuth->url('LeaveApplications', 'payroll'), $array, $xml = '', $format = "xml");
        if ($XeroOAuth->response['code'] == 200) {
            $leaves = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            return $leaves->LeaveApplications;
        } else {
            return false;
        }
    }

    //Set the new leave type to Xero
    //Return Array
    public function setLeaveType($XeroOAuth, $params, $org_id) {
        $xml = \App\Models\Serializer::toxml($params, "LeaveTypes", array("LeaveType"));
        $xml = "<PayItems>" . $xml . "</PayItems>";
        $response = $XeroOAuth->request('POST', $XeroOAuth->url('PayItems', 'payroll'), array(), $xml, $format = "json");
        if ($XeroOAuth->response['code'] == 200) {
            $leavetype = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            $response_items = $leavetype->PayItems->LeaveTypes;
            foreach ($response_items as $item) {
                $leavetypeinDb = \App\Models\LeaveType::where([
                            'org_id' => $org_id,
                            'name' => $item->Name,
                            'xero_id' => null
                        ])->first();
                if (isset($leavetypeinDb)) {
                    $leavetypeinDb->xero_id = $item->LeaveTypeID;
                    $leavetypeinDb->save();
                }
            }
        }
    }

    public function setUser($XeroOAuth, $params, $org_id) {
        $xml = \App\Models\Serializer::toxml($params, "Employees", array("Employee"));
        $response = $XeroOAuth->request('POST', $XeroOAuth->url('Employees', 'payroll'), array(), $xml, $format = "xml");
        if ($XeroOAuth->response['code'] == 200) {
            $users = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            $response_items = $users->Employees;

            //get the child tree
            $tree = $this->getChildTree($org_id);
            foreach ($response_items->Employee as $item) {
                $user_org = \App\Models\OrganisationUser::where([
                                    'xero_id' => '',
                                    'birthday' => date_format(date_create($item->DateOfBirth), 'Y-m-d H:i:s'),
                                ])->whereIn('org_str_id', $tree)
                                ->with(['user' => function ($query) use ($item) {
                                        $query->where('name', ($item->FirstName . " " . $item->LastName));
                                    }])->first();

                if (isset($user_org)) {

                    $user_org->xero_id = $item->EmployeeID;
                    $user_org->xero_name = $item->FirstName . ' ' . $item->LastName;
                    $user_org->save();
                }
            }
        }
    }

    public function setLeaveApplication($XeroOAuth, $xml) {
        $response = $XeroOAuth->request('POST', $XeroOAuth->url('LeaveApplications', 'payroll'), array(), $xml, $format = "json");

        if ($XeroOAuth->response['code'] == 200) {
            $leaveapp = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            $id = $leaveapp->LeaveApplications[0]->LeaveApplicationID;
            return $id;
        } else {
            return false;
        }
    }

}
