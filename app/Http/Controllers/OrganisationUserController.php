<?php

namespace App\Http\Controllers;

require app_path() . '/lib/XeroOAuth.php';

use App\Http\Requests\CreateOrganisationUserRequest;
use App\Http\Requests\UpdateOrganisationUserRequest;
use App\Repositories\OrganisationUserRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class OrganisationUserController extends AppBaseController {

    /** @var  OrganisationUserRepository */
    private $organisationUserRepository;

    public function __construct(OrganisationUserRepository $organisationUserRepo) {

        $this->middleware('auth');
        $this->middleware('accountActiveCheck');
        $this->organisationUserRepository = $organisationUserRepo;
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

    //get children tree
    private function getChildTree2($org_id) {
        $account_id = \App\Models\OrganisationStructure::find($org_id)->account_id;
        $tree = [];
        $nodes = \App\Models\OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($org_id);
        $this->traverse($nodes, $tree, 1);
        $array = [];
        foreach ($tree as $item) {
            $array[$item['id']] = $item['name'];
        }
        $org = \App\Models\OrganisationStructure::find($org_id);
        if ($org->parent_id != null) {
            $array[intval($org_id)] = \App\Models\OrganisationStructure::find($org_id)->name;
        }
        return $array;
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
            return view('organisation_users.index')
                            ->with(['organisationUsers' => $organisationUsers, 'userRegisters' => $userRegisters, 'tree' => $tree]);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new OrganisationUser.
     *
     * @return Response
     */
    public function create() {
        return view('errors.403');
        // return view('organisation_users.create');
    }

    /**
     * Store a newly created OrganisationUser in storage.
     *
     * @param CreateOrganisationUserRequest $request
     *
     * @return Response
     */
    public function store(CreateOrganisationUserRequest $request) {
        return view('errors.403');
//        $input = $request->all();
//
//        $organisationUser = $this->organisationUserRepository->create($input);
//
//        Flash::success('Organisation User saved successfully.');
//
//        return redirect(route('organisationUsers.index'));
    }

    /**
     * Display the specified OrganisationUser.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id, Request $request) {

        $org_id = $request->session()->get('current_org');
        $tree = $this->getChildTree($org_id);
        $org_user = \App\Models\OrganisationUser::find($id);
        if ((\App\User::checkUserRole(\Auth::user()->id, $org_id)) && (in_array($org_user->org_str_id, $tree))) {
            $organisationUser = $this->organisationUserRepository->findWithoutFail($id);

            if (empty($organisationUser)) {
                Flash::error('Organisation User not found');

                return redirect(route('organisationUsers.index'));
            }

            return view('organisation_users.show')->with('organisationUser', $organisationUser);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for editing the specified OrganisationUser.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, Request $request) {
        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        $tree = $this->getChildTree($realBoss);
        $org_user = \App\Models\OrganisationUser::find($id);
        if ((\App\User::checkUserRole(\Auth::user()->id, $org_id)) && (in_array($org_user->org_str_id, $tree))) {
            $organisationUser = $this->organisationUserRepository->findWithoutFail($id);

            return view('organisation_users.edit')->with(['organisationUser' => $organisationUser, 'org_list' => $this->getChildTree2($realBoss)]);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified OrganisationUser in storage.
     *
     * @param  int              $id
     * @param UpdateOrganisationUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOrganisationUserRequest $request) {
        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        $tree = $this->getChildTree($realBoss);
        $org_user = \App\Models\OrganisationUser::find($id);
        if ((\App\User::checkUserRole(\Auth::user()->id, $org_id)) && (in_array($org_user->org_str_id, $tree))) {
            $organisationUser = $this->organisationUserRepository->findWithoutFail($id);

            if (empty($organisationUser)) {
                Flash::error('Organisation User not found');

                return redirect(route('organisationUsers.index'));
            }
            $request['tree'] = $tree;
            $organisationUser = $this->organisationUserRepository->update($request->all(), $id);

            Flash::success('Organisation User updated successfully.');

            return redirect(route('organisationUsers.index'));
        } else {
            return view('errors.403');
        }
    }

    /**
     * Remove the specified OrganisationUser from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request) {
        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        $tree = $this->getChildTree($realBoss);
        $org_user = \App\Models\OrganisationUser::find($id);
        if ((\App\User::checkUserRole(\Auth::user()->id, $org_id)) && (in_array($org_user->org_str_id, $tree))) {
            $organisationUser = $this->organisationUserRepository->findWithoutFail($id);

            if (empty($organisationUser)) {
                Flash::error('Organisation User not found');

                return redirect(route('organisationUsers.index'));
            }
            if ($organisationUser->user_id == \Auth::user()->id) {
                Flash::error('Can not delete current User.');

                return redirect(route('organisationUsers.index'));
            }

            $this->organisationUserRepository->delete($id);

            Flash::success('Organisation User deleted successfully.');

            return redirect(route('organisationUsers.index'));
        } else {
            return view('errors.403');
        }
    }

    public function editUser(Request $request) {
        $validationRules = [
            'birthday' => 'required|date',
            'image' => 'max:1024|mimes:jpeg,bmp,png,jpg',
        ];
        $validator = \JsValidator::make($validationRules, array(), array(), '#org_user_update');
        $user_id = \Auth::user()->id;
        $org_id = $request->session()->get('current_org');
        $org_user = \App\Models\OrganisationUser::where([
                    'user_id' => $user_id,
                    'org_str_id' => $org_id
                ])->first();
        $birthday = date_create($org_user->birthday);
        $formatted = date_format($birthday, 'Y-m-d');
        $startDate = date_create($org_user->start_working_date);
        $formatted2 = date_format($startDate, 'Y-m-d');
        $org_user->birthday = $formatted;
        $org_user->start_working_date = $formatted2;
        return view('organisation_users.basic')->with(['organisationUser' => $org_user, 'view' => 'basic', 'validator' => $validator]);
    }

    public function updateUser(Request $request) {
        $user_id = \Auth::user()->id;
        $org_id = $request->session()->get('current_org');

        if (isset($request['image'])) {
            $path = $request->file('image')->storePublicly('image');
            $user = \Auth::user();
            if ($user->profile_pic != null) {
                \Storage::delete($user->profile_pic);
            }
            $user->profile_pic = $path;
            $user->save();
        }

        //Set Email notification field
        $user = \Auth::user();
        $user->receiveEmailNotification = (isset($request['emailNotification'])) ? 1 : 0;
        $user->save();

        $org_user = \App\Models\OrganisationUser::where([
                    'user_id' => $user_id,
                    'org_str_id' => $org_id
                ])->first();
        $org_user->phone = $request['phone'];
        $org_user->birthday = $request['birthday'];
        $org_user->start_working_date = $request['start_working_date'];
        $org_user->birthdayFeedColor = $request['birthdayFeedColor'];
        $org_user->anniversariesFeedColor = $request['anniversariesFeedColor'];
        $org_user->birthdayTextColor = $request['birthdayTextColor'];
        $org_user->anniversaryTextColor = $request['anniversaryTextColor'];
        $org_user->save();
        return redirect(action('OrganisationUserController@editUser'))->with('status', 'User Information Updated Successfully.');
    }

    public function editEmail(Request $request) {

        return view('organisation_users.email_setting')->with([ 'view' => 'email']);
    }

    public function deleteProfile(Request $request) {
        $user = \Auth::user();
        \Storage::delete($user->profile_pic);
        $user->profile_pic = null;
        $user->save();

        return redirect(action('OrganisationUserController@editUser'))->with('status', 'User Profile Picture Deleted Successfully.');
    }

    public function getEmail(Request $request) {
        $exceptionDepartmentId = $request['exception'];
        $org_id = \Session::get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        $tree = $this->getChildTree($realBoss);
        $users = \App\Models\OrganisationUser::where('org_str_id', '<>', $exceptionDepartmentId)->whereIn('org_str_id', $tree)->groupBy('user_id')->get(['user_id']);
        $returnArray = [];
        foreach ($users as $user) {
            $userEntity = \App\User::find($user->user_id);
            $returnArray[] = array(
                'email' => $userEntity->email,
                'name' => $userEntity->name
            );
        };
        return json_encode($returnArray);
    }

    public function removeXerotoken($id) {
        $orgUser = \App\Models\OrganisationUser::find($id);
        $orgUser->update(['xero_id' => '', 'xero_name' => '']);

        Flash::success('Xero Match deleted successfully.');

        return redirect(route('organisationUsers.index'));
    }

    public function enableTour() {
        $user = \App\User::find(\Auth::user()->id);
        $user->tourGuide = 0;
        $user->save();
        return redirect('/');
    }

}
