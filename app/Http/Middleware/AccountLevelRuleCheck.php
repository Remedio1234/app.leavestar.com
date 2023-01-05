<?php

namespace App\Http\Middleware;

use Closure;
use Flash;

class AccountLevelRuleCheck {

    public function AccountCheckMessage() {
        return "You have to assign yourself to at least one Organisation.";
    }

    public function findOtherAssignOrg($user_id, $exceptionOrgId) {
        $searchResult = \App\Models\OrganisationUser::where('user_id', $user_id)
                        ->where('org_str_id', '<>', $exceptionOrgId)->first();
        if (isset($searchResult)) {
            return true;
        } else {
            return false;
        }
    }

    public function handle($request, Closure $next) {

        $org_id = \Session::get('current_org');
        $user_id = \Auth::user()->id;
        $user_last_org = \App\User::find($user_id)->last_visit_org;
        $org_id = isset($org_id) ? $org_id : $user_last_org;
        $user_org = \App\Models\OrganisationStructure::where([
                    'id' => $org_id,
                ])->first();
        if ($user_org->parent_id == null) {
            $result = $this->findOtherAssignOrg($user_id, $org_id);
            if (!$result) {
                Flash::error($this->AccountCheckMessage());
                return redirect('/home');
            }
        }

        return $next($request);
    }

}
