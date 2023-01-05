<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRegisterRequest;
use App\Http\Requests\UpdateUserRegisterRequest;
use App\Repositories\UserRegisterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Invitation;
use Flash;
use Response;

//Only create new invitation is allowed 
//Other action has been hidden
class UserRegisterController extends AppBaseController {

    /** @var  UserRegisterRepository */
    private $userRegisterRepository;
    protected $validationRules_invite = [
        'name' => 'required',
        'email' => 'required|email',
        'org_id' => 'required',
        'is_admin' => 'required',
        'birthday' => 'required|date',
    ];
    protected $validationRules_register = [
        'birthday' => 'required|date',
        'email' => 'required|email',
        'name' => 'required',
        'password' => 'required|confirmed|min:6',
    ];

    public function __construct(UserRegisterRepository $userRegisterRepo) {
        $this->middleware('auth', ['except' => ['RegisterFromToken', 'Register']]);
        $this->userRegisterRepository = $userRegisterRepo;
    }

    /**
     * Display a listing of the UserRegister.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        return view('errors.403');
//        $this->userRegisterRepository->pushCriteria(new RequestCriteria($request));
//        $userRegisters = $this->userRegisterRepository->all();
//
//        return view('user_registers.index')
//                        ->with('userRegisters', $userRegisters);
    }

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

    //Help Class to get the child tree
    //Including the root itself
    //return the $tree
    public function getChildTree($org_id) {
        $account_id = \App\Models\OrganisationStructure::find($org_id)->account_id;
        $tree = [];
        $nodes = \App\Models\OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($org_id);
        $this->traverse($nodes, $tree, 1);
        $array = [];
        foreach ($tree as $item) {
            $array[] = $item['id'];
        }

        if (\App\Models\OrganisationStructure::find($org_id)->parent_id != null) {
            $array[] = intval($org_id);
        }
        return $array;
    }

    /**
     * Show the form for creating a new UserRegister.
     *
     * @return Response
     */
    public function create(Request $request) {
        $org_id = $request->session()->get('current_org');
        if (\App\User::checkUserRole(\Auth::user()->id, $org_id)) {
            $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
            $tree = $this->getChildTree($realBoss);
            $first_org_id = $tree[0];
            $leave_accrual_setting = \App\Models\LeaveAccrualSetting::findSetting($first_org_id);
            $rules = $this->validationRules_invite;
            $messages = [];
            foreach ($leave_accrual_setting as $item) {
                $rules['hours_' . $item->leave_type_id] = "required|numeric";
                $messages['hours_' . $item->leave_type_id . ".required"] = "Hours field is required. ";
                $messages['hours_' . $item->leave_type_id . ".numeric"] = " Hours field must be number. ";
            }
            $validator = \JsValidator::make($rules, $messages, array(), '#form-invite');

            return view('user_registers.create')->with(['tree' => $tree, 'validator' => $validator]);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Store a newly created UserRegister in storage.
     *
     * @param CreateUserRegisterRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRegisterRequest $request) {
        $requestID = $request['org_id'];

        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        if (\App\User::checkUserRole(\Auth::user()->id, $realBoss)) {
            $user = \App\User::where('email', $request['email'])->first();
            $tree = $this->getChildTree($requestID);
            if (isset($user)) {
                $org_user = \App\Models\OrganisationUser::whereIn('org_str_id', $tree)->where('user_id', $user->id)->first();
            }
//$org_user = \App\Models\OrganisationUser::where(['user_id' => $user->id, 'org_str_id' => $request['org_id']])->first();
            if (isset($org_user)) {
                Flash::error('Same User is already existed under this account.');
                return redirect(route('userRegisters.create'));
            }

            $input = $request->all();
            $userRegister = $this->userRegisterRepository->create($input);
            //Send Email to User
            $currentUser = \App\Models\OrganisationUser::where(['org_str_id' => $org_id, 'user_id' => \Auth::user()->id])->first();

            Mail::to($userRegister->email)->send(new Invitation($userRegister, $currentUser));
            Flash::success('User has been invited.');
            return redirect(route('organisationUsers.index'));
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified UserRegister.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        return view('errors.403');
//        $userRegister = $this->userRegisterRepository->findWithoutFail($id);
//
//        if (empty($userRegister)) {
//            Flash::error('User Register not found');
//
//            return redirect(route('userRegisters.index'));
//        }
//
//        return view('user_registers.show')->with('userRegister', $userRegister);
    }

    /**
     * Show the form for editing the specified UserRegister.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id) {
        return view('errors.403');
//        $userRegister = $this->userRegisterRepository->findWithoutFail($id);
//
//        if (empty($userRegister)) {
//            Flash::error('User Register not found');
//
//            return redirect(route('userRegisters.index'));
//        }
//
//        return view('user_registers.edit')->with('userRegister', $userRegister);
    }

    /**
     * Update the specified UserRegister in storage.
     *
     * @param  int              $id
     * @param UpdateUserRegisterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRegisterRequest $request) {
        return view('errors.403');
//        $userRegister = $this->userRegisterRepository->findWithoutFail($id);
//
//        if (empty($userRegister)) {
//            Flash::error('User Register not found');
//
//            return redirect(route('userRegisters.index'));
//        }
//
//        $userRegister = $this->userRegisterRepository->update($request->all(), $id);
//
//        Flash::success('User Register updated successfully.');
//
//        return redirect(route('userRegisters.index'));
    }

    /**
     * Remove the specified UserRegister from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request) {
        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        $tree = $this->getChildTree($realBoss);
        $invitation = \App\Models\UserRegister::find($id);

        if ((\App\User::checkUserRole(\Auth::user()->id, $realBoss)) && (in_array($invitation->org_id, $tree))) {
            $userRegister = $this->userRegisterRepository->findWithoutFail($id);
            if (empty($userRegister)) {
                Flash::error('User Register not found');
                return redirect(route('userRegisters.index'));
            }
            $this->userRegisterRepository->delete($id);
            Flash::success(' Invitation deleted successfully.');
            return redirect(route('organisationUsers.index'));
        } else {
            return view('errors.403');
        }
    }

    public function registerProcedure($user_register, $newuser, $birthday) {
        $user_org = \App\Models\OrganisationUser::create([
                    'org_str_id' => $user_register->org_id,
                    'user_id' => $newuser->id,
                    'is_admin' => $user_register->is_admin,
                    'phone' => isset($user_register->phone) ? $user_register->phone : "",
                    'address' => $user_register->address,
                    'birthday' => $birthday,
                    'xero_id' => isset($user_register->xero_id) ? $user_register->xero_id : "",
        ]);

        $register_capacity = \App\Models\RegisterCapacity::where('register_id', $user_register->id)->get();
        foreach ($register_capacity as $item) {
            \App\Models\LeaveCapacity::create([
                'user_id' => $newuser->id,
                'org_id' => $user_register->org_id,
                'leave_type_id' => $item->leave_type_id,
                'capacity' => $item->capacity,
                'last_update_date' => date("Y-m-d H:i:s", time()),
            ]);
            $item->delete();
        }
        $user_register->delete();
        return $user_org;
    }

    public function RegisterFromToken($token,Request $request) {
 
        if (isset($token) && ($token != null)) {
            $validator = \JsValidator::make($this->validationRules_register, array(), array(), '#form-register');
            $register_token = \App\Models\UserRegister::where('token', $token)->first();
            if (isset($register_token)) {
                $user = \App\User::where('email', $register_token->email)->first();
                $birthday = date_create($register_token->birthday);
                $formatted = date_format($birthday, 'Y-m-d');
                if (!isset($user)) {
                    $register_token->birthday = $formatted;
                    //$_SESSION['token_id'] = $register_token->id;echo 's2'.$_SESSION['token_id'];
                    $request->session()->put('token_id', $register_token->id);
                    return view('user_registers.register')->with(['userRegister' => $register_token, 'validator' => $validator]);
                } else {
                    $user->last_visit_org = $register_token->org_id;
                    $user->save();
                    $newuser = $user;
                    $user_org = $this->registerProcedure($register_token, $newuser, $formatted);
                    \Session::set('current_org', $user_org->org_str_id);
                    \Auth::login($newuser, true);
                    return redirect('/');
                }
            } else {
                return view('errors.404');
            }
        } else {
            return view('errors.404');
        }
    }

    public function Register(CreateUserRegisterRequest $request) {
        if ($request['password'] != null) {
            //$user_register = \App\Models\UserRegister::find($_SESSION['token_id']);
            $user_register = \App\Models\UserRegister::find($request->session()->get('token_id'));
            $user = \App\User::where('email', $request['email'])->first();
            if (!isset($user)) {
                $newuser = \App\User::create([
                            'name' => $request['name'],
                            'email' => $request['email'],
                            'password' => bcrypt($request['password']),
                            'last_visit_org' => $user_register->org_id,
                ]);
            } else {
                $user->last_visit_org = $user_register->org_id;
                $user->save();
                $newuser = $user;
            }
            $user_org = $this->registerProcedure($user_register, $newuser, $request['birthday']);

            $request->session()->set('current_org', $user_org->org_str_id);
            \Auth::login($newuser, true);
            return redirect('/');
        } else {
            return view('errors.404');
        }
    }

    public function ReturnList(Request $request) {
        if (\Request::ajax()) {
            $query_org = $request['org_id'];
            $leave_accrual_setting = \App\Models\LeaveAccrualSetting::findSetting($query_org);
            $rules = [];
            $messages = [];
            foreach ($leave_accrual_setting as $item) {
                $rules['hours_' . $item->leave_type_id] = "required|numeric";
                $messages['hours_' . $item->leave_type_id . ".required"] = "Hours field is required. ";
                $messages['hours_' . $item->leave_type_id . ".numeric"] = " Hours field must be number. ";
            }
            $validator = \JsValidator::make($rules, $messages, array(), '#form-invite');
            return view('user_registers.partical_register')->with(['leave_accrual_setting' => $leave_accrual_setting, 'validator' => $validator]);
        } else {
            return view('errors.403');
        }
    }

}
