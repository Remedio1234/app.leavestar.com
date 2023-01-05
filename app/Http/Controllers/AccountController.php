<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Repositories\AccountRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Config;

class AccountController extends AppBaseController {

    /** @var  AccountRepository */
    private $accountRepository;
    //private $stripe_sk = "sk_test_KhVOTxZs04bja2UGbidgcg1b";
    private $plan_id = "plan_EpQ6Eq25SfimEe";//"10203040";
    private $weekly_token = "iaee7svQSWwEIBPyu4jteigVNi7A2nOfYftcTgDgaYfObHo3ya3GXceTOyVH";
    protected $validationRules_signup = [
        'acc_name' => 'required',
        'email' => 'required|email|unique:users,email',
        'name' => 'required',
        'birthday' => 'required|date',
        'password' => 'required|confirmed|min:6',
    ];

    public function __construct(AccountRepository $accountRepo) {
        $this->middleware('auth', ['except' => ['Signup', 'WeeklyCheck', 'store']]);
        $this->accountRepository = $accountRepo;
    }

    /**
     * Display a listing of the Account.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        if (\Auth::user()->id == 1) {
            $this->accountRepository->pushCriteria(new RequestCriteria($request));
            //$accounts = $this->accountRepository->all();

            $accounts = $this->accountRepository->paginate(20);
            return view('accounts.index')
                            ->with('accounts', $accounts);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new Account.
     *
     * @return Response
     */
    public function create(Request $request) {
        return view('errors.403');

//        if (\Auth::user()->id == 1) {
//            return view('accounts.create');
//        } else {
//            return view('errors.403');
//        }
    }

    /**
     * Store a newly created Account in storage.
     *
     * @param CreateAccountRequest $request
     *
     * @return Response
     */
//    public function store(CreateAccountRequest $request) {
//        if (\Auth::user()->id == 1) {
//            $input = $request->all();
//            $account = $this->accountRepository->create($input);
//            \App\Models\OrganisationStructure::create([
//                'name' => $account->name,
//                'account_id' => $account->id,
//                'setting_id' => $account->setting_id,
//                'org_id' => '0',
//                'level' => '0',
//                'children' => [],
//            ]);
//            Flash::success('Account saved successfully.');
//            return redirect(route('accounts.index'));
//        } else {
//            return view('errors.403');
//        }
//    }

    /**
     * Display the specified Account.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show(Request $request, $id) {
        if (\Auth::user()->id == 1) {
            $org_id = \App\Models\OrganisationStructure::where(['account_id' => $id, 'parent_id' => null])->first()->id;
            $request->session()->put('current_org', $org_id);
            return redirect()->action('OrganisationStructureController@index');
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for editing the specified Account.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id) {
        if (\Auth::user()->id == 1) {
            $account = $this->accountRepository->findWithoutFail($id);

            if (empty($account)) {
                Flash::error('Account not found');

                return redirect(route('accounts.index'));
            }

            return view('accounts.edit')->with('account', $account);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified Account in storage.
     *
     * @param  int              $id
     * @param UpdateAccountRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountRequest $request) {
        if (\Auth::user()->id == 1) {
            $account = $this->accountRepository->findWithoutFail($id);

            if (empty($account)) {
                Flash::error('Account not found');

                return redirect(route('accounts.index'));
            }

            $account = $this->accountRepository->update($request->all(), $id);

            Flash::success('Account updated successfully.');

            return redirect(route('accounts.index'));
        } else {
            return view('errors.403');
        }
    }

    /**
     * Remove the specified Account from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id) {
        if (\Auth::user()->id == 1) {
            $account = $this->accountRepository->findWithoutFail($id);

            if (empty($account)) {
                Flash::error('Account not found');

                return redirect(route('accounts.index'));
            }

            $this->accountRepository->delete($id);

            Flash::success('Account deleted successfully.');

            return redirect(route('accounts.index'));
        } else {
            return view('errors.403');
        }
    }

    public function Signup() {
        $validator = \JsValidator::make($this->validationRules_signup, array(), array(), '#account-signup');
        return view('accounts.signup')->with('validator', $validator);
    }

    public function store(CreateAccountRequest $request) {
        $email = $request['email'];
        $stripe_token = $request['strip_token'];
        $acc_name = $request['acc_name'];
        $birthday = $request['birthday'];
        $password = $request['password'];
        $name = $request['name'];

        //Create Strip Subscription First
        \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_sk'));
        $customer = \Stripe\Customer::create(array(
                    "description" => "Customer for " . $email,
                    "source" => $stripe_token  // obtained with Stripe.js
        ));

        $subscription = \Stripe\Subscription::create(array(
                    "customer" => $customer->id,
                    "plan" => $this->plan_id,
                    "quantity" => 1
        ));
        /* Create Acccount
         * Create org 
         * Create org_user
         * Create user
         */
        $new_account = \App\Models\Account::create([
                    'name' => $acc_name,
                    'stripe_client_token' => $customer->id,
                    'stripe_sub_token' => $subscription->id,
        ]);

        $new_org = \App\Models\OrganisationStructure::create([
                    'name' => $acc_name,
                    'account_id' => $new_account->id,
                    'setting_id' => 1,
                    'parent_id' => null,
                    'children' => [],
        ]);

        $user = \App\User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt($password),
                    'last_visit_org' => $new_org->id
        ]);

        $org_user = \App\Models\OrganisationUser::create([
                    'org_str_id' => $new_org->id,
                    'user_id' => $user->id,
                    'is_admin' => 'yes',
                    'birthday' => $birthday,
        ]);

        $request->session()->set('current_org', $new_org->id);
        \Auth::login($user, true);
        return redirect('/');
    }

    private function getChildTree($org_id) {
        $account_id = \App\Models\OrganisationStructure::find($org_id)->account_id;
        $tree = [];
        $nodes = \App\Models\OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($org_id);
        $this->traverse($nodes, $tree, 1);
        $array[0] = $org_id;
        foreach ($tree as $item) {
            $array[] = $item['id'];
        }
        return $array;
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
            if ($category->children->count() != 0) {
                $this->traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1, $category);
            }
        }
    }

    public function WeeklyCheck($token) {
        if ($token == $this->weekly_token) {
            $accounts = \App\Models\Account::where('status', 1)->get();
            \Stripe\Stripe::setApiKey(Config::get('stripe.stripe_sk'));
            var_dump(Config::get('stripe.stripe_sk'));
            foreach ($accounts as $item) {
                $org_top = \App\Models\OrganisationStructure::where([
                            'account_id' => $item->id,
                            'parent_id' => null
                        ])->first();

                $trees = $this->getChildTree($org_top->id);
                $num_of_users = \App\Models\OrganisationUser::whereIn('org_str_id', $trees)->count();
                if (isset($item->stripe_sub_token)) {
                    $subscription = \Stripe\Subscription::retrieve($item->stripe_sub_token);
                    $subscription->quantity = $num_of_users;
                    $subscription->save();
                }
            }
        } else {
            return view('errors.403');
        }
    }

}
