<?php

namespace App\Http\Middleware;

use Closure;
use Flash;

class AccountActiveCheck {

    public function AccountActiveCheckMessage() {
        return "The organisation you have assigned to is no longer active, please contact your account manager for more infomation.";
    }

    public function handle($request, Closure $next) {
        $org_id = \Session::get('current_org');
        $accountID = \App\Models\OrganisationStructure::find($org_id)->account_id;
        $statusOfAccount = \App\Models\Account::find($accountID)->status;
        if ($statusOfAccount != 1) {
            Flash::error($this->AccountActiveCheckMessage());
            return redirect('/home');
        }


        return $next($request);
    }

}
