<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\Traits\MailSetting;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use App\Http\Controllers\AppBaseController;

class MailSettingController extends AppBaseController {

    use MailSetting;

    private function getSuccessGmail() {
        return "Gmail Account Linked.";
    }

    private function getSuccessOutlook() {
        return "Outlook Account Linked.";
    }

    public function GmailConnect() {
        $client = new \Google_Client();
        $client->setAuthConfig(app_path('certs/client_id.json'));
        $client->setApprovalPrompt('force');
        $client->setAccessType("offline");        // offline access
        $client->setIncludeGrantedScopes(true);   // incremental auth
        $client->addScope('https://www.googleapis.com/auth/gmail.settings.basic');
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/gmail/callback');
        $auth_url = $client->createAuthUrl();
        redirect()->to($auth_url)->send();
    }

    //Must have https
    public function GmailCallBack(Request $request) {
        if (isset($_GET)) {
            if (isset($_GET['code'])) {
                $client = $this->getGamil();
                $client->authenticate($_GET['code']);
                $org_id = $request->session()->get('current_org');
                $user_org = \App\Models\OrganisationUser::where([
                            'org_str_id' => $org_id,
                            'user_id' => \Auth::user()->id
                        ])->first();
                $this->saveRefreshToken($user_org, $client->getAccessToken()['refresh_token'], 'gmail');
                return redirect('home')->with('status', $this->getSuccessGmail());
            } else {
                return redirect('/');
            }
        } else {
            return view('errors.403');
        }
    }

    public function OutlookConnect() {
        $outlook = $this->getOutlook();
        $authorizationUrl = $outlook->getAuthorizationUrl();
        redirect()->to($authorizationUrl)->send();
    }

    public function OutlookCallBack(Request $request) {
        $outlook = $this->getOutlook();
        $accessToken = $outlook->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        $org_id = $request->session()->get('current_org');
        $user_org = \App\Models\OrganisationUser::where([
                    'org_str_id' => $org_id,
                    'user_id' => \Auth::user()->id
                ])->first();
       
        $this->saveRefreshToken($user_org, $accessToken->getRefreshToken(), 'outlook');
        return redirect('home')->with('status', $this->getSuccessOutlook());
    }

}
