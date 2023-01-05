<?php

namespace App\Traits;

trait XeroUSA {

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
    public function getTokenFromDb($org_id, $accsosft = 2) {
        $tokenTarget = \App\Models\AccountingToken::where(['org_str_id' => $org_id, 'accsoft_id' => $accsosft])->first();

        if (isset($tokenTarget)) {
            $tokenTarget = $this->refreshToken($tokenTarget);
        }
        return $tokenTarget;
        //return $tokenTarget;
    }

    //Save the token to DB
    public function saveTokenToDb($token, $org_id, $accsosft = 2) {
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

    //Get leave type list from Xero
    //Return array
    public function getTimeoffType($XeroOAuth) {
        $response = $XeroOAuth->request('GET', $XeroOAuth->url('PayItems', 'payroll'), array(), $xml = '', $format = "json");
        if ($XeroOAuth->response['code'] == 200) {
            $leavetype = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            return $leavetype->PayItems->TimeOffTypes;
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
            return $employee->Employees[0]->TimeOffBalances;
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

}
