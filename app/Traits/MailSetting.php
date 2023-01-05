<?php

namespace App\Traits;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

trait MailSetting {

    public function dateFormat($date) {
        $date2 = date_create($date);
        return date_format($date2, 'Y-m-d');
    }

    public function getLeaveMessage($leaveapp) {
        $default = "<html>\n<body>\n<p>I will be out of office during " . $this->dateFormat($leaveapp->start_date) . " and " . $this->dateFormat($leaveapp->end_date) . ". I will get back to you as soon as possible.\n Cheers. <br>\n</p></body>\n</html>\n";
        if ($leaveapp->autoreplymessage == null) {
            return $default;
        } else {
            return $leaveapp->autoreplymessage;
        }
    }

    public function getOutlook() {
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => 'c42a12fd-e378-4cfb-8ffd-bac6a61fac08',
            'clientSecret' => 'YRU98gW3u8j8fjeNud55WSn',
            'redirectUri' => \URL::to('/') . '/outlook/callback',
            'urlAuthorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => '',
            'scopes' => 'offline_access MailboxSettings.ReadWrite'
        ]);
        return $provider;
    }

    public function getGamil() {
        $client = new \Google_Client();
        $client->setAuthConfig(app_path('certs/client_id.json'));
        $client->setAccessType("offline");
        $client->addScope('https://www.googleapis.com/auth/gmail.settings.basic');
        return $client;
    }

    public function saveRefreshToken($org_user, $refreshtoken, $provider) {
        $org_user->refresh_token = $refreshtoken;
        $org_user->email_provider = $provider;
        $org_user->save();
        return $org_user;
    }

    public function postVacationOutlook($leaveapp, $org_user, $status, $provider = "outlook") {
        $refresh_token = $org_user->refresh_token;
        $outlook = $this->getOutlook();
        $newToken = $outlook->getAccessToken('refresh_token', [
            'refresh_token' => $refresh_token
        ]);
        $this->saveRefreshToken($org_user, $newToken->getRefreshToken(), $provider);
        $token = $newToken->getToken();
        $graph = new Graph();
        $graph->setAccessToken($token);
        if ($status == 1) {
            $array = array(
                'status' => 'Scheduled',
                'internalReplyMessage' => $this->getLeaveMessage($leaveapp),
                'externalReplyMessage' => $this->getLeaveMessage($leaveapp),
                'scheduledStartDateTime' => array(
                    'dateTime' => $leaveapp->start_date,
                ),
                'scheduledEndDateTime' => array(
                    'dateTime' => $leaveapp->end_date,
                ),
            );
        } else {
            $array = array(
                'status' => 'Disabled',
            );
        }
        $new_setting = new Model\MailboxSettings();
        $new_setting->setAutomaticRepliesSetting($array);
        $setting = $graph->createRequest('PATCH', '/me/MailboxSettings')
                ->attachBody($new_setting)
                ->setReturnType(Model\User::class)
                ->execute();
        
    }

    public function postVacationGmail($leaveapp, $org_user, $status, $provider = "gmail") {
        $refresh_token = $org_user->refresh_token;
        $client = $this->getGamil();
        $client->refreshToken($refresh_token);
        $this->saveRefreshToken($org_user, $client->getAccessToken()['refresh_token'], $provider);

        $gmail = new \Google_Service_Gmail($client);
        $vacation_setting = new \Google_Service_Gmail_VacationSettings();
        if ($status == 1) {
            $vacation_setting->setEnableAutoReply('true');
            $vacation_setting->setStartTime(strtotime($leaveapp->start_date) * 1000);
            $vacation_setting->setEndTime(strtotime($leaveapp->end_date) * 1000);
            $vacation_setting->setResponseSubject('Out of Office');
            $vacation_setting->setResponseBodyHtml($this->getLeaveMessage($leaveapp));
            $gmail->users_settings->updateVacation('me', $vacation_setting);
        } else {
            $vacation_setting->setEnableAutoReply('false');
            $gmail->users_settings->updateVacation('me', $vacation_setting);
        }
    }

}
