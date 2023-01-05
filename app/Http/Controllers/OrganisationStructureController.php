<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrganisationStructureRequest;
use App\Http\Requests\UpdateOrganisationStructureRequest;
use App\Repositories\OrganisationStructureRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class OrganisationStructureController extends AppBaseController {

    /** @var  OrganisationStructureRepository */
    protected $validationRules = [
        'name' => 'required',
    ];
    private $organisationStructureRepository;

    public function __construct(OrganisationStructureRepository $organisationStructureRepo) {
        $this->middleware('auth');
        $this->middleware('accountActiveCheck');
        $this->organisationStructureRepository = $organisationStructureRepo;
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
     * Display a listing of the OrganisationStructure.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {

        $org_user = \App\Models\OrganisationUser::where(['org_str_id' => $request->session()->get('current_org'), 'user_id' => (\Auth::user()->id)])->first();
        if ($org_user->is_admin == 'yes') {
            $this->organisationStructureRepository->pushCriteria(new RequestCriteria($request));
            //$organisationStructures = $this->organisationStructureRepository->all();
            //Manager can see their own structure
            $user_org = $request->session()->get('current_org');


            return view('organisation_structures.index')
                            ->with(['org_id' => $user_org]);

            //$organisationStructures = $this->organisationStructureRepository;
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new OrganisationStructure.
     *
     * @return Response
     */
    public function create($id, Request $request) {
        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        $tree = $this->getChildTree($realBoss);
        if ((\App\User::checkUserRole(\Auth::user()->id, $org_id)) && (in_array($id, $tree))) {

            $validator = \JsValidator::make($this->validationRules, array(), array(), '#org_create');

            return view('organisation_structures.create')->with(['parent_id' => $id, 'validator' => $validator]);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Store a newly created OrganisationStructure in storage.
     *
     * @param CreateOrganisationStructureRequest $request
     * @param $request->parent_id , mean which node does this new node attach to 
     * 
     * @return Response
     */
    public function store(CreateOrganisationStructureRequest $request) {
        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        if ((\App\User::checkUserRole(\Auth::user()->id, $realBoss))) {
            $parent_id = $request->input('parent_id');
            $name = $request->input('name');

            $account_id = \App\Models\OrganisationStructure::where('id', $org_id)->first()->account_id;
            //apply parent's setting when creation
            $setting_id = \App\Models\OrganisationStructure::where('id', $parent_id)->first()->setting_id;

            $newtree[0] = [
                'name' => $name,
                'setting_id' => $setting_id,
                'account_id' => $account_id,
                'children' => [],
            ];
            $tree = $this->getNewTreeforCreate($newtree, $account_id, $parent_id);
            \App\Models\OrganisationStructure::scoped([ 'account_id' => $account_id])->rebuildTree($tree, false);
            return redirect('organisationStructures')->with('status', 'Orgnazation Structure Updated Successfully!');
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified OrganisationStructure.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        return view('errors.403');
//        $organisationStructure = $this->organisationStructureRepository->findWithoutFail($id);
//
//        if (empty($organisationStructure)) {
//            Flash::error('Organisation Structure not found');
//
//            return redirect(route('organisationStructures.index'));
//        }
//
//        return view('organisation_structures.show')->with('organisationStructure', $organisationStructure);
    }

    /**
     * Show the form for editing the specified OrganisationStructure.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id) {

        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules, array(), array(), '#setting_home');

            //$organisationStructure = $this->organisationStructureRepository->findWithoutFail($id);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $id)->first();
            //Find the setting model with all the relationship attach to it
            $setting_id = $organisationStructure->setting_id;

            $setting = \App\Models\Setting::find($setting_id);

            //$leave_types = \App\Models\LeaveType::where('setting_id', $setting_id)->get();
            if (empty($organisationStructure)) {
                //Flash::error('Organisation Structure not found');
                //return redirect(route('organisationStructures.index'));
                return "Organisation Structure not found";
            }
            //return view('organisation_structures.editindex')->with(['organisationStructure' => $organisationStructure, 'setting' => $setting,
            //            'leave_type' => $leave_types, 'validator' => $validator]);
            return view('organisation_structures.fieldsbasic')->with(['organisationStructure' => $organisationStructure, 'setting' => $setting, 'validator' => $validator, 'view' => 'basic']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified OrganisationStructure in storage.
     *
     * @param  int              $id
     * @param UpdateOrganisationStructureRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOrganisationStructureRequest $request) {
        if (\Request::ajax()) {

            $validator = \JsValidator::make($this->validationRules, array(), array(), '#setting_home');
            $this->organisationStructureRepository->update($request->all(), $id);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $id)->first();
            $setting_id = $organisationStructure->setting_id;
            $setting = \App\Models\Setting::find($setting_id);
            $alert = 'Organisation saved successfully';
            return view('organisation_structures.fieldsbasic')->with(['organisationStructure' => $organisationStructure, 'setting' => $setting, 'validator' => $validator, 'alert' => $alert, 'view' => 'basic']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Remove the specified OrganisationStructure from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request) {
        return view('errors.403');
//        $org_id = $request->session()->get('current_org');
//        $tree = $this->getChildTree($org_id);
//        if ((\App\User::checkUserRole(\Auth::user()->id, $org_id)) && (in_array($id, $tree))) {
//            $organisationStructure = $this->organisationStructureRepository->findWithoutFail($id);
//
//            if (empty($organisationStructure)) {
//                Flash::error('Organisation Structure not found');
//
//                return redirect(route('organisationStructures.index'));
//            }
//
//            $this->organisationStructureRepository->delete($id);
//
//            Flash::success('Organisation Structure deleted successfully.');
//
//            return redirect(route('organisationStructures.index'));
//        } else {
//            return view('errors.403');
//        }
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
                $this->traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1);
            }
        }
    }

    //Tree is the original tree
    //newtree_array is the updated part
    //Test : $tree[0]['children'][0] = $newtree_array[0];
    //traverse and find the right ID and replace that part with newtree_array 
    //for update purpose
    private function traverse2(&$trees, $target) {
        foreach ($trees as $key => $value) {
            if ($trees[$key]['id'] == $target[0]['id']) {
                $trees[$key] = $target[0];
                break;
            }
            if (sizeof($trees[$key]['children']) != 0) {
                $this->traverse2($trees[$key]['children'], $target);
            }
        }
    }

    //for create purpose
    private function traverse3(&$trees, &$target, $parent_id) {
        foreach ($trees as $key => $value) {
            if ($trees[$key]['id'] == $parent_id) {
                $target[0]['setting_id'] = $trees[$key]['setting_id'];
                $trees[$key]['children'][] = $target[0];
                break;
            }
            if (sizeof($trees[$key]['children']) != 0) {
                $this->traverse3($trees[$key]['children'], $target, $parent_id);
            }
        }
    }

    //@return $tree is the final Tree array, use it to build the new tree
    //This is for update purpose
    private function getNewTreeforUpdate($newtree, $account_id) {
        //Get the Top Tree Node , and the children tree structure
        $top_node = \App\Models\OrganisationStructure::where([['account_id', $account_id], ['parent_id', null]])->first();
        $nodes = \App\Models\OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($top_node)->toTree();
        $tree[0] = [
            'id' => $top_node->id,
            'name' => $top_node->name,
            'account_id' => $top_node->account_id,
            'setting_id' => $top_node->setting_id,
            'children' => [],
        ];
        $this->traverse($nodes, $tree[0]['children'], 1);
        $newtree_array = json_decode(json_encode($newtree), true);
        $this->traverse2($tree, $newtree_array);
        return $tree;
    }

    //@return $tree is the final Tree array, use it to build the new tree
    //This is for create purpose
    private function getNewTreeforCreate($newtree, $account_id, $parent_id) {
        //Get the Top Tree Node , and the children tree structure
        $top_node = \App\Models\OrganisationStructure::where([['account_id', $account_id], ['parent_id', null]])->first();
        $nodes = \App\Models\OrganisationStructure::scoped(['account_id', $account_id])->descendantsOf($top_node)->toTree();
        $tree[0] = [
            'id' => $top_node->id,
            'name' => $top_node->name,
            'account_id' => $top_node->account_id,
            'setting_id' => $top_node->setting_id,
            'children' => [],
        ];
        $this->traverse($nodes, $tree[0]['children'], 1);
        $newtree_array = json_decode(json_encode($newtree), true);
        $this->traverse3($tree, $newtree_array, $parent_id);
        return $tree;
    }

    public function dataUpdate(Request $request) {
        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        if (\App\User::checkUserRole(\Auth::user()->id, $realBoss)) {
            $newtree = json_decode($request->input('newtree'));
            $account_id = \App\Models\OrganisationStructure::where('id', $org_id)->first()->account_id;

            //Save the tree with scope--account_id
            $tree = $this->getNewTreeforUpdate($newtree, $account_id);
            \App\Models\OrganisationStructure::scoped([ 'account_id' => $account_id])->rebuildTree($tree, false);

            return redirect('organisationStructures')->with('status', 'Orgnazation Structure Updated Successfully!');
        } else {
            return view('errors.403');
        }
    }

    public function dataDelete($id, Request $request) {
        $org_id = $request->session()->get('current_org');
        $realBoss = \App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);
        $tree = $this->getChildTree($realBoss);
        if ((\App\User::checkUserRole(\Auth::user()->id, $org_id)) && (in_array($id, $tree))) {
            $check = true;
            $sub_tree = $this->getChildTree($id);
            foreach ($sub_tree as $org) {
                $user_find = \App\Models\OrganisationUser::where('org_str_id', $org)->count();
                $invitation = \App\Models\UserRegister::where('org_id', $org)->count();
                if (($user_find > 0) || ($invitation)) {
                    $check = false;
                    break;
                }
            }
            if ($check) {
                \App\Models\OrganisationStructure::where('id', $id)->first()->delete();
                return redirect('organisationStructures')->with('status', 'Organisation Structure deleted successfully.');
            } else {
                Flash::error('You have to terminate all the users and delete all the invitation before deleteing an organsation.');
                return redirect('organisationStructures');
            }
        } else {
            return view('errors.403');
        }
    }

}
