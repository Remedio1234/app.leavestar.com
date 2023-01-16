<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Repositories\OrganisationUserRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use DB;

class ReportsController extends AppBaseController {

    /** @var  OrganisationUserRepository */
    private $organisationUserRepository;

    public function __construct(OrganisationUserRepository $organisationUserRepo) {

        $this->middleware('auth');
        $this->middleware('accountActiveCheck');
        $this->organisationUserRepository = $organisationUserRepo;
    }

    public function index(Request $request) {
        //$this->organisationUserRepository->pushCriteria(new RequestCriteria($request));
        //$organisationUsers = $this->organisationUserRepository->all();
        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);

        if (\App\User::checkUserRole(\Auth::user()->id, $realBoss)) {
            $tree = $this->getChildTree($realBoss);

            $organisationUsers = \App\Models\OrganisationUser::Join('organisation_structure', 'organisation_structure.id', '=', 'organisation_user.org_str_id')
                            ->whereIn('org_str_id', $tree)->select(['organisation_user.*', 'organisation_structure.id as KK'])->paginate(20);

            $userRegisters = \App\Models\UserRegister::whereIn('org_id', $tree)->paginate(20);
            //$organisationUsers = $this->organisationUserRepository->paginate(20);
            return view('reports.attendance.index')
                            ->with(['organisationUsers' => $organisationUsers, 'userRegisters' => $userRegisters, 'tree' => []]);
        } else {
            return view('errors.403');
        }
    }

    //get children tree
    private function getChildTree($org_id) {
        $account_id = \App\Models\OrganisationStructure::find($org_id)->account_id;
        $tree = [];
        $nodes = \App\Models\OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($org_id);
        $this->traverse($nodes, $tree, 1);
        $array = [];
        foreach ($tree as $item) {
            $array[] = $item['id'];
        }
        $array[] = intval($org_id);
        return $array;
    }

    /**
     * Display a listing of the OrganisationUser.
     *
     * @param Request $request
     * @return Response
     */
    //Get the Top Tree Node , and the children tree structure
    private function traverse($categories, &$tree, $level) {
        foreach ($categories as $category) {
            array_push($tree, array(
                'id' => $category->id,
                'name' => $category->name,
                'account_id' => $category->account_id,
                'setting_id' => $category->setting_id,
                'children' => [],
            ));
            if ($category->children->count() > 0) {
                $this->traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1);
            }
        }
    }

    public function getAttendanceReports(Request $request){
        if($request->ajax()){
            $data = $this->getAttendanceData($request->from, $request->to);
            return view('reports.attendance.table')
                ->with("data", $data)
                ->render();
        }
        return view('errors.403');
    }

    private function getAttendanceData($from, $to){
        $root_org_id = \App\Models\OrganisationStructure::findRootOrg(\Session::get('current_org'));
        $attendance = DB::select("SELECT u.id as user_id, cap.id, u.name as emp_name, lt.name as leave_name, 
        cap.capacity as balance, SUM(la.leave_credit) as taken 
        FROM leave_application la 
        INNER JOIN users u ON la.user_id = u.id 
        INNER JOIN leave_type lt ON lt.id = la.leave_type_id 
        INNER JOIN (SELECT id, user_id,org_id,leave_type_id,capacity FROM leave_capacity 
        WHERE org_id = '".$root_org_id."') cap ON (la.leave_type_id = cap.leave_type_id AND la.user_id = cap.user_id AND la.org_id = cap.org_id) 
        WHERE la.org_id = '".$root_org_id."' AND (date(la.start_date) >= '".date("Y-m-d", strtotime($from))."' AND date(la.end_date) <= '".date("Y-m-d", strtotime($to))."')
        GROUP BY u.id, la.leave_type_id, cap.leave_type_id 
        ORDER BY `emp_name` ASC");
        return $attendance;
    }

    public function updateLeaveTaken(Request $request){
        if($request->ajax()){
            $attendance = DB::table('leave_application')
            ->where('leave_credit', 0)
            ->update(['leave_credit' => DB::raw("TIMESTAMPDIFF(SECOND,start_date,end_date)")]);
            
            if($attendance)
                echo json_encode(["response" => "successfully updated"]);
            else 
                echo json_encode(["response" => "no updates"]);
        }
    }
}